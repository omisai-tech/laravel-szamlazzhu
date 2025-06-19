<?php

namespace Omisai\Szamlazzhu\Response;

use Illuminate\Support\Facades\Log;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Omisai\Szamlazzhu\Response\AbstractResponse;
use Omisai\Szamlazzhu\SzamlaAgentException;

class InvoiceResponse extends AbstractResponse
{
    public const INVOICE_NOTIFICATION_SEND_FAILED = 56;

    protected string $userAccountUrl;

    protected float $outstandingDebtAmount;

    protected float $netPrice;

    protected float $grossAmount;

    protected string $invoiceNumber = '';

    protected int $invoiceIdentifier;

    protected string $pdfData;

    protected array $headers;

    protected function parseData()
    {
        if('array' != gettype($this->getData()) || empty($this->getData()) || empty($this->getData()['result']['headers'])) {
            return;
        }

        $this->headers = $this->getData()['result']['headers'];

        if (array_key_exists('szlahu_szamlaszam', $this->headers)) {
            $this->invoiceNumber = $this->headers['szlahu_szamlaszam'];
        }

        if (array_key_exists('szlahu_id', $this->headers)) {
            $this->invoiceIdentifier = $this->headers['szlahu_id'];
        }

        if (array_key_exists('szlahu_vevoifiokurl', $this->headers)) {
            $this->userAccountUrl = rawurldecode($this->headers['szlahu_vevoifiokurl']);
        }

        if (array_key_exists('szlahu_kintlevoseg', $this->headers)) {
            $this->outstandingDebtAmount = floatval($this->headers['szlahu_kintlevoseg']);
        }

        if (array_key_exists('szlahu_nettovegosszeg', $this->headers)) {
            $this->netPrice = floatval($this->headers['szlahu_nettovegosszeg']);
        }

        if (array_key_exists('szlahu_bruttovegosszeg', $this->headers)) {
            $this->grossAmount = floatval($this->headers['szlahu_bruttovegosszeg']);
        }

        if (array_key_exists('szlahu_error', $this->headers)) {
            $this->errorMessage = urldecode($this->headers['szlahu_error']);
        }

        if (array_key_exists('szlahu_error_code', $this->headers)) {
            $this->errorCode = intval($this->headers['szlahu_error_code']);
        }

        if (isset($this->getData()['result']['body'])) {
            $pdfFile = $this->getData()['result']['body'];
        } elseif ($this->isXmlResponse() && isset($this->getData()['result']['pdf'])) {
            $pdfFile = $this->getData()['result']['pdf'];
        } else {
            $pdfFile = '';
        }
        if ($this->isPdfResponse($this->getData()['result']) && !empty($pdfFile)) {
            $this->pdfFile = base64_decode($pdfFile);
        }

        if (!$this->hasError()) {
            $this->isSuccess = true;
            Log::channel('szamlazzhu')->debug('Invoice response is success', [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ]);
        } else {
            Log::channel('szamlazzhu')->debug('Invoice response is failed', [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ]);
        }

        if ($this->hasInvoiceNotificationSendError()) {
            Log::channel('szamlazzhu')->debug(SzamlaAgentException::INVOICE_NOTIFICATION_SEND_FAILED);
        }
    }

    protected function isPdfResponse(array $data): bool
    {
        if (isset($data['pdf'])) {
            return true;
        }

        if (isset($data['headers']['Content-Type']) && $data['headers']['Content-Type'] == 'application/pdf') {
            return true;
        }

        if (isset($data['headers']['Content-Disposition']) && stripos($data['headers']['Content-Disposition'], 'pdf') !== false) {
            return true;
        }

        return false;
    }

    public function hasError()
    {
        $result = false;
        if (!empty($this->errorMessage) || !empty($this->errorCode)) {
            $result = true;
        }
        if ($this->hasInvoiceNumber() && $this->hasInvoiceNotificationSendError()) {
            $result = false;
        }

        return $result;
    }

    public function hasInvoiceNumber(): bool
    {
        return !empty($this->invoiceNumber);
    }

    public function hasInvoiceNotificationSendError(): bool
    {
        if (self::INVOICE_NOTIFICATION_SEND_FAILED === $this->errorCode) {
            return true;
        }

        return false;
    }

    public function getUserAccountUrl(): string
    {
        return urldecode($this->userAccountUrl);
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function getOutstandingDebtAmount(): float
    {
        return $this->outstandingDebtAmount;
    }

    public function getNetPrice(): float
    {
        return $this->netPrice;
    }

    public function getGrossAmount(): float
    {
        return $this->grossAmount;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getDocumentNumber(): string
    {
        return $this->invoiceNumber;
    }
}
