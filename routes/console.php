<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:clean-expired-users')->everyMinute()->withoutOverlapping();

// Expire SaaS subscriptions (and trials) once their period lapses.
Schedule::command('saas:expire-subscriptions')->dailyAt('00:05')->withoutOverlapping();
