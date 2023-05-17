<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\IdGenerator;

use PeibinLaravel\Contracts\IdGeneratorInterface;

class RequestIdGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        $us = strstr(microtime(), ' ', true);
        return $us * 1000 * 1000 . rand(100, 999);
    }
}
