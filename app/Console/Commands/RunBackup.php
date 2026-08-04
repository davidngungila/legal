<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class RunBackup extends Command
{
    protected $signature = 'backup:run
        {--type=database : Type of backup: database, files or full}
        {--keep=10 : Number of backups to keep after running}';

    protected $description = 'Create a database and/or file backup';

    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type');
        $keep = (int) $this->option('keep');

        if (!in_array($type, BackupService::TYPES, true)) {
            $this->error("Invalid backup type '{$type}'. Allowed: " . implode(', ', BackupService::TYPES));
            return self::FAILURE;
        }

        $this->info("Starting {$type} backup...");

        try {
            $meta = $backupService->create($type, null);

            $this->info("Backup created: {$meta['filename']}");
            $this->line("    Type:     {$meta['type']}");
            $this->line("    Tables:   {$meta['tables']}");
            $this->line("    Rows:     {$meta['rows']}");
            $this->line("    Size:     {$backupService->humanSize($meta['size'])}");
            $this->line("    Database: {$meta['database']}");

            $pruned = $backupService->prune($keep);
            if ($pruned > 0) {
                $this->info("Pruned {$pruned} old backup(s), keeping last {$keep}.");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
