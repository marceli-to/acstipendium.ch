<?php

use Illuminate\Support\Facades\Schedule;

if (app()->environment('production')) {
    Schedule::command('health:check')->everyFiveMinutes();
    Schedule::command('backup:create')->daily();
}
