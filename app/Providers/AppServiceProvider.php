<?php

namespace App\Providers;

use App\Events\TaskCompleted;
use App\Listeners\SendTaskCompletionEmailListener;
use App\Models\Role;
use App\Models\Task;
use App\Policies\RolePolicy;
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
        Gate::policy(Role::class, RolePolicy::class);
        Event::listen(TaskCompleted::class , SendTaskCompletionEmailListener::class);
    }
}
