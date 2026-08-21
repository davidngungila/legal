<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingChecklistTemplate extends Model
{
    protected $fillable = [
        'client_id',
        'task_key',
        'task_name',
        'category',
        'order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the checklist template for a client.
     * Returns the customized template if saved, otherwise the default.
     */
    public static function getForClient(int $clientId): array
    {
        $saved = self::where('client_id', $clientId)
            ->orderBy('category')
            ->orderBy('order')
            ->get();

        if ($saved->isEmpty()) {
            return self::getDefaultTasks();
        }

        return $saved->map(function ($item) {
            return [
                'task_key' => $item->task_key,
                'task_name' => $item->task_name,
                'category' => $item->category,
                'order' => $item->order,
                'is_required' => $item->is_required,
                'custom' => true,
            ];
        })->toArray();
    }

    /**
     * Replace the client's template with the given tasks.
     */
    public static function saveForClient(int $clientId, array $tasks): void
    {
        self::where('client_id', $clientId)->delete();

        foreach ($tasks as $index => $task) {
            self::create([
                'client_id' => $clientId,
                'task_key' => $task['task_key'] ?? 'custom_task_' . ($index + 1),
                'task_name' => $task['task_name'],
                'category' => $task['category'],
                'order' => $task['order'] ?? ($index + 1),
                'is_required' => filter_var($task['is_required'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Get the default tasks (from the base checklist definition).
     */
    public static function getDefaultTasks(): array
    {
        return array_map(function ($task) {
            $task['custom'] = false;
            return $task;
        }, OnboardingChecklist::getDefaultChecklist());
    }
}
