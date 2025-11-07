<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Mail\SendTaskCompletionEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendTaskCompletionEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct(){

    }

    /**
     * Handle the event.
     */
    public function handle(TaskCompleted $event): void
    {
        Mail::send(new SendTaskCompletionEmail($event->task));
    }
}
