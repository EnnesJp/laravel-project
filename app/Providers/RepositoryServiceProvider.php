<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository mappings - only define exceptions to the convention here
     *
     * @var array<string, string>
     */
    private array $customMappings = [
        'App\Repositories\Contracts\CacheRepositoryInterface' => 'App\Repositories\RedisCacheRepository',
    ];

    /**
     * Interfaces namespaces to scan
     *
     * @var array<string, string>
     */
    private array $interfaceNamespaces = [
        'Repositories/Contracts'                     => 'App\Repositories\Contracts',
        'Domains/User/Repositories/Contracts'        => 'App\Domains\User\Repositories\Contracts',
        'Domains/Transaction/Repositories/Contracts' => 'App\Domains\Transaction\Repositories\Contracts',
    ];

    /**
     * Repository namespaces to scan
     *
     * @var array<string>
     */
    private array $repositoryNamespaces = [
        'App\Repositories',
        'App\Domains\User\Repositories',
        'App\Domains\Transaction\Repositories',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->autoBindRepositories();
    }

    /**
     * Auto-bind repositories using naming convention and file discovery
     */
    private function autoBindRepositories(): void
    {
        foreach ($this->interfaceNamespaces as $interface => $namespace) {
            $interfacePath = app_path($interface);

            if (!is_dir($interfacePath)) {
                return;
            }

            $interfaceFiles = glob($interfacePath . '/*RepositoryInterface.php');

            foreach ($interfaceFiles as $file) {
                $className      = basename($file, '.php');
                $interfaceClass = "{$namespace}\\{$className}";

                if (isset($this->customMappings[$interfaceClass])) {
                    $this->app->bind($interfaceClass, $this->customMappings[$interfaceClass]);
                    continue;
                }

                $implementation = $this->findImplementation($className);

                if ($implementation && class_exists($implementation)) {
                    $this->app->bind($interfaceClass, $implementation);
                }
            }
        }
    }

    /**
     * Find the implementation class for an interface
     */
    private function findImplementation(string $interfaceName): ?string
    {
        $repositoryName = str_replace('Interface', '', $interfaceName);

        foreach ($this->repositoryNamespaces as $namespace) {
            $implementationClass = "{$namespace}\\{$repositoryName}";

            if (class_exists($implementationClass)) {
                return $implementationClass;
            }
        }

        return null;
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
