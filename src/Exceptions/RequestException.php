<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Exceptions;

use PeibinLaravel\HttpClient\Utils\ResponseBuilder;
use RuntimeException;

class RequestException extends RuntimeException
{
    /**
     * @var ResponseBuilder
     */
    private $builder;

    public function __construct(string $message = '', int $code = 0, ResponseBuilder $builder = null)
    {
        parent::__construct($message, $code);
        $this->builder = $builder;
    }

    public function getBuilder(): ?ResponseBuilder
    {
        return $this->builder;
    }
}
