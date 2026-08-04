<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Backups
|--------------------------------------------------------------------------
|
| Run `php artisan schedule:work` (or configure cron on the server) to
| enable automatic backups. Daily database backups at 02:00 and a full
| backup (database + files) every Monday at 03:00.
|
*/

Schedule::command('backup:run --type=database --keep=10')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled database backup failed.');
    });

Schedule::command('backup:run --type=full --keep=10')
    ->weeklyOn(1, '03:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled full backup failed.');
    });
