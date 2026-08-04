<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDO;

class BackupService
{
    /**
     * Directory where backups are stored (relative to storage/app).
     */
    public const BACKUP_DISK = 'local';
    public const BACKUP_DIR = 'backups';

    /**
     * Allowed backup types.
     */
    public const TYPES = ['database', 'files', 'full'];

    /**
     * Create a backup.
     *
     * @param string $type database|files|full
     * @param int|null $userId
     * @return array{filename: string, path: string, size: int, type: string, tables: int, rows: int, created_at: string}
     */
    public function create(string $type = 'database', ?int $userId = null): array
    {
        $type = in_array($type, self::TYPES, true) ? $type : 'database';
        $timestamp = now()->format('Y-m-d_His');

        $results = [];

        if ($type === 'database' || $type === 'full') {
            $results = $this->dumpDatabase(self::BACKUP_DIR . "/database_{$timestamp}.sql");
        }

        if ($type === 'files') {
            $results = array_merge($results, $this->zipFiles(self::BACKUP_DIR . "/files_{$timestamp}.zip"));
        }

        if ($type === 'full') {
            $sqlRelative = self::BACKUP_DIR . "/database_{$timestamp}.sql";
            $results = array_merge($results, $this->zipFull($sqlRelative, self::BACKUP_DIR . "/full_{$timestamp}.zip"));
            Storage::disk(self::BACKUP_DISK)->delete($sqlRelative);
        }

        $backupFile = $type === 'full' ? "full_{$timestamp}" : ($type === 'files' ? "files_{$timestamp}" : "database_{$timestamp}");
        $meta = [
            'filename' => "{$backupFile}.{$this->extensionFor($type)}",
            'type' => $type,
            'connection' => DB::getDefaultConnection(),
            'database' => $this->databaseName(),
            'tables' => $results['tables'] ?? 0,
            'rows' => $results['rows'] ?? 0,
            'size' => $results['size'] ?? 0,
            'created_by' => $userId,
            'created_at' => now()->toDateTimeString(),
            'version' => app()->version(),
        ];

        Storage::disk(self::BACKUP_DISK)->put(
            self::BACKUP_DIR . "/{$meta['filename']}.meta.json",
            json_encode($meta, JSON_PRETTY_PRINT)
        );

        $this->prune(config('backup.retention', 10));

        return $meta;
    }

    /**
     * List all backups sorted newest first.
     */
    public function list(): \Illuminate\Support\Collection
    {
        return collect($this->allMeta())
            ->filter(fn ($backup) => !empty($backup['exists']))
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Aggregate storage / backup summary.
     */
    public function summary(): array
    {
        $backups = $this->list();

        $totalSize = $backups->sum('size');

        $dir = storage_path('app/' . self::BACKUP_DIR);

        return [
            'count' => $backups->count(),
            'total_size' => $totalSize,
            'human_total_size' => $this->humanSize($totalSize),
            'last_backup' => $backups->first(),
            'database' => $this->databaseName(),
            'connection' => DB::getDefaultConnection(),
            'storage_path' => $dir,
            'disk_free' => function_exists('disk_free_space') ? disk_free_space(storage_path()) : 0,
            'disk_total' => function_exists('disk_total_space') ? disk_total_space(storage_path()) : 0,
            'human_disk_used' => $this->humanSize(function_exists('disk_total_space') ? (disk_total_space(storage_path()) - disk_free_space(storage_path())) : 0),
            'human_disk_total' => $this->humanSize(function_exists('disk_total_space') ? disk_total_space(storage_path()) : 0),
            'retention' => config('backup.retention', 10),
        ];
    }

    /**
     * Download (return absolute path) or abort.
     */
    public function pathFor(string $filename): ?string
    {
        if ($this->isSafeFilename($filename) && Storage::disk(self::BACKUP_DISK)->exists(self::BACKUP_DIR . '/' . $filename)) {
            return Storage::disk(self::BACKUP_DISK)->path(self::BACKUP_DIR . '/' . $filename);
        }

        return null;
    }

    /**
     * Delete a backup (file + meta).
     */
    public function delete(string $filename): bool
    {
        if (!$this->isSafeFilename($filename)) {
            return false;
        }

        Storage::disk(self::BACKUP_DISK)->delete(self::BACKUP_DIR . '/' . $filename);
        Storage::disk(self::BACKUP_DISK)->delete(self::BACKUP_DIR . '/' . $filename . '.meta.json');

        return true;
    }

    /**
     * Restore a database or files backup from the backups directory.
     */
    public function restore(string $filename): array
    {
        $path = $this->pathFor($filename);
        if (!$path) {
            throw new \RuntimeException('Backup file not found.');
        }

        $meta = $this->metaFor($filename);
        $type = $meta['type'] ?? $this->guessType($filename);

        return match ($type) {
            'full' => $this->restoreFull($path),
            'files' => $this->restoreFiles($path),
            default => $this->restoreDatabase($path),
        };
    }

    /**
     * Upload an external SQL / zip file into the backups directory.
     */
    public function upload(string $originalName, string $tmpPath): array
    {
        $originalName = basename($originalName);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['sql', 'zip'], true)) {
            throw new \RuntimeException('Only .sql and .zip files are supported.');
        }

