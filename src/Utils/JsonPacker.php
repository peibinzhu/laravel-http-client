<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

use PeibinLaravel\HttpClient\Contracts\Packer;

class JsonPacker implements Packer
{
    public function pack($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function unpack(string $data)
    {
        return json_decode($data, true);
    }

    public function hasError(): bool
    {
        return json_last_error() !== JSON_ERROR_NONE;
    }
}
