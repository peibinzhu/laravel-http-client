<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\HttpClient\Listeners\AddConsumerDefinitionListener;
use PeibinLaravel\SwooleEvent\Events\BootApplication;
use PeibinLaravel\Utils\Providers\RegisterProviderConfig;

class ClientServiceProvider extends ServiceProvider
{
    use RegisterProviderConfig;

    public function __invoke(): array
    {
        return [
            'listeners' => [
                BootApplication::class => AddConsumerDefinitionListener::class,
            ],
        ];
    }
}
