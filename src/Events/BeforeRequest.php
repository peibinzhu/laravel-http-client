<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Events;

use PeibinLaravel\HttpClient\Utils\RequestBuilder;

class BeforeRequest
{
    /**
     * @var RequestBuilder
     */
    public $builder;

    public function __construct(RequestBuilder $builder)
    {
        $this->builder = $builder;
    }
}
