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

    public function __construct(\Closure $callback, array $methodData = [], array $options = [])
    {
        $container = $callback();
        $this->client = $container->make(ServiceClient::class, [
            'container'  => $container,
            'methodData' => $methodData,
            'options'    => $options,
        ]);
    }
}
