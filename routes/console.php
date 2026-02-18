<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Déclencher les campagnes planifiées toutes les minutes
Schedule::command('campaigns:send-scheduled')->everyMinute()->withoutOverlapping();
