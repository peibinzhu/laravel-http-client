<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Contracts;

interface Packer
{
    public function pack($data): string;

    public function unpack(string $data);
}
