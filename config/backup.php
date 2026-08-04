<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Retention
    |--------------------------------------------------------------------------
    |
    | Maximum number of backups to retain. Older backups are automatically
    | pruned after each new backup is created.
    |
    */

    'retention' => env('BACKUP_RETENTION', 10),

];
