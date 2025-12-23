<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Transaction\Services\BalanceCacheService;
use App\Domains\User\Models\User;
use Illuminate\Console\Command;

class WarmBalanceCache extends Command
{
    protected $signature   = 'balance:warm-cache {--user-id= : Specific user ID to warm cache for}';
    protected $description = 'Warm up the balance cache for users';

    public function __construct(
        private readonly BalanceCacheService $cacheService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $userId = $this->option('user-id');

        if ($userId) {
            $this->warmCacheForUser((int) $userId);

            return self::SUCCESS;
        }

        $this->warmCacheForAllUsers();

        return self::SUCCESS;
    }

    private function warmCacheForUser(int $userId): void
    {
        $this->info("Warming cache for user ID: {$userId}");

        $balance = $this->cacheService->refreshUserBalance($userId);

        $this->info("Cache warmed. Balance: {$balance}");
    }

    private function warmCacheForAllUsers(): void
    {
        $this->info('Warming cache for all users...');

        $users = User::select('id')->get();
        $bar   = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $this->cacheService->refreshUserBalance($user->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Cache warming completed for all users.');
    }
}
