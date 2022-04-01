<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var int
     */
    private $requestTime;

    /**
     * @var float
     */
    private $runtime;

    public function setRequestTime(int $time): self
    {
        $this->requestTime = $time;
        return $this;
    }

    public function setRequest(RequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function setException(Exception $exception): self
    {
        $this->exception = $exception;
        return $this;
    }

    public function setTransferTime(float $runtime): self
    {
        $this->runtime = $runtime;
        return $this;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getException(): ?Exception
    {
        return $this->exception;
    }

    public function getRequestTime(): ?int
    {
        return $this->requestTime;
    }

    public function getRuntime(): ?float
    {
        return $this->runtime;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
}
