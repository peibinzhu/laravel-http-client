<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Proxy;

use PeibinLaravel\HttpClient\ServiceClient;

abstract class AbstractProxyService
{
    /**
     * @var ServiceClient
     */
    protected $client;

    public function __construct(\Closure $callback, string $serviceName, array $methodData = [])
    {
        $container = $callback();
        $this->client = $container->make(ServiceClient::class, [
            'container'   => $container,
            'serviceName' => $serviceName,
            'methodData'  => $methodData,
        ]);
    }
}
