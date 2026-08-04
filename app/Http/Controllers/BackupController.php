<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = $this->backupService->list();
        $summary = $this->backupService->summary();

        $creatorIds = $backups->pluck('created_by')->filter()->unique()->values();
        $creators = \App\Models\User::whereIn('id', $creatorIds)->get()->keyBy('id')
            ->map(fn ($user) => trim($user->first_name . ' ' . $user->last_name))
            ->all();

        return view('backups.index', compact('backups', 'summary', 'creators'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:database,files,full',
        ]);

        try {
            $meta = $this->backupService->create($request->input('type'), auth()->id());
            $size = $meta['human_size'] ?? $this->backupService->humanSize($meta['size']);

            AuditLogger::log(
                'created',
                null,
                'System Administration',
                "Created {$meta['type']} backup: {$meta['filename']} ({$size})"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($meta['type']) . ' backup created successfully.',
                    'backup' => $meta,
                ]);
            }

            return back()->with('success', ucfirst($meta['type']) . ' backup created successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(Request $request, string $filename)
    {
        $path = $this->backupService->pathFor($filename);

        if (!$path) {
            abort(404, 'Backup not found.');
        }

        return response()->download($path, $filename);
    }

    public function restore(Request $request, string $filename)
    {
        try {
            $result = $this->backupService->restore($filename);

            AuditLogger::log(
                'restored',
                null,
                'System Administration',
                "Restored backup: {$filename} (" . json_encode($result) . ")"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup restored successfully.',
                ]);
            }

            return back()->with('success', 'Backup restored successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restore failed: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, string $filename)
    {
        try {
            $deleted = $this->backupService->delete($filename);

            if (!$deleted) {
                throw new \RuntimeException('Invalid backup file.');
            }

            AuditLogger::log(
                'deleted',
                null,
                'System Administration',
                "Deleted backup: {$filename}"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully.',
                ]);
            }

            return back()->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delete failed: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip|max:51200',
        ]);

        try {
            $file = $request->file('backup_file');
            $meta = $this->backupService->upload($file->getClientOriginalName(), $file->getRealPath());

            AuditLogger::log(
                'created',
                null,
                'System Administration',
                "Uploaded backup: {$meta['filename']}"
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup uploaded successfully.',
                    'backup' => $meta,
                ]);
            }

            return back()->with('success', 'Backup uploaded successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function clean(Request $request)
    {
        $keep = (int) $request->input('keep', config('backup.retention', 10));
        $removed = $this->backupService->prune($keep);

        AuditLogger::log(
            'deleted',
            null,
            'System Administration',
            "Pruned old backups, keeping last {$keep} (removed {$removed})"
        );

        return back()->with('success', "Retention applied. Removed {$removed} old backup(s).");
    }
}
