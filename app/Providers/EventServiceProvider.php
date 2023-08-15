<?php

namespace App\Providers;

use App\Events\ChallengeCompleted;
use App\Events\ChallengeForked;
use App\Events\ChallengeJoined;
use App\Events\ReactionCreated;
use App\Events\ReactionDeleted;
use App\Listeners\AwardPoints;
use App\Listeners\Registered as RegisteredListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            RegisteredListener::class,
        ],
        ChallengeCompleted::class => [AwardPoints::class],
        ChallengeJoined::class => [AwardPoints::class],
        ChallengeForked::class => [AwardPoints::class],
        ReactionCreated::class => [AwardPoints::class],
        ReactionDeleted::class => [AwardPoints::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
