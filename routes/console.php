<?php

use App\Jobs\SendScheduledMessage;
use App\Models\MessageScheduler;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Message Dispatcher
|--------------------------------------------------------------------------
|
| Runs every minute to find pending messages whose scheduled time (waktu)
| has arrived, and dispatches a queued job for each one.
|
*/
Schedule::call(function () {
    $dueSchedulers = MessageScheduler::where('status', 'pending')
        ->where('waktu', '<=', now())
        ->get();

    foreach ($dueSchedulers as $scheduler) {
        // Mark as processing immediately to prevent duplicate dispatch
        $scheduler->update(['status' => 'processing']);

        SendScheduledMessage::dispatch($scheduler->id);
    }
})->everyMinute()
  ->name('dispatch-scheduled-messages')
  ->withoutOverlapping();
