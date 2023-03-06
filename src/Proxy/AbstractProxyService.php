<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Proxy;

use Illuminate\Contracts\Container\Container;
use PeibinLaravel\HttpClient\ServiceClient;

abstract class AbstractProxyService
{
    /**
     * @var ServiceClient
     */
    protected $client;

    public function __construct(Container $container, string $serviceName, array $methodData = [])
    {
        $this->client = $container->make(ServiceClient::class, [
            'container'   => $container,
            'serviceName' => $serviceName,
            'methodData'  => $methodData,
        ]);
    }
}
