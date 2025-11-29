<?php

namespace Omisai\Szamlazzhu;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Client\Response;

class SzamlaAgentRequest
{
    public const HTTP_OK = 200;

    public const CRLF = "\r\n";

    public const XML_BASE_URL = 'http://www.szamlazz.hu/';

    public const REQUEST_TIMEOUT = 30;

    public const MAX_NUMBER_OF_ATTACHMENTS = 5;

    public const CERTIFICATION_FILENAME = 'cacert.pem';

    /**
     * HU: Számlakészítéshez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/agent/xmlszamla.xsd
     */
    public const XML_SCHEMA_CREATE_INVOICE = 'xmlszamla';

    /**
     * HU: Számla sztornózásához használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/agentst/xmlszamlast.xsd
     */
    public const XML_SCHEMA_CREATE_REVERSE_INVOICE = 'xmlszamlast';

    /**
     * HU: Jóváírás rögzítéséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/agentkifiz/xmlszamlakifiz.xsd
     */
    public const XML_SCHEMA_PAY_INVOICE = 'xmlszamlakifiz';

    /**
     * HU: Számla adatok lekéréséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/agentxml/xmlszamlaxml.xsd
     */
    public const XML_SCHEMA_REQUEST_INVOICE_XML = 'xmlszamlaxml';

    /**
     * HU: Számla PDF lekéréséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/agentpdf/xmlszamlapdf.xsd
     */
    public const XML_SCHEMA_REQUEST_INVOICE_PDF = 'xmlszamlapdf';

    /**
     * HU: Nyugta készítéséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/nyugtacreate/xmlnyugtacreate.xsd
     */
    public const XML_SCHEMA_CREATE_RECEIPT = 'xmlnyugtacreate';

    /**
     * HU: Nyugta sztornóhoz használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/nyugtast/xmlnyugtast.xsd
     */
    public const XML_SCHEMA_CREATE_REVERSE_RECEIPT = 'xmlnyugtast';

    /**
     * HU: Nyugta kiküldéséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/nyugtasend/xmlnyugtasend.xsd
     */
    public const XML_SCHEMA_SEND_RECEIPT = 'xmlnyugtasend';

    /**
     * HU: Nyugta megjelenítéséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/nyugtaget/xmlnyugtaget.xsd
     */
    public const XML_SCHEMA_GET_RECEIPT = 'xmlnyugtaget';

    /**
     * HU: Adózó adatainak lekérdezéséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/taxpayer/xmltaxpayer.xsd
     */
    public const XML_SCHEMA_TAXPAYER = 'xmltaxpayer';

    /**
     * HU: Díjbekérő törléséhez használt XML séma
     *
     * @see https://www.szamlazz.hu/szamla/docs/xsds/dijbekerodel/xmlszamladbkdel.xsd
     */
    public const XML_SCHEMA_DELETE_PROFORMA = 'xmlszamladbkdel';

    public const REQUEST_AUTHORIZATION_BASIC_AUTH = 1;

    private SzamlaAgent $agent;

    private string $type;

    private object $entity;

    private string $xmlString;

    private string $xmlName;

    private string $xmlFilePath;

    private string $xmlDirectory;

    private string $fileName;

    private string $fieldName;

    private bool $cData = true;

    private int $requestTimeout;

    private CookieHandler $cookieHandler;

