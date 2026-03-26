<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:sync-user')
    ->weekly()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/sync-user.log'));

Schedule::command('app:sync-schedule')
    ->hourly()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/sync-schedule.log'));


Schedule::command('app:sync-dtr-to-qis')
    ->everyMinute()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/sync-dtr-to-qis.log'));