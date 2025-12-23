<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Transaction\Repositories\Contracts\CreditRepositoryInterface;
use App\Domains\Transaction\Repositories\Contracts\DebitRepositoryInterface;
use App\Domains\Transaction\Repositories\Contracts\FundDebitRepositoryInterface;
use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domains\Transaction\Repositories\CreditRepository;
use App\Domains\Transaction\Repositories\DebitRepository;
use App\Domains\Transaction\Repositories\FundDebitRepository;
use App\Domains\Transaction\Repositories\RemainingCreditRepository;
use App\Domains\Transaction\Repositories\TransactionRepository;
use App\Domains\User\Repositories\Contracts\UserRepositoryInterface;
use App\Domains\User\Repositories\UserRepository;
use App\Repositories\Contracts\CacheRepositoryInterface;
use App\Repositories\RedisCacheRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(CreditRepositoryInterface::class, CreditRepository::class);
        $this->app->bind(DebitRepositoryInterface::class, DebitRepository::class);
        $this->app->bind(FundDebitRepositoryInterface::class, FundDebitRepository::class);
        $this->app->bind(RemainingCreditRepositoryInterface::class, RemainingCreditRepository::class);
        $this->app->bind(CacheRepositoryInterface::class, RedisCacheRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
