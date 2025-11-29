<?php

namespace Omisai\Szamlazzhu;

use Illuminate\Support\Facades\Log;
use Omisai\Szamlazzhu\Document\DeliveryNote;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Document\Invoice\CorrectiveInvoice;
use Omisai\Szamlazzhu\Document\Invoice\FinalInvoice;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Document\Invoice\PrePaymentInvoice;
use Omisai\Szamlazzhu\Document\Invoice\ReverseInvoice;
use Omisai\Szamlazzhu\Document\Proforma;
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\Document\Receipt\ReverseReceipt;
use Omisai\Szamlazzhu\Header\DocumentHeader;
use Omisai\Szamlazzhu\Response\AbstractResponse;
use Omisai\Szamlazzhu\Response\InvoiceResponse;
use Omisai\Szamlazzhu\Response\ReceiptResponse;
use Omisai\Szamlazzhu\Response\ProformaDeletionResponse;

class SzamlaAgent
{
    public const API_ENDPOINT_URL = 'https://www.szamlazz.hu/szamla/';

    public const PDF_FILE_SAVE_PATH = 'pdf';

    public const XML_FILE_SAVE_PATH = 'xmls';

    private SzamlaAgentSetting $setting;

    private SzamlaAgentRequest $request;

    private  ?int $requestTimeout = null;

    private AbstractResponse $response;

    /**
     * @var SzamlaAgent[]
     */
    protected static array $agents = [];

    protected array $customHTTPHeaders = [];

    protected bool $isXmlFileSaveable = false;

    protected bool $isRequestXmlFileSaveable = false;

    protected bool $isResponseXmlFileSaveable = false;

    protected bool $isPdfFileSaveable = true;

    private CookieHandler $cookieHandler;

    private bool $singleton = true;

    protected function __construct(?string $username, ?string $password, ?string $apiKey, bool $downloadPdf, int $responseType = AbstractResponse::RESULT_AS_XML, string $aggregator = '')
    {
        $this->setting = new SzamlaAgentSetting($username, $password, $apiKey, $downloadPdf, SzamlaAgentSetting::DOWNLOAD_COPIES_COUNT, $responseType, $aggregator);
        $this->cookieHandler = new CookieHandler();
        Log::channel('szamlazzhu')->debug(sprintf('Számla Agent initialization is complete ($username: %s, apiKey: %s)', $username, $apiKey));

        $this->isPdfFileSaveable = $downloadPdf;
        $this->isXmlFileSaveable = config('szamlazzhu.xml.file_save', false);
        $this->isRequestXmlFileSaveable = config('szamlazzhu.xml.request_file_save', false);
        $this->isResponseXmlFileSaveable = config('szamlazzhu.xml.response_file_save', false);
    }

    /**
     * @deprecated Not recommended the username/password authetnication mode
     * use instead the SzamlaAgent::createWithAPIkey($apiKey)
     */
    public static function createWithUsername(string $username, string $password, bool $downloadPdf = true)
    {
        $index = self::getHash($username);

        $agent = null;
        if (isset(self::$agents[$index])) {
            $agent = self::$agents[$index];
        }

        if ($agent === null) {
            return self::$agents[$index] = new self($username, $password, null, $downloadPdf);
        } else {
            return $agent;
        }
    }

    /**
     * API key is the recommended authentication mode
     */
    public static function createWithAPIkey(string $apiKey, bool $downloadPdf = true, int $responseType = AbstractResponse::RESULT_AS_XML, string $aggregator = '')
    {
        $index = self::getHash($apiKey);

        $agent = null;
        if (isset(self::$agents[$index])) {
            $agent = self::$agents[$index];
        }

        if ($agent === null) {
            return self::$agents[$index] = new self(null, null, $apiKey, $downloadPdf, $responseType, $aggregator);
        } else {
            return $agent;
        }
    }

