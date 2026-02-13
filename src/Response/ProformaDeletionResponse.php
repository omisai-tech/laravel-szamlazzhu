<?php

namespace Omisai\Szamlazzhu\Response;

class ProformaDeletionResponse extends AbstractResponse
{
    protected string $proformaNumber;

    protected array $headers;

    public function parseData()
    {
        if (gettype($this->getData()) !== 'array' || empty($this->getData()) || empty($this->getData()['headers'])) {
            return;
        }

        $this->headers = $this->getData()['headers'];

        if (array_key_exists('szlahu_error', $this->headers)) {
            $this->errorMessage = urldecode($this->headers['szlahu_error']);
        }

        if (array_key_exists('szlahu_error_code', $this->headers)) {
            $this->errorCode = $this->headers['szlahu_error_code'];
        }

        if (!$this->hasError()) {
            $this->isSuccess = true;
        }
    }
}
