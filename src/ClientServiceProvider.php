<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\HttpClient\Listeners\AddConsumerDefinitionListener;
use PeibinLaravel\ProviderConfig\Contracts\ProviderConfigInterface;
use PeibinLaravel\SwooleEvent\Events\BootApplication;

class ClientServiceProvider extends ServiceProvider implements ProviderConfigInterface
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                BootApplication::class => AddConsumerDefinitionListener::class,
            ],
        ];
    }
}
