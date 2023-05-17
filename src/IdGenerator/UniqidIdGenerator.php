<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\IdGenerator;

use PeibinLaravel\Contracts\IdGeneratorInterface;

class UniqidIdGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        return uniqid();
    }
}
