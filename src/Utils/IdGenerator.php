<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

use PeibinLaravel\HttpClient\Contracts\IdGenerator as IdGeneratorContract;

class IdGenerator implements IdGeneratorContract
{
    public function generate(): string
    {
        $us = strstr(microtime(), ' ', true);
        return $us * 1000 * 1000 . rand(100, 999);
    }
}