        $timestamp = now()->format('Y-m-d_His');
        $storedName = $ext === 'zip'
            ? self::BACKUP_DIR . "/uploaded_files_{$timestamp}.zip"
            : self::BACKUP_DIR . "/uploaded_{$timestamp}.sql";

        $size = filesize($tmpPath);

        Storage::disk('local')->put(
            $storedName,
            file_get_contents($tmpPath)
        );

        $type = $ext === 'zip' ? 'files' : 'database';

        $meta = [
            'filename' => basename($storedName),
            'type' => $type,
            'connection' => DB::getDefaultConnection(),
            'database' => $this->databaseName(),
            'tables' => 0,
            'rows' => 0,
            'size' => $size,
            'created_by' => auth()->id(),
            'created_at' => now()->toDateTimeString(),
            'version' => app()->version(),
            'uploaded' => true,
        ];

        Storage::disk('local')->put(self::BACKUP_DIR . "/{$meta['filename']}.meta.json", json_encode($meta, JSON_PRETTY_PRINT));

        return $meta;
    }

    /**
     * Purge backups beyond the retention limit.
     */
    public function prune(int $keep = 10): int
    {
        $backups = $this->list();
        $removed = 0;

        if ($backups->count() <= $keep) {
            return 0;
        }

        foreach ($backups->slice($keep) as $backup) {
            if ($this->delete($backup['filename'])) {
                $removed++;
            }
        }

        if ($removed > 0) {
            Log::info("Backup pruning removed {$removed} old backup(s).");
        }

        return $removed;
    }

    /**
     * Pure-PHP MySQL dump. Writes structure + batched data inserts.
     */
    protected function dumpDatabase(string $destination): array
    {
        $pdo = DB::connection()->getPdo();
        $dbName = $this->databaseName();

        $tables = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"')->fetchAll(PDO::FETCH_NUM);
        $tableNames = array_column($tables, 0);

        $sql = "-- LegalHR Database Backup\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- Database: {$dbName}\n";
        $sql .= "-- Connection: " . DB::getDefaultConnection() . "\n";
        $sql .= "-- Laravel: " . app()->version() . "\n";
        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "SET SQL_MODE = '';\n\n";

        $totalRows = 0;

        foreach ($tableNames as $table) {
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $sql .= $create['Create Table'] . ";\n\n";

            $select = $pdo->query("SELECT * FROM `{$table}`");

            $batch = [];
            $count = 0;

            while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = $pdo->quote((string) $value);
                    }
                }
                $batch[] = '(' . implode(', ', $values) . ')';
                $count++;

                if (count($batch) >= 200) {
                    $sql .= "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n";
                    $batch = [];
                }
            }

            if ($batch) {
                $sql .= "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $batch) . ";\n";
            }

            $sql .= "\n";
            $totalRows += $count;
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $sql .= "-- End of backup\n";

        Storage::disk(self::BACKUP_DISK)->put($destination, $sql);

        return [
            'tables' => count($tableNames),
            'rows' => $totalRows,
            'size' => Storage::disk(self::BACKUP_DISK)->size($destination),
        ];
    }

    /**
     * Zip the user-uploaded storage directories.
     */
    protected function zipFiles(string $destination): array
    {
        $zip = $this->openArchive($destination);

        $root = storage_path('app');
        $directories = ['public', 'private'];

        foreach ($directories as $directory) {
            $source = $root . DIRECTORY_SEPARATOR . $directory;
            if (is_dir($source)) {
                $this->addDirectoryToZip($zip, $source, $directory);
            }
        }

        $zip->close();

        return [
            'size' => Storage::disk(self::BACKUP_DISK)->size($destination),
        ];
    }

    /**
     * Create a single self-contained full backup: database dump + storage files.
     */
    protected function zipFull(string $sqlRelativePath, string $zipDestination): array
    {
        $zip = $this->openArchive($zipDestination);

        $zip->addFile(
            Storage::disk(self::BACKUP_DISK)->path($sqlRelativePath),
            'database.sql'
        );

        $root = storage_path('app');
        foreach (['public', 'private'] as $directory) {
            $source = $root . DIRECTORY_SEPARATOR . $directory;
            if (is_dir($source)) {
                $this->addDirectoryToZip($zip, $source, $directory);
            }
        }

        $zip->close();

        return [
            'size' => Storage::disk(self::BACKUP_DISK)->size($zipDestination),
        ];
    }

    protected function openArchive(string $destination): \ZipArchive
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('The Zip extension is required for file backups.');
        }

        $zip = new \ZipArchive();
        $absolutePath = Storage::disk(self::BACKUP_DISK)->path($destination);
        @mkdir(dirname($absolutePath), 0775, true);

        if ($zip->open($absolutePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create zip archive.');
        }

        return $zip;
    }

    protected function addDirectoryToZip(\ZipArchive $zip, string $source, string $base): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $realPath = $file->getRealPath();
            $relative = $base . '/' . str_replace('\\', '/', substr($realPath, strlen($source) + 1));

            if (str_starts_with($relative, 'backups/')) {
                continue;
            }

            $zip->addFile($realPath, $relative);
        }
    }

    protected function restoreDatabase(string $path): array
    {
        $pdo = DB::connection()->getPdo();
        $sql = file_get_contents($path);

        $statements = $this->splitStatements($sql);
        $executed = 0;

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($statements as $statement) {
            $pdo->exec($statement);
            $executed++;
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        return [
            'statements' => $executed,
            'size' => strlen($sql),
            'type' => 'database',
        ];
    }

    protected function restoreFiles(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('The Zip extension is required for file restore.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Unable to open backup archive.');
        }

        $root = storage_path('app');
        $extracted = 0;
        $dbStatements = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $entry = ltrim($entry, '/');

            if ($entry === 'database.sql') {
                $contents = $zip->getFromIndex($i);
                if ($contents !== false) {
                    $pdo = DB::connection()->getPdo();
                    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
                    foreach ($this->splitStatements($contents) as $statement) {
                        $pdo->exec($statement);
                        $dbStatements++;
                    }
                    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
                }
                continue;
            }

            $segments = explode('/', $entry);
            if (count($segments) < 2 || !in_array($segments[0], ['public', 'private'], true)) {
                continue;
            }

            $targetDir = $root . DIRECTORY_SEPARATOR . $segments[0];
            @mkdir($targetDir, 0775, true);

            if (str_ends_with($entry, '/')) {
                continue;
            }

            $target = $targetDir . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($segments, 1));
            @mkdir(dirname($target), 0775, true);

            $contents = $zip->getFromIndex($i);
            if ($contents !== false) {
                file_put_contents($target, $contents);
                $extracted++;
            }
        }

        $zip->close();

        return [
            'files' => $extracted,
            'statements' => $dbStatements,
            'type' => 'files',
        ];
    }

    /**
     * Restore a full backup: database.sql dump + storage files from one archive.
     */
    protected function restoreFull(string $path): array
    {
        $result = $this->restoreFiles($path);

        return array_merge($result, [
            'type' => 'full',
        ]);
    }

    /**
     * Read the meta payload for a given backup, if present.
     */
    protected function metaFor(string $filename): ?array
    {
        $metaPath = self::BACKUP_DIR . '/' . $filename . '.meta.json';

        if (!Storage::disk(self::BACKUP_DISK)->exists($metaPath)) {
            return null;
        }

        $data = json_decode(Storage::disk(self::BACKUP_DISK)->get($metaPath), true);

        return is_array($data) ? $data : null;
    }

    /**
     * Fallback type detection when no meta file exists.
     */
    protected function guessType(string $filename): string
    {
        if (str_contains($filename, 'full_')) {
            return 'full';
        }

        if (str_contains($filename, 'files_') || str_ends_with($filename, '.zip')) {
            return 'files';
        }

        return 'database';
    }

    /**
     * Split a SQL dump into individual statements, respecting quoted strings.
     */
    protected function splitStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $length = strlen($sql);
        $inString = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $current .= $char;

            if ($inString) {
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sql[$i + 1];
                    $i++;
                    continue;
                }
                if ($char === "'") {
                    if ($i + 1 < $length && $sql[$i + 1] === "'") {
                        $current .= $sql[$i + 1];
                        $i++;
                        continue;
                    }
                    $inString = false;
                }
                continue;
            }

            if ($char === "'") {
                $inString = true;
                continue;
            }

            if ($char === ';') {
                $statement = trim($current);
                if ($statement !== '' && !str_starts_with($statement, '--')) {
                    $statements[] = $statement;
                }
                $current = '';
            }
        }

        $statement = trim($current);
        if ($statement !== '' && !str_starts_with($statement, '--')) {
            $statements[] = $statement;
        }

        return $statements;
    }

    /**
     * Load all backup metadata from the backups directory.
     */
    protected function allMeta(): array
    {
        $files = Storage::disk(self::BACKUP_DISK)->files(self::BACKUP_DIR);
        $meta = [];

        foreach ($files as $file) {
            if (!str_ends_with($file, '.meta.json')) {
                continue;
            }

            try {
                $data = json_decode(Storage::disk(self::BACKUP_DISK)->get($file), true);
                if (is_array($data)) {
                    $target = self::BACKUP_DIR . '/' . ($data['filename'] ?? basename($file, '.meta.json'));
                    $data['exists'] = Storage::disk(self::BACKUP_DISK)->exists($target);
                    if ($data['exists']) {
                        $data['size'] = $data['size'] ?? Storage::disk(self::BACKUP_DISK)->size($target);
                        $data['human_size'] = $this->humanSize($data['size']);
                    }
                    $meta[] = $data;
                }
            } catch (\Throwable $e) {
                Log::warning("Skipping unreadable backup meta: {$file}");
            }
        }

        return $meta;
    }

    protected function databaseName(): string
    {
        return (string) config('database.connections.' . DB::getDefaultConnection() . '.database', 'unknown');
    }

    protected function isSafeFilename(string $filename): bool
    {
        return $filename !== '' && !str_contains($filename, '/') && !str_contains($filename, '..') && !str_contains($filename, '\\');
    }

    protected function extensionFor(string $type): string
    {
        return ($type === 'files' || $type === 'full') ? 'zip' : 'sql';
    }

    public function humanSize($bytes): string
    {
        $bytes = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        foreach ($units as $unit) {
            if ($bytes < 1024 || $unit === 'TB') {
                return $bytes == 0 ? '0 B' : round($bytes, 2) . ' ' . $unit;
            }
            $bytes /= 1024;
        }

        return '0 B';
    }
}
