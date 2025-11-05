<?php

namespace App\Providers;

use App\Events\TaskCompleted;
use App\Listeners\SendTaskCompletionEmail;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Task::class , TaskPolicy::class);
        Event::listen(TaskCompleted::class , SendTaskCompletionEmail::class);
    }
}