    public function __construct(SzamlaAgent $agent, CookieHandler $cookieHandler, string $type, object $entity)
    {
        $this->agent = $agent;
        $this->type = $type;
        $this->cookieHandler = $cookieHandler;
        $this->entity = $entity;
        $this->cData = true;
        if (null === $agent->getRequestTimeout()) {
            $this->requestTimeout = self::REQUEST_TIMEOUT;
        } else {
            $this->requestTimeout = $agent->getRequestTimeout();
        }
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function send(): Response
    {
        $this->buildXmlData();
        $response = $this->makeHttpRequest();

        return $response;
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    private function buildXmlData(): void
    {
        $agent = $this->agent;
        $this->setXmlFileData($this->getType());
        $this->setFileName(SzamlaAgentUtil::getXmlFileName('request', $this->getXmlName(), $agent, $this->getEntity()));

        Log::channel('szamlazzhu')->debug('Started to build the XML data.');
        $xmlArray = $this->getEntity()->buildXmlData($this);

        $xml = new SimpleXMLExtended($this->getXmlBase());
        $this->arrayToXML($xmlArray, $xml);
        try {
            $result = SzamlaAgentUtil::checkValidXml($xml->saveXML());
            if (!empty($result)) {
                throw new SzamlaAgentException(SzamlaAgentException::XML_NOT_VALID . " a {$result[0]->line}. sorban: {$result[0]->message}. ");
            }
            $formatXml = SzamlaAgentUtil::formatXml($xml);
            $this->setXmlString($formatXml->saveXML());
            Log::channel('szamlazzhu')->debug('The build of the XML data is completed.');
            if (($agent->isXmlFileSave() || $agent->isRequestXmlFileSave())) {
                $this->createXmlFile($formatXml);
            }
        } catch (\Exception $e) {
            try {
                $formatXml = SzamlaAgentUtil::formatXml($xml);
                $this->setXmlString($formatXml->saveXML());
                if (!empty($this->getXmlString())) {
                    $xmlString = $this->getXmlString();
                }
            } catch (\Exception $ex) {
                Log::channel('szamlazzhu')->debug('XML', ['data' => print_r($xmlString, true)]);
                throw new SzamlaAgentException(SzamlaAgentException::XML_DATA_BUILD_FAILED . ":  {$e->getMessage()} ");
            }
        }
    }

    /**
     * @throws SzamlaAgentException
     */
    private function setXmlFileData(string $type)
    {
        switch ($type) {
            case 'generateProforma':
            case 'generateInvoice':
            case 'generatePrePaymentInvoice':
            case 'generateFinalInvoice':
            case 'generateCorrectiveInvoice':
            case 'generateDeliveryNote':
                $fieldName = 'action-xmlagentxmlfile';
                $xmlName = self::XML_SCHEMA_CREATE_INVOICE;
                $xmlDirectory = 'agent';
                break;
            case 'generateReverseInvoice':
                $fieldName = 'action-szamla_agent_st';
                $xmlName = self::XML_SCHEMA_CREATE_REVERSE_INVOICE;
                $xmlDirectory = 'agentst';
                break;
            case 'payInvoice':
                $fieldName = 'action-szamla_agent_kifiz';
                $xmlName = self::XML_SCHEMA_PAY_INVOICE;
                $xmlDirectory = 'agentkifiz';
                break;
            case 'requestInvoiceData':
                $fieldName = 'action-szamla_agent_xml';
                $xmlName = self::XML_SCHEMA_REQUEST_INVOICE_XML;
                $xmlDirectory = 'agentxml';
                break;
            case 'requestInvoicePDF':
                $fieldName = 'action-szamla_agent_pdf';
                $xmlName = self::XML_SCHEMA_REQUEST_INVOICE_PDF;
                $xmlDirectory = 'agentpdf';
                break;
            case 'generateReceipt':
                $fieldName = 'action-szamla_agent_nyugta_create';
                $xmlName = self::XML_SCHEMA_CREATE_RECEIPT;
                $xmlDirectory = 'nyugtacreate';
                break;
            case 'generateReverseReceipt':
                $fieldName = 'action-szamla_agent_nyugta_storno';
                $xmlName = self::XML_SCHEMA_CREATE_REVERSE_RECEIPT;
                $xmlDirectory = 'nyugtast';
                break;
            case 'sendReceipt':
                $fieldName = 'action-szamla_agent_nyugta_send';
                $xmlName = self::XML_SCHEMA_SEND_RECEIPT;
                $xmlDirectory = 'nyugtasend';
                break;
            case 'requestReceiptData':
            case 'requestReceiptPDF':
                $fieldName = 'action-szamla_agent_nyugta_get';
                $xmlName = self::XML_SCHEMA_GET_RECEIPT;
                $xmlDirectory = 'nyugtaget';
                break;
            case 'getTaxPayer':
                $fieldName = 'action-szamla_agent_taxpayer';
                $xmlName = self::XML_SCHEMA_TAXPAYER;
                $xmlDirectory = 'taxpayer';
                break;
            case 'deleteProforma':
                $fieldName = 'action-szamla_agent_dijbekero_torlese';
                $xmlName = self::XML_SCHEMA_DELETE_PROFORMA;
                $xmlDirectory = 'dijbekerodel';
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::REQUEST_TYPE_NOT_EXISTS . ": {$type}");
        }

        $this->fieldName = $fieldName;
        $this->xmlName = $xmlName;
        $this->xmlDirectory = $xmlDirectory;
    }

    private function getXmlBase(): string
    {
        $xmlName = $this->getXmlName();
        $queryData = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $queryData .= '<' . $xmlName . ' xmlns="' . $this->getXmlNamespace($xmlName) . '" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="' . $this->getSchemaLocation($xmlName) . '">' . PHP_EOL;
        $queryData .= '</' . $xmlName . '>' . self::CRLF;

        return $queryData;
    }

    private function getXmlNamespace($xmlName): string
    {
        return self::XML_BASE_URL . "{$xmlName}";
    }

    private function getSchemaLocation($xmlName): string
    {
        return self::XML_BASE_URL . "szamla/{$xmlName} http://www.szamlazz.hu/szamla/docs/xsds/{$this->getXmlDirectory()}/{$xmlName}.xsd";
    }

    private function arrayToXML(array $xmlString, SimpleXMLExtended &$xmlFields): void
    {
        foreach ($xmlString as $key => $value) {
            if (is_array($value)) {
                $fieldKey = $key;
                if (strpos($key, 'item') !== false) {
                    $fieldKey = 'tetel';
                }
                if (strpos($key, 'note') !== false) {
                    $fieldKey = 'kifizetes';
                }
                $subNode = $xmlFields->addChild("$fieldKey");
                $this->arrayToXML($value, $subNode);
            } else {
                if (is_bool($value)) {
                    $value = ($value) ? 'true' : 'false';
                } elseif (! $this->isCData()) {
                    $value = htmlspecialchars("$value");
                }

                if ($this->isCData()) {
                    $xmlFields->addChildWithCData("$key", $value);
                } else {
                    $xmlFields->addChild("$key", $value);
                }
            }
        }
    }

    /**
     * @throws SzamlaAgentException
     * @throws \ReflectionException
     */
    private function createXmlFile(\DOMDocument $xml): void
    {
        $realPath = sprintf('%s/request/%s/%s', SzamlaAgent::XML_FILE_SAVE_PATH, $this->getXmlDirectory(), $this->getFileName());
        $isStored = Storage::disk('payment')->put($realPath, $xml->saveXML());
        if ($isStored) {
            Log::channel('szamlazzhu')->debug('XML file saved', ['path' => $realPath]);
        } else {
            Log::channel('szamlazzhu')->debug('XML file was not saved', ['path' => $realPath, 'xml' => $xml->saveXML()]);
            throw new SzamlaAgentException(SzamlaAgentException::XML_FILE_SAVE_FAILED);
        }

        $this->setXmlFilePath($realPath);
    }

    /**
     * @throws \Exception
     */
    private function makeHttpRequest(): Response
    {
        $client = Http::timeout($this->getRequestTimeout())
            ->withCookies(...$this->cookieHandler->getCookies())
            ->attach(
                $this->getFieldName(),
                $this->getXmlString(),
                $this->getFileName(),
            );

        if ($this->hasCertificationFile()) {
            $client->withOptions([
                'cert' => $this->getCertificationFile(),
            ]);
        }


        $response = $client->post(SzamlaAgent::API_ENDPOINT_URL);
        if ($this->hasAttachments()) {
            $attachments = $this->getEntity()->getAttachments();
            foreach ($attachments as $key => $attachment) {
                if (self::MAX_NUMBER_OF_ATTACHMENTS < ($key + 1)) {
                    break;
                }
                $client = $client->attach('attachfile' . $key, $attachment['content'], $attachment['name']);
            }
        }

        return $response;
    }

    public function getAgent(): SzamlaAgent
    {
        return $this->agent;
    }

    private function getType(): string
    {
        return $this->type;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    private function getXmlString(): string
    {
        return $this->xmlString;
    }

    private function setXmlString(string $xmlString): void
    {
        $this->xmlString = $xmlString;
    }

    private function isCData(): bool
    {
        return $this->cData;
    }

    public function getXmlName(): string
    {
        return $this->xmlName;
    }

    private function getFieldName(): string
    {
        return $this->fieldName;
    }

    private function getFileName(): string
    {
        return $this->fileName;
    }

    private function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getXmlFilePath()
    {
        return $this->xmlFilePath;
    }

    private function setXmlFilePath(string $xmlFilePath): void
    {
        $this->xmlFilePath = $xmlFilePath;
    }

    private function getXmlDirectory(): string
    {
        return $this->xmlDirectory;
    }

    private function hasAttachments()
    {
        $entity = $this->getEntity();
        if (is_a($entity, Invoice::class)) {
            return count($entity->getAttachments()) > 0;
        }

        return false;
    }

    private function getRequestTimeout(): int
    {
        return $this->requestTimeout;
    }

    public function getCertificationFile(): ?string
    {
        return Storage::disk('payment')->get(self::CERTIFICATION_FILENAME);
    }

    public function hasCertificationFile(): bool
    {
        return Storage::disk('payment')->exists(self::CERTIFICATION_FILENAME);
    }
}
