<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Mail\SendTaskCompletionEmail as SendTaskCompletionMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;


class SendTaskCompletionEmail
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
        //
        $user = User::where('id' , $event->task->user_id)->first();
        if ($user) {
            Mail::to($user->email)->send(new SendTaskCompletionMail($event->task));
        }
    }
}
