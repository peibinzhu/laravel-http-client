<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Annotation;

use Attribute;
use PeibinLaravel\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD)]
class Service extends AbstractAnnotation
{
    public const GET = 'GET';
    public const DELETE = 'DELETE';
    public const HEAD = 'HEAD';
    public const OPTIONS = 'OPTIONS';
    public const PATCH = 'PATCH';
    public const POST = 'POST';
    public const PUT = 'PUT';

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $method;

    /**
     * @var int|float
     */
    public $timeout;

    /**
     * @param string         $uri
     * @param string         $method
     * @param int|float|null $timeout
     */
    public function __construct(string $uri, string $method = self::POST, int|float $timeout = null)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->timeout = $timeout;
    }
}
