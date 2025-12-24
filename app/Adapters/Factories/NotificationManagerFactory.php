<?php

declare(strict_types=1);

namespace App\Adapters\Factories;

use App\Adapters\Contracts\NotificationManagerInterface;
use App\Adapters\NotificationManager;

class NotificationManagerFactory
{
    /**
     * @param array<string, array{type: string, config: array<string, mixed>}> $channelsConfig
     */
    public static function create(array $channelsConfig): NotificationManagerInterface
    {
        $adapters = [];

        foreach ($channelsConfig as $channel => $config) {
            $type          = $config['type'];
            $adapterConfig = $config['config'];

            $adapters[$channel] = NotificationAdapterFactory::create($type, $adapterConfig);
        }

        return new NotificationManager($adapters);
    }
}
