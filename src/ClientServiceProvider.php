<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\ServiceProvider;
use PeibinLaravel\HttpClient\Listeners\AddConsumerDefinitionListener;
use PeibinLaravel\SwooleEvent\Events\BeforeMainServerStart;
use PeibinLaravel\SwooleEvent\Events\BeforeWorkerStart;
use PeibinLaravel\Utils\Providers\RegisterProviderConfig;

class ClientServiceProvider extends ServiceProvider
{
    use RegisterProviderConfig;

    public function __invoke(): array
    {
        return [
            'listeners' => [
                ArtisanStarting::class       => AddConsumerDefinitionListener::class,
                BeforeMainServerStart::class => AddConsumerDefinitionListener::class,
                BeforeWorkerStart::class     => AddConsumerDefinitionListener::class,
            ],
        ];
    }
}
