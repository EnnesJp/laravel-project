<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\DebitRepositoryInterface;
use App\Repositories\Contracts\FundDebitRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\CreditRepository;
use App\Repositories\DebitRepository;
use App\Repositories\FundDebitRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
