<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\MainServerStarting;
use Laravel\Octane\Events\WorkerStarting;
use PeibinLaravel\HttpClient\Listeners\AddConsumerDefinitionListener;
use PeibinLaravel\Utils\Providers\RegisterProviderConfig;

class ClientServiceProvider extends ServiceProvider
{
    use RegisterProviderConfig;

    public function __invoke(): array
    {
        return [
            'listeners' => [
                ArtisanStarting::class    => AddConsumerDefinitionListener::class,
                MainServerStarting::class => AddConsumerDefinitionListener::class,
                WorkerStarting::class     => AddConsumerDefinitionListener::class,
            ],
        ];
    }
}
