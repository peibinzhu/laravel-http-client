<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use PeibinLaravel\HttpClient\Annotation\Service;
use PeibinLaravel\HttpClient\Events\AfterRequest;
use PeibinLaravel\HttpClient\Events\BeforeRequest;
use PeibinLaravel\HttpClient\Exception\RequestException;
use PeibinLaravel\HttpClient\Utils\DataFormatter;
use PeibinLaravel\HttpClient\Utils\IdGenerator;
use PeibinLaravel\HttpClient\Utils\JsonPacker;
use PeibinLaravel\HttpClient\Utils\RequestBuilder;
use PeibinLaravel\HttpClient\Utils\ResponseBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ServiceClient
{
    /**
     * @var array
     */
    private $methodData;

    /**
     * @var array
     */
    private $options;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var IdGenerator
     */
    private $idGenerator;

    /**
     * @var DataFormatter
     */
    private $dataFormatter;

    /**
     * @var RequestBuilder
     */
    private $requestGenerator;

    /**
     * @var ResponseBuilder
     */
    private $responsePacker;

    /**
     * @var JsonPacker
     */
    private $dataPacker;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    /**
     * @param Container $container
     * @param array     $methodData
     * @param array     $options
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(Container $container, array $methodData = [], array $options = [])
    {
        $this->container = $container;
        $this->methodData = $methodData;
        $this->options = $options;

        /** @var Client $client */
        $client = $this->container->make(Client::class);
        $this->dataPacker = $this->container->make(JsonPacker::class);
        $responsePacker = $this->container->make(ResponseBuilder::class);

        $this->client = $client->setPacker($responsePacker);
        $this->idGenerator = $this->container->make(IdGenerator::class);
        $this->dataFormatter = $this->container->make(DataFormatter::class);
        $this->requestGenerator = $this->container->make(RequestBuilder::class);
        $this->responsePacker = $this->container->make(ResponseBuilder::class);
        $this->eventDispatcher = $this->container->get(Dispatcher::class);
    }

    protected function __request(string $method, array $params, ?string $id = null): mixed
    {
        if (!$id) {
            $id = $this->idGenerator->generate();
        }

        /** @var Service $methodMetadata */
        $methodMetadata = $this->methodData[$method];

        $headers = $this->options['settings'][RequestOptions::HEADERS] ?? [];

        $timeout = $this->options['settings'][RequestOptions::TIMEOUT] ?? 1.5;
        $timeout = $methodMetadata->timeout ?: $timeout;

        $host = $this->options['host'];
        $uri = '/' . ltrim($methodMetadata->uri, '/');
        $data = $this->dataFormatter->formatRequest([$params, $id]);

        $method = $methodMetadata->method;
        $contentType = $this->options['settings']['content_type'] ?? $method;

        $request = $this
            ->requestGenerator
            ->setHeader($headers)
            ->setUrl($host, $uri)
            ->setMethod($method)
            ->setTimeout($timeout)
            ->setData($data, $contentType);

        $this->eventDispatcher->dispatch(new BeforeRequest($request));

        $responsePacker = $this->client->send($request);

        $this->eventDispatcher->dispatch(new AfterRequest($request, $responsePacker));

        if (!$responsePacker->hasResponse()) {
            $exception = $responsePacker->getException();
            throw new RequestException($exception->getMessage(), $exception->getCode(), $responsePacker);
        }

        $responseContent = (string)$responsePacker->getResponse()->getBody();
        $responseData = $this->dataPacker->unpack($responseContent);
        if ($this->dataPacker->hasError()) {
            throw new RequestException(
                'Invalid response.',
                $responsePacker->getException()->getCode(),
                $responsePacker
            );
        }

        return $this->dataFormatter->formatResponse($responseData);
    }

    public function __call(string $method, array $params)
    {
        return $this->__request($method, $params);
    }
}
