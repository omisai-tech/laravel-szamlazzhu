<?php

namespace Omisai\Szamlazzhu\Response;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\SimpleXMLExtended;
use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

abstract class AbstractResponse
{
    public const RESULT_AS_TEXT = 1;

    public const RESULT_AS_XML = 2;

    /**
     * The response given by the Számla Agent will be in XML format
     * as returned by the NAV Online Invoice System
     *
     * @see https://onlineszamla.nav.gov.hu/dokumentaciok
     */
    public const RESULT_AS_TAXPAYER_XML = 3;

    protected SzamlaAgent $agent;

    protected Response $httpResponse;

    protected int $httpCode;

    protected \SimpleXMLElement $xmlData;

    protected string $pdfFile = '';

    protected string $content;

    protected string $xmlSchemaType;

    protected ?int $errorCode = null;

    protected string $errorMessage = '';

    protected bool $isSuccess = false;

    public function __construct(SzamlaAgent $agent, Response $httpResponse)
    {
        $this->agent = $agent;
        $this->httpResponse = $httpResponse;
        $this->agent->getCookieHandler()->setCookieFile($httpResponse);

        $this->parseHttpResponse();
    }

    public function parseHttpResponse()
    {
        $this->xmlSchemaType = $this->httpResponse->header('Schema-Type');
        $this->validateHttpResponse();
        $this->httpCode = $this->httpResponse->status();

        if ($this->isXmlResponse()) {
            $this->buildResponseXmlData();
        } else {
            $this->buildResponseTextData();
        }

        $this->parseData();
        if ($this->agent->isXmlFileSave() || $this->agent->isResponseXmlFileSave()) {
            $this->createXmlFile();
        }

        if ($this->hasError()) {
            throw new SzamlaAgentException(SzamlaAgentException::AGENT_ERROR . ": [{$this->errorCode}], {$this->errorMessage}");
        }


        Log::channel('szamlazzhu')->debug('The Agent call succesfully ended.');
        if ($this->isTaxPayerXmlResponse()) {
            throw new SzamlaAgentException(SzamlaAgentException::TAX_PAYER_RESPONSE);
        }

        if (!$this->agent->isDownloadPdf()) {
            $this->content = $this->httpResponse->body();

            return;
        }

        try {
            if (empty($this->pdfFile) && !in_array($this->agent->getRequest()->getXmlName(), [SzamlaAgentRequest::XML_SCHEMA_SEND_RECEIPT, SzamlaAgentRequest::XML_SCHEMA_PAY_INVOICE])) {
                throw new SzamlaAgentException(SzamlaAgentException::DOCUMENT_DATA_IS_MISSING);
            }

            if (!empty($this->pdfFile)) {
                if ($this->agent->isPdfFileSaveable()) {
                    $realPath = $this->getPdfFileName();
                    $isSaved = Storage::disk('payment')->put($realPath, $this->pdfFile);

                    if ($isSaved) {
                        Log::channel('szamlazzhu')->debug(SzamlaAgentException::PDF_FILE_SAVE_SUCCESS, ['path' => $realPath]);
                    } else {
                        $errorMessage = SzamlaAgentException::PDF_FILE_SAVE_FAILED . ': ' . SzamlaAgentException::FILE_CREATION_FAILED;
                        Log::channel('szamlazzhu')->debug($errorMessage);
                        throw new SzamlaAgentException($errorMessage);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::channel('szamlazzhu')->debug(SzamlaAgentException::PDF_FILE_SAVE_FAILED, ['error_message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @throws SzamlaAgentException
     */
    private function validateHttpResponse(): void
    {
        if (empty($this->httpResponse) || $this->httpResponse === null) {
            throw new SzamlaAgentException(SzamlaAgentException::AGENT_RESPONSE_IS_EMPTY);
        }

        if (!empty($this->httpResponse->header('szlahu_down'))) {
            throw new SzamlaAgentException(SzamlaAgentException::SYSTEM_DOWN, 500);
        }

        if (empty($this->httpResponse->headers())) {
            throw new SzamlaAgentException(SzamlaAgentException::AGENT_RESPONSE_NO_HEADER);
        }

        if (empty($this->httpResponse->body())) {
            throw new SzamlaAgentException(SzamlaAgentException::AGENT_RESPONSE_NO_CONTENT);
        }

        if ($this->isAgentInvoiceResponse()) {
            $keys = implode(',', array_keys($this->httpResponse->headers()));
            if (!preg_match('/(szlahu_)/', $keys, $matches)) {
                throw new SzamlaAgentException(SzamlaAgentException::NO_SZLAHU_KEY_IN_HEADER);
            }
        }
    }

    public function isAgentInvoiceResponse(): bool
    {
        return Document::DOCUMENT_TYPE_INVOICE === $this->xmlSchemaType;
    }

    public function isAgentProformaResponse(): bool
    {
        return Document::DOCUMENT_TYPE_PROFORMA === $this->xmlSchemaType;
    }

    public function isAgentReceiptResponse(): bool
    {
        return Document::DOCUMENT_TYPE_RECEIPT === $this->xmlSchemaType;
    }

    public function isTaxPayerResponse(): bool
    {
        return $this->xmlSchemaType === 'taxpayer';
    }

    protected function isXmlResponse(): bool
    {
        return self::RESULT_AS_XML === $this->agent->getResponseType();
    }

    protected function buildResponseXmlData(): void
    {
        if ($this->isTaxPayerXmlResponse()) {
            $xmlData = new SimpleXMLExtended($this->httpResponse->body());
            $xmlData = SzamlaAgentUtil::removeNamespaces($xmlData);
        } else {
            $xmlData = new \SimpleXMLElement($this->httpResponse->body());
            $headers = $xmlData->addChild('headers');
            foreach ($this->httpResponse->headers() as $key => $header) {
                $headers->addChild($key, $header[0]);
            }
        }

        $this->xmlData = $xmlData;
    }

    public function isTaxPayerXmlResponse(): bool
    {
        $result = true;
        if ('taxpayer' !== $this->xmlSchemaType) {
            return false;
        }

        if (self::RESULT_AS_TAXPAYER_XML !== $this->agent->getResponseType()) {
            $result = false;
        }

        return $result;
    }

    private function buildResponseTextData()
    {
        $xmlData = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response></response>');
        $headers = $xmlData->addChild('headers');

        foreach ($this->httpResponse->headers() as $key => $value) {
            $headers->addChild($key, $value[0]);
        }

        if ($this->isAgentReceiptResponse()) {
            $content = base64_encode($this->httpResponse->body());
        } else {
            $content = ($this->agent->isDownloadPdf()) ? base64_encode($this->httpResponse->body()) : $this->httpResponse->body();
        }

        $xmlData->addChild('body', $content);

        $this->xmlData = $xmlData;
    }

    /**
     * Method is overwritten in child Response
     */
    protected function parseData() {}

    protected function getData(): ?array
    {
        $rawData = [];
        if (!$this->isTaxPayerXmlResponse()) {
            $rawData['documentNumber'] = $this->getDocumentNumber();
        }

        if (!empty($this->xmlData)) {
            $rawData['result'] = $this->xmlData;
        } else {
            $rawData['result'] = $this->content;
        }

        $jsonString = json_encode($rawData);
        if (empty($jsonString) || !SzamlaAgentUtil::isValidJSON($jsonString)) {
            throw new SzamlaAgentException(SzamlaAgentException::INVALID_JSON);
        }

        return json_decode($jsonString, true);
    }


    /**
     * @throws SzamlaAgentException
     * @throws \ReflectionException
     */
    private function createXmlFile(): void
    {
        if ($this->isTaxPayerXmlResponse()) {
            $xml = SzamlaAgentUtil::formatResponseXml($this->httpResponse->body());
        } else {
            $xml = SzamlaAgentUtil::formatXml($this->xmlData);
        }

        $name = '';
        if ($this->hasError()) {
            $name = 'error-';
        }
        $name .= strtolower($this->agent->getRequest()->getXmlName());

        switch ($this->agent->getResponseType()) {
            case self::RESULT_AS_XML:
            case self::RESULT_AS_TAXPAYER_XML:
                $postfix = '-xml';
                break;
            case self::RESULT_AS_TEXT:
                $postfix = '-text';
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::RESPONSE_TYPE_NOT_EXISTS . $this->agent->getResponseType());
        }

        $filename = SzamlaAgentUtil::getXmlFileName('response', $name . $postfix, $this->agent->getRequest()->getEntity());
        $realPath = sprintf('%s/response/%s', SzamlaAgent::XML_FILE_SAVE_PATH, $filename);
        $isXmlSaved = Storage::disk('payment')->put($realPath, $xml->saveXML());
        if ($isXmlSaved) {
            Log::channel('szamlazzhu')->debug('XML file saved', ['path' => $realPath]);
        } else {
            Log::channel('szamlazzhu')->debug('XML file was not saved', ['path' => $realPath, 'xml' => $xml->saveXML()]);
            throw new SzamlaAgentException(SzamlaAgentException::XML_FILE_SAVE_FAILED);
        }
    }

    public function hasError()
    {
        $result = false;
        if (!empty($this->errorMessage) || !empty($this->errorCode)) {
            $result = true;
        }

        return $result;
    }

    public function isSuccess()
    {
        return $this->isSuccess && !$this->hasError();
    }

    /**
     * Method is overwritten in child Response
     */
    public function getDocumentNumber(): ?string
    {
        return null;
    }

    public function getPdfFileName(bool $withPath = true): string
    {
        $header = $this->agent->getRequestEntityHeader();

        if ($header instanceof InvoiceHeader && $header->isPreviewPdf()) {
            $name = '';
            $entity = $this->agent->getRequestEntity();
            if ($entity != null) {
                $name = (new \ReflectionClass($entity))->getShortName();
            }

            $documentNumber = sprintf('preview-%s-%s', $name, SzamlaAgentUtil::getDateTimeWithMilliseconds());
        } else {
            $documentNumber = $this->getDocumentNumber();
        }

        if ($withPath) {
            return $this->getPdfFilePath(sprintf('%s.pdf', $documentNumber));
        } else {
            return sprintf('%s.pdf', $documentNumber);
        }
    }

    protected function getPdfFilePath($pdfFileName): string
    {
        return sprintf('%s/%s', SzamlaAgent::PDF_FILE_SAVE_PATH, $pdfFileName);
    }

    public function getPdfFile(): string
    {
        return $this->pdfFile;
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    protected function getXmlData(): \SimpleXMLElement
    {
        return $this->xmlData;
    }

    public function getXmlSchemaType(): string
    {
        return $this->xmlSchemaType;
    }

    protected function getContent(): string
    {
        return $this->content;
    }
}
