<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Listeners\InvalidateBalanceCache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class TransactionEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TransactionFailed::class => [
            InvalidateBalanceCache::class,
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
