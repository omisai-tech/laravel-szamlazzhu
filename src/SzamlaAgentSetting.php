<?php

namespace Omisai\Szamlazzhu;

use Omisai\Szamlazzhu\Response\SzamlaAgentResponse;

class SzamlaAgentSetting
{
    public const DOWNLOAD_COPIES_COUNT = 1;

    public const API_KEY_LENGTH = 42;

    private ?string $username = '';

    private ?string $password = '';

    /**
     * @link https://www.szamlazz.hu/blog/2019/07/szamla_agent_kulcsok/
     */
    private ?string $apiKey = '';

    private bool $downloadPdf = true;

    private int $downloadCopiesCount = self::DOWNLOAD_COPIES_COUNT;

    /**
     * 1: RESULT_AS_TEXT - egyszerű szöveges válaszüzenetet vagy pdf-et ad vissza.
     * 2: RESULT_AS_XML - xml válasz, ha kérte a pdf-et az base64 kódolással benne van az xml-ben.
     */
    private int $responseType;

    /**
     * @example  WooCommerce, OpenCart, PrestaShop, Shoprenter, Superwebáruház, Drupal invoice Agent, etc.
     */
    private string $aggregator;

    private bool $guardian = false;

    private bool $invoiceItemIdentifier = false;

    private string $invoiceExternalId = '';

    private string $taxNumber = '';

    /**
     * Számla Agent beállítás létrehozása
     *
     * @param  string  $username  szamlazz.hu fiók felhasználónév vagy e-mail cím
     * @param  string  $password  szamlazz.hu fiók jelszava
     * @param  string  $apiKey  SzámlaAgent kulcs
     * @param  bool  $downloadPdf  szeretnénk-e PDF formátumban is megkapni a bizonylatot
     * @param  int  $copiesCount  bizonylat másolatok száma, ha PDF letöltést választottuk
     * @param  int  $responseType  válasz típusa (szöveges vagy XML)
     * @param  string  $aggregator  webáruházat futtató motor neve
     */
    public function __construct(?string $username = '', ?string $password = '', ?string $apiKey = '', bool $downloadPdf = true, int $copiesCount = self::DOWNLOAD_COPIES_COUNT, int $responseType = SzamlaAgentResponse::RESULT_AS_TEXT, string $aggregator = '')
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setApiKey($apiKey);
        $this->setDownloadPdf($downloadPdf);
        $this->setDownloadCopiesCount($copiesCount);
        $this->setResponseType($responseType);
        $this->setAggregator($aggregator);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * Visszaadja a Számla Agent kéréshez használt kulcsot
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @link  https://www.szamlazz.hu/blog/2019/07/szamla_agent_kulcsok/
     */
    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function isDownloadPdf(): bool
    {
        return $this->downloadPdf;
    }

    public function setDownloadPdf(bool $downloadPdf): void
    {
        $this->downloadPdf = $downloadPdf;
    }

    public function getDownloadCopiesCount(): int
    {
        return $this->downloadCopiesCount;
    }

    public function setDownloadCopiesCount(int $downloadCopiesCount): void
    {
        $this->downloadCopiesCount = $downloadCopiesCount;
    }

    public function getResponseType(): int
    {
        return $this->responseType;
    }

    /**
     * Számla Agent válasz típusának beállítása
     *
     * 1: RESULT_AS_TEXT - egyszerű szöveges válaszüzenetet vagy pdf-et ad vissza.
     * 2: RESULT_AS_XML  - xml válasz, ha kérted a pdf-et az base64 kódolással benne van az xml-ben.
     */
    public function setResponseType(int $responseType)
    {
        $this->responseType = $responseType;
    }

    public function getAggregator(): string
    {
        return $this->aggregator;
    }