    /**
     * @param  string  $instanceId : email, username or api key
     * @throws SzamlaAgentException
     */
    public static function get($instanceId): SzamlaAgent
    {
        $index = self::getHash($instanceId);
        $agent = self::$agents[$index];

        if ($agent === null) {
            if (strpos($instanceId, '@') === false && strlen($instanceId) == SzamlaAgentSetting::API_KEY_LENGTH) {
                throw new SzamlaAgentException(SzamlaAgentException::NO_AGENT_INSTANCE_WITH_APIKEY);
            } else {
                throw new SzamlaAgentException(SzamlaAgentException::NO_AGENT_INSTANCE_WITH_USERNAME);
            }
        }

        return $agent;
    }

    protected static function getHash($username): string
    {
        return hash('sha1', $username);
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    private function sendRequest(SzamlaAgentRequest $request): AbstractResponse
    {
        $this->setRequest($request);
        if (Proforma::class === $request->getEntity()::class) {
            return new ProformaDeletionResponse($this, $request->send());
        } elseif (Invoice::class === $request->getEntity()::class) {
            return new InvoiceResponse($this, $request->send());
        } elseif (Receipt::class === $request->getEntity()::class) {
            return new ReceiptResponse($this, $request->send());
        }

        throw new SzamlaAgentException("Cannot process response.");
    }

    /**
     * HU: Bizonylat elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateDocument(string $type, Document $document): AbstractResponse
    {
        $request = new SzamlaAgentRequest($this, $this->cookieHandler, $type, $document);

        return $this->sendRequest($request);
    }

    /**
     * HU: Számla elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateInvoice(Invoice $invoice): InvoiceResponse
    {
        return $this->generateDocument('generateInvoice', $invoice);
    }

    /**
     * HU: Előlegszámla elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generatePrePaymentInvoice(PrePaymentInvoice $invoice): InvoiceResponse
    {
        return $this->generateInvoice($invoice);
    }

    /**
     * HU: Végszámla elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateFinalInvoice(FinalInvoice $invoice): InvoiceResponse
    {
        return $this->generateInvoice($invoice);
    }

    /**
     * HU: Helyesbítő számla elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateCorrectiveInvoice(CorrectiveInvoice $invoice): InvoiceResponse
    {
        return $this->generateInvoice($invoice);
    }

    /**
     * HU: Nyugta elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateReceipt(Receipt $receipt): ReceiptResponse
    {
        return $this->generateDocument('generateReceipt', $receipt);
    }

    /**
     * HU: Számla jóváírás rögzítése
     *
     * @throws SzamlaAgentException
     */
    public function payInvoice(Invoice $invoice): InvoiceResponse
    {
        if ($this->getResponseType() != AbstractResponse::RESULT_AS_TEXT) {
            $message = 'Helytelen beállítási kísérlet a számla kifizetettségi adatok elküldésénél: a kérésre adott válaszverziónak TEXT formátumúnak kell lennie!';
            Log::channel('szamlazzhu')->warning($message);
        }
        $this->setResponseType(AbstractResponse::RESULT_AS_TEXT);

        return $this->generateDocument('payInvoice', $invoice);
    }

    /**
     * HU: Nyugta elküldése
     *
     * @throws SzamlaAgentException
     */
    public function sendReceipt(Receipt $receipt): ReceiptResponse
    {
        return $this->generateDocument('sendReceipt', $receipt);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function getInvoiceData(string $data, int $type = Invoice::FROM_INVOICE_NUMBER, $downloadPdf = false): InvoiceResponse
    {
        $invoice = new Invoice();

        if ($type == Invoice::FROM_INVOICE_NUMBER) {
            $invoice->getHeader()->setInvoiceNumber($data);
        } else {
            $invoice->getHeader()->setOrderNumber($data);
        }

        if ($this->getResponseType() !== AbstractResponse::RESULT_AS_XML) {
            $message = 'Helytelen beállítási kísérlet a számla adatok lekérdezésénél: Számla adatok letöltéséhez a kérésre adott válasznak xml formátumúnak kell lennie!';
            Log::channel('szamlazzhu')->warning($message);
        }

        $this->setDownloadPdf($downloadPdf);
        $this->setResponseType(AbstractResponse::RESULT_AS_XML);

        return $this->generateDocument('requestInvoiceData', $invoice);
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function getInvoicePdf(string $data, int $type = Invoice::FROM_INVOICE_NUMBER): InvoiceResponse
    {
        $invoice = new Invoice();

        if ($type == Invoice::FROM_INVOICE_NUMBER) {
            $invoice->getHeader()->setInvoiceNumber($data);
        } elseif ($type == Invoice::FROM_INVOICE_EXTERNAL_ID) {
            if (!empty($data)) {
                throw new SzamlaAgentException(SzamlaAgentException::INVOICE_EXTERNAL_ID_IS_EMPTY);
            }
            $this->getSetting()->setInvoiceExternalId($data);
        } else {
            $invoice->getHeader()->setOrderNumber($data);
        }

        if (!$this->isDownloadPdf()) {
            $message = 'Helytelen beállítási kísérlet a számla PDF lekérdezésénél: Számla letöltéshez a "downloadPdf" paraméternek "true"-nak kell lennie!';
            Log::channel('szamlazzhu')->warning($message);
        }
        $this->setDownloadPdf(true);

        return $this->generateDocument('requestInvoicePDF', $invoice);
    }

    /**
     * @return bool
     */
    public function isExistsInvoiceByExternalId(string|int $invoiceExternalId): bool
    {
        try {
            $result = $this->getInvoicePdf($invoiceExternalId, Invoice::FROM_INVOICE_EXTERNAL_ID);
            if ($result->isSuccess() && !empty($result->getDocumentNumber())) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function getReceiptData(string $receiptNumber): ReceiptResponse
    {
        return $this->generateDocument('requestReceiptData', new Receipt($receiptNumber));
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function getReceiptPdf(string $receiptNumber): ReceiptResponse
    {
        return $this->generateDocument('requestReceiptPDF', new Receipt($receiptNumber));
    }

    /**
     * HU: A választ a NAV Online Számla XML formátumában kapjuk vissza
     * EN: The response will be returned in the XML format of NAV Online Számla
     *
     * @throws SzamlaAgentException
     */
    public function getTaxPayer(string $taxPayerId): AbstractResponse
    {
        $request = new SzamlaAgentRequest($this, $this->cookieHandler, 'getTaxPayer', new TaxPayer($taxPayerId));
        $this->setResponseType(AbstractResponse::RESULT_AS_TAXPAYER_XML);

        return $this->sendRequest($request);
    }

    /**
     * HU: Sztornó számla elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateReverseInvoice(ReverseInvoice $invoice): InvoiceResponse
    {
        return $this->generateDocument('generateReverseInvoice', $invoice);
    }

    /**
     * HU: Sztornó nyugta elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateReverseReceipt(ReverseReceipt $receipt): ReceiptResponse
    {
        return $this->generateDocument('generateReverseReceipt', $receipt);
    }

    /**
     * HU: Díjbekérő elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateProforma(Proforma $proforma): InvoiceResponse
    {
        return $this->generateDocument('generateProforma', $proforma);
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function getDeleteProforma(string $data, int $type = Proforma::FROM_INVOICE_NUMBER): ProformaDeletionResponse
    {
        $proforma = new Proforma();

        if ($type == Proforma::FROM_INVOICE_NUMBER) {
            $proforma->getHeader()->setInvoiceNumber($data);
        } else {
            $proforma->getHeader()->setOrderNumber($data);
        }

        $this->setResponseType(AbstractResponse::RESULT_AS_XML);
        $this->setDownloadPdf(false);

        return $this->generateDocument('deleteProforma', $proforma);
    }

    /**
     * HU: Szállítólevél elkészítése
     *
     * @throws SzamlaAgentException
     */
    public function generateDeliveryNote(DeliveryNote $deliveryNote): AbstractResponse
    {
        return $this->generateDocument('generateDeliveryNote', $deliveryNote);
    }

    public function getSetting(): SzamlaAgentSetting
    {
        return $this->setting;
    }

    public function setSetting(SzamlaAgentSetting $setting): void
    {
        $this->setting = $setting;
    }

    /**
     * @return SzamlaAgent[]
     */
    public static function getAgents(): array
    {
        return self::$agents;
    }

    public function getUsername(): ?string
    {
        return $this->getSetting()->getUsername();
    }

    /**
     * The username is the email address or a specificied username
     * used on the https://www.szamlazz.hu/szamla/login website.
     */
    public function setUsername(?string $username): self
    {
        $this->getSetting()->setUsername($username);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->getSetting()->getPassword();
    }

    /**
     * The password is used on the https://www.szamlazz.hu/szamla/login website.
     */
    public function setPassword(?string $password): self
    {
        $this->getSetting()->setPassword($password);

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->getSetting()->getApiKey();
    }

    /**
     * @link Docs: https://www.szamlazz.hu/blog/2019/07/szamla_agent_kulcsok/
     */
    public function setApiKey(?string $apiKey): self
    {
        $this->getSetting()->setApiKey($apiKey);

        return $this;
    }

    public function isDownloadPdf(): bool
    {
        return $this->getSetting()->isDownloadPdf();
    }

    public function setDownloadPdf(bool $downloadPdf): self
    {
        $this->getSetting()->setDownloadPdf($downloadPdf);

        return $this;
    }

    public function getDownloadCopiesCount(): int
    {
        return $this->getSetting()->getDownloadCopiesCount();
    }

    /**
     * HU: Amennyiben az Agenttel papír alapú számlát készítesz és kéred a számlaletöltést ($downloadPdf = true),
     * akkor opcionálisan megadható, hogy nem csak a számla eredeti példányát kéred, hanem a másolatot is egyetlen pdf-ben.
     *
     * EN: If you use Agent to create a paper invoice and request an invoice download ($downloadPdf = true),
     * you can optionally specify that you request not only the original invoice, but also a copy in a single pdf file.
     */
    public function setDownloadCopiesCount(int $downloadCopiesCount): self
    {
        $this->getSetting()->setDownloadCopiesCount($downloadCopiesCount);

        return $this;
    }

    public function getResponseType(): int
    {
        return $this->getSetting()->getResponseType();
    }

    /**
     * HU:
     * 1: RESULT_AS_TEXT - egyszerű szöveges válaszüzenetet vagy pdf-et ad vissza.
     * 2: RESULT_AS_XML  - xml válasz, ha kérted a pdf-et az base64 kódolással benne van az xml-ben.
     * EN:
     * 1: RESULT_AS_TEXT - return a plain text response message or pdf.
     * 2: RESULT_AS_XML  - xml response, if you requested the pdf, then it is included in the xml with base64 encoding.
     */
    public function setResponseType(int $responseType): self
    {
        $this->getSetting()->setResponseType($responseType);

        return $this;
    }

    public function getAggregator(): string
    {
        return $this->getSetting()->getAggregator();
    }

    /**
     * @example WooCommerce, OpenCart, PrestaShop, Shoprenter, Superwebáruház, Drupal invoice Agent, etc.
     */
    public function setAggregator(string $aggregator): self
    {
        $this->getSetting()->setAggregator($aggregator);

        return $this;
    }

    public function getGuardian(): bool
    {
        return $this->getSetting()->getGuardian();
    }

    public function setGuardian(bool $guardian): self
    {
        $this->getSetting()->setGuardian($guardian);

        return $this;
    }

    public function getInvoiceExternalId(): string
    {
        return $this->getSetting()->getInvoiceExternalId();
    }

    /**
     * HU: A számlát a külső rendszer (Számla Agentet használó rendszer) ezzel az adattal azonosítja.
     * (a számla adatai később ezzel az adattal is lekérdezhetők lesznek)
     *
     * EN: The external system (the system using the Számla Agent)
     * identifies the invoice with this data.
     * (the invoice data will also be retrieved later with this data)
     */
    public function setInvoiceExternalId(string $invoiceExternalId): self
    {
        $this->getSetting()->setInvoiceExternalId($invoiceExternalId);

        return $this;
    }

    public function getRequest(): SzamlaAgentRequest
    {
        return $this->request;
    }

    public function setRequest(SzamlaAgentRequest $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getResponse(): AbstractResponse
    {
        return $this->response;
    }

    public function setResponse(AbstractResponse $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getCustomHTTPHeaders(): array
    {
        return $this->customHTTPHeaders;
    }

    public function addCustomHTTPHeader(string $key, string $value): self
    {
        if (!empty($key)) {
            $this->customHTTPHeaders[$key] = $value;
        } else {
            Log::channel('szamlazzhu')->warning('The custom HTTP header key cannot be blank', [
                'key' => $key,
                'value' => $value,
            ]);
        }

        return $this;
    }

    public function removeCustomHTTPHeader(string $key): self
    {
        if (!empty($key)) {
            unset($this->customHTTPHeaders[$key]);
        }

        return $this;
    }

    public function isPdfFileSaveable(): bool
    {
        return $this->isPdfFileSaveable;
    }

    public function isXmlFileSave(): bool
    {
        return $this->isXmlFileSaveable;
    }

    public function isNotXmlFileSave(): bool
    {
        return !$this->isXmlFileSave();
    }

    public function setXmlFileSave(bool $isXmlFileSaveable): void
    {
        $this->isXmlFileSaveable = $isXmlFileSaveable;
    }

    public function isRequestXmlFileSave(): bool
    {
        return $this->isRequestXmlFileSaveable;
    }

    public function isNotRequestXmlFileSave(): bool
    {
        return !$this->isRequestXmlFileSave();
    }

    public function setRequestXmlFileSave(bool $isRequestXmlFileSaveable): void
    {
        $this->isRequestXmlFileSaveable = $isRequestXmlFileSaveable;
    }

    public function isResponseXmlFileSave(): bool
    {
        return $this->isResponseXmlFileSaveable;
    }

    public function setResponseXmlFileSave(bool $isResponseXmlFileSaveable): void
    {
        $this->isResponseXmlFileSaveable = $isResponseXmlFileSaveable;
    }

    /**
     * @return Document|object
     */
    public function getRequestEntity()
    {
        return $this->getRequest()->getEntity();
    }

    /**
     * @return DocumentHeader|null
     */
    public function getRequestEntityHeader()
    {
        $header = null;

        $request = $this->getRequest();
        $entity = $request->getEntity();

        if ($entity != null && $entity instanceof Invoice) {
            $header = $entity->getHeader();
        }

        return $header;
    }

    public function getRequestTimeout(): ?int
    {
        return $this->requestTimeout;
    }

    public function setRequestTimeout(int $timeout): void
    {
        $this->requestTimeout = $timeout;
    }

    public function isInvoiceItemIdentifier(): bool
    {
        return $this->getSetting()->isInvoiceItemIdentifier();
    }

    public function setInvoiceItemIdentifier(bool $invoiceItemIdentifier): void
    {
        $this->getSetting()->setInvoiceItemIdentifier($invoiceItemIdentifier);
    }

    public function getCookieHandler(): CookieHandler
    {
        return $this->cookieHandler;
    }

    public function getSingleton(): bool
    {
        return $this->singleton;
    }

    protected function setSingleton(bool $singleton): void
    {
        $this->singleton = $singleton;
    }
}
