<?php

declare(strict_types=1);

namespace PeibinLaravel\HttpClient\Utils;

use GuzzleHttp\RequestOptions;

class RequestBuilder
{
    private ?string $id;

    private $url;

    private $data;

    private $method = 'POST';

    private $options = [
        RequestOptions::TIMEOUT => 5,
    ];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setHeader(array | string $key, string $value = null): self
    {
        $headers = $this->options[RequestOptions::HEADERS] ?? [];
        if (is_array($key)) {
            $headers = array_merge($headers, $key);
        } else {
            $headers[$key] = $value;
        }
        $this->options[RequestOptions::HEADERS] = $headers;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->options[RequestOptions::HEADERS] = $headers;
        return $this;
    }

    public function setUrl(string $host, string $url): self
    {
        $this->url = rtrim($host, '/') . '/' . trim($url, '/');
        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function setTimeout(int | float $timeout): self
    {
        $this->options[RequestOptions::TIMEOUT] = $timeout;
        return $this;
    }

    public function setData($data, string $type = RequestOptions::QUERY): self
    {
        if (strtoupper($this->method) == 'GET') {
            $type = 'get';
        }

        match (strtolower($type)) {
            RequestOptions::QUERY, 'get' => $this->setQueryData($data),
            RequestOptions::FORM_PARAMS, 'post' => $this->setFormData($data),
            RequestOptions::MULTIPART => $this->setMultipartData($data),
            RequestOptions::JSON, 'put' => $this->setJsonData($data),
        };

        $this->data = $data;
        return $this;
    }

    public function setQueryData(array $data): self
    {
        $this->options[RequestOptions::QUERY] = $data;
        return $this;
    }

    public function setFormData(array $data): self
    {
        $this->options[RequestOptions::FORM_PARAMS] = $data;
        return $this;
    }

    public function setMultipartData(array $data): self
    {
        $this->options[RequestOptions::MULTIPART] = $data;
        return $this;
    }

    public function setJsonData(array $data): self
    {
        $this->options[RequestOptions::JSON] = $data;
        return $this;
    }

    public function setOption(array | string $key, string $value = null): self
    {
        if (is_array($key)) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $value;
        }
        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }
}
