<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Contracts;

interface DataFormatter
{
    /**
     * @param array $data [$params, $id]
     * @return array
     */
    public function formatRequest($data);

    /**
     * @param array $data [$id, $result]
     * @return array
     */
    public function formatResponse($data);

    /**
     * @param array $data [$id, $code, $message, $exception]
     * @return array
     */
    public function formatErrorResponse($data);
}
