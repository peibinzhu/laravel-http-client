<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use GuzzleHttp\RequestOptions;
use PeibinLaravel\HttpClient\Annotation\Service;
use PeibinLaravel\HttpClient\Events\AfterRequest;
use PeibinLaravel\HttpClient\Events\BeforeRequest;
use PeibinLaravel\HttpClient\Exceptions\RequestException;

class ServiceClient extends AbstractServiceClient
{
    protected function __request(string $method, array $params, ?string $id = null): mixed
    {
        if (!$id) {
            $id = $this->idGenerator->generate();
        }

        /** @var Service $methodMetadata */
        $methodMetadata = $this->methodData[$method];

        $options = $this->getConsumerConfig();

        $headers = $options['settings'][RequestOptions::HEADERS] ?? [];

        $timeout = $options['settings'][RequestOptions::TIMEOUT] ?? 1.5;
        $timeout = $methodMetadata->timeout ?: $timeout;

        $host = $options['host'];
        $uri = '/' . ltrim($methodMetadata->uri, '/');
        $data = $this->dataFormatter->formatRequest($params);

        $method = $methodMetadata->method;
        $contentType = $options['settings']['content_type'] ?? $method;

        $request = $this
            ->requestGenerator
            ->setId($id)
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
        if (!is_array($responseData)) {
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
