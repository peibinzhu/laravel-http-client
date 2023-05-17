<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient;

use PeibinLaravel\HttpClient\Contracts\DataFormatterInterface;

class DataFormatter implements DataFormatterInterface
{
    public function formatRequest($data)
    {
        $params = is_array($data) ? $data : [];
        if ($params) {
            $params = count($params) > 1 ? ['params' => $params] : $params[0];
        }
        return $params;
    }

    public function formatResponse($data): array
    {
        return $data;
    }

    public function formatErrorResponse($data)
    {
        // TODO: Implement formatErrorResponse() method.
    }
}
