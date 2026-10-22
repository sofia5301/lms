<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check teacher YouTube channels for live streams, during school hours, every few minutes.
// Needs a server cron running `php artisan schedule:run` every minute. withoutOverlapping stops
// a slow sweep from stacking on the next tick.
Schedule::command('live:refresh')
    ->everyThreeMinutes()
    ->between('6:00', '20:00')
    ->withoutOverlapping();
