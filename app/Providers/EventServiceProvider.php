<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PublicRequestApproved;
use App\Events\PublicRequestRejected;
use App\Events\VisitorCheckedIn;
use App\Events\VisitorCheckedOut;
use App\Listeners\LogPublicRequestApproval;
use App\Listeners\LogPublicRequestRejection;
use App\Listeners\LogVisitorCheckIn;
use App\Listeners\LogVisitorCheckOut;

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
        ],
        PublicRequestApproved::class => [
            LogPublicRequestApproval::class,
        ],
        PublicRequestRejected::class => [
            LogPublicRequestRejection::class,
        ],
        VisitorCheckedIn::class => [
            LogVisitorCheckIn::class,
        ],
        VisitorCheckedOut::class => [
            LogVisitorCheckOut::class,
        ],
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
