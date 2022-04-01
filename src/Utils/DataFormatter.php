<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

use PeibinLaravel\HttpClient\Contracts\DataFormatter as DataFormatterContract;

class DataFormatter implements DataFormatterContract
{
    public function formatRequest($data)
    {
        [$params, $id] = $data;
        $params = is_array($params) ? $params : [];

        if ($params) {
            $params = count($params) > 1 ? ['params' => $params] : $params[0];
        }
        return array_merge($params, ['_id' => $id]);
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
