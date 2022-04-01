<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Events;

use PeibinLaravel\HttpClient\Utils\RequestBuilder;
use PeibinLaravel\HttpClient\Utils\ResponseBuilder;

class AfterRequest
{
    /**
     * @var RequestBuilder
     */
    public $requestBuilder;

    /**
     * @var ResponseBuilder
     */
    public $responseBuilder;

    /**
     * @param RequestBuilder  $requestBuilder
     * @param ResponseBuilder $responseBuilder
     */
    public function __construct(RequestBuilder $requestBuilder, ResponseBuilder $responseBuilder)
    {
        $this->requestBuilder = $requestBuilder;
        $this->responseBuilder = $responseBuilder;
    }
}
