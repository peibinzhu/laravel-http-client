<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
use PeibinLaravel\HttpClient\Utils\RequestBuilder;
use PeibinLaravel\HttpClient\Utils\ResponseBuilder;
use Throwable;

class Client
{
    private ?ResponseBuilder $packer = null;

    public function send(RequestBuilder $request): ResponseBuilder
    {
        if (!$this->packer) {
            throw new InvalidArgumentException('Packer missing.');
        }

        $packer = $this->packer;
        $packer->setRequestTime(time());

        $request->setOptions([
            'on_stats' => function (TransferStats $stats) use (&$packer) {
                $packer->setRequest($stats->getRequest());
                $packer->setTransferTime($stats->getTransferTime());
            },
        ]);

        try {
            $client = new HttpClient();
            $response = $client->request(
                $request->getMethod(),
                $request->getUrl(),
                $request->getOptions(),
            );
            $packer->setResponse($response);
        } catch (Throwable $exception) {
            $packer->setException($exception);
        }
        return $packer;
    }

    public function getPacker(): ResponseBuilder
    {
        return $this->packer;
    }

    public function setPacker(ResponseBuilder $packer): self
    {
        $this->packer = $packer;
        return $this;
    }
}
