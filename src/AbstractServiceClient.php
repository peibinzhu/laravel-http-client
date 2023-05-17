<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use PeibinLaravel\Contracts\IdGeneratorInterface;
use PeibinLaravel\Contracts\PackerInterface;
use PeibinLaravel\HttpClient\Contracts\DataFormatterInterface;
use PeibinLaravel\HttpClient\Utils\RequestBuilder;
use PeibinLaravel\HttpClient\Utils\ResponseBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractServiceClient
{
    protected Client $client;

    protected IdGeneratorInterface $idGenerator;

    protected DataFormatterInterface $dataFormatter;

    protected RequestBuilder $requestGenerator;

    protected ResponseBuilder $responsePacker;

    protected PackerInterface $dataPacker;

    protected Dispatcher $eventDispatcher;

    /**
     * @param Container $container
     * @param string    $serviceName
     * @param array     $methodData
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        protected Container $container,
        protected string $serviceName,
        protected array $methodData = []
    ) {
        $client = $this->container->make(Client::class);
        $this->dataPacker = $this->container->get(PackerInterface::class);
        $responsePacker = $this->container->get(ResponseBuilder::class);

        $this->client = $client->setPacker($responsePacker);
        $this->idGenerator = $this->container->get(IdGeneratorInterface::class);
        $this->dataFormatter = $this->container->get(DataFormatterInterface::class);

        $this->requestGenerator = $this->container->get(RequestBuilder::class);
        $this->responsePacker = $this->container->get(ResponseBuilder::class);
        $this->eventDispatcher = $this->container->get(Dispatcher::class);
    }

    protected function getConsumerConfig(): array
    {
        $key = sprintf('services.consumers.%s', $this->serviceName);
        return $this->container->make(Repository::class)->get($key, []);
    }
}