    /**
     * Ha bérelhető webáruházat üzemeltetsz, beállítja a webáruházat futtató motor nevét.
     * Ha nem vagy benne biztos, akkor kérd ügyfélszolgálatunk segítségét (info@szamlazz.hu).
     * (pl. WooCommerce, OpenCart, PrestaShop, Shoprenter, Superwebáruház, Drupal invoice Agent, stb.)
     */
    public function setAggregator(string $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function getGuardian(): bool
    {
        return $this->guardian;
    }

    public function setGuardian(bool $guardian): void
    {
        $this->guardian = $guardian;
    }

    public function isInvoiceItemIdentifier(): bool
    {
        return $this->invoiceItemIdentifier;
    }

    public function setInvoiceItemIdentifier(bool $invoiceItemIdentifier): void
    {
        $this->invoiceItemIdentifier = $invoiceItemIdentifier;
    }

    public function getInvoiceExternalId(): string
    {
        return $this->invoiceExternalId;
    }

    public function setInvoiceExternalId(string $invoiceExternalId): void
    {
        $this->invoiceExternalId = $invoiceExternalId;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $settings = ['felhasznalo', 'jelszo', 'szamlaagentkulcs'];

        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_INVOICE:
                $data = $this->buildFieldsData($request, array_merge($settings, ['eszamla', 'szamlaLetoltes', 'szamlaLetoltesPld', 'valaszVerzio', 'aggregator', 'szamlaKulsoAzon']));
                break;
            case $request::XML_SCHEMA_DELETE_PROFORMA:
                $data = $this->buildFieldsData($request, $settings);
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_INVOICE:
                $data = $this->buildFieldsData($request, array_merge($settings, ['eszamla', 'szamlaLetoltes', 'szamlaLetoltesPld', 'aggregator', 'valaszVerzio', 'szamlaKulsoAzon']));
                break;
            case $request::XML_SCHEMA_PAY_INVOICE:
                $data = $this->buildFieldsData($request, array_merge($settings, ['szamlaszam', 'adoszam', 'additiv', 'aggregator', 'valaszVerzio']));
                break;
            case $request::XML_SCHEMA_REQUEST_INVOICE_XML:
                $data = $this->buildFieldsData($request, array_merge($settings, ['szamlaszam', 'rendelesSzam', 'pdf']));
                break;
            case $request::XML_SCHEMA_REQUEST_INVOICE_PDF:
                $data = $this->buildFieldsData($request, array_merge($settings, ['szamlaszam', 'rendelesSzam', 'valaszVerzio', 'szamlaKulsoAzon']));
                break;
            case $request::XML_SCHEMA_CREATE_RECEIPT:
            case $request::XML_SCHEMA_CREATE_REVERSE_RECEIPT:
            case $request::XML_SCHEMA_GET_RECEIPT:
                $data = $this->buildFieldsData($request, array_merge($settings, ['pdfLetoltes']));
                break;
            case $request::XML_SCHEMA_SEND_RECEIPT:
            case $request::XML_SCHEMA_TAXPAYER:
                $data = $this->buildFieldsData($request, $settings);
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::XML_SCHEMA_TYPE_NOT_EXISTS.": {$request->getXmlName()}");
        }

        return $data;
    }

    /**
     * @throws SzamlaAgentException
     */
    private function buildFieldsData(SzamlaAgentRequest $request, array $fields): array
    {
        $data = [];

        foreach ($fields as $key) {
            switch ($key) {
                case 'felhasznalo':
                    $value = $this->getUsername();
                    break;
                case 'jelszo':
                    $value = $this->getPassword();
                    break;
                case 'szamlaagentkulcs':
                    $value = $this->getApiKey();
                    break;
                case 'szamlaLetoltes':
                case 'pdf':
                case 'pdfLetoltes':
                    $value = $this->isDownloadPdf();
                    break;
                case 'szamlaLetoltesPld':
                    $value = $this->getDownloadCopiesCount();
                    break;
                case 'valaszVerzio':
                    $value = $this->getResponseType();
                    break;
                case 'aggregator':
                    $value = $this->getAggregator();
                    break;
                case 'guardian':
                    $value = $this->getGuardian();
                    break;
                case 'cikkazoninvoice':
                    $value = $this->isInvoiceItemIdentifier();
                    break;
                case 'szamlaKulsoAzon':
                    $value = $this->getInvoiceExternalId();
                    break;
                case 'eszamla':
                    $value = $request->getEntity()->getHeader()->isEInvoice();
                    break;
                case 'additiv':
                    $value = $request->getEntity()->isAdditive();
                    break;
                case 'szamlaszam':
                    $value = $request->getEntity()->getHeader()->getInvoiceNumber();
                    break;
                case 'rendelesSzam':
                    $value = $request->getEntity()->getHeader()->getOrderNumber();
                    break;
                case 'adoszam':
                    $value = $this->getTaxNumber();
                    break;
                default:
                    throw new SzamlaAgentException(SzamlaAgentException::XML_KEY_NOT_EXISTS.": {$key}");
            }

            if (isset($value)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
