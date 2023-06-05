<?php

namespace Omisai\Szamlazzhu\Document\Invoice;

use Illuminate\Support\Facades\Log;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\CreditNote\InvoiceCreditNote;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Omisai\Szamlazzhu\Waybill\Waybill;

/**
 * HU: Számla
 */
class Invoice extends Document
{
    /** HU: Számla típus: papír számla */
    public const INVOICE_TYPE_P_INVOICE = 1;

    /** HU: Számla típus: e-számla */
    public const INVOICE_TYPE_E_INVOICE = 2;

    /** HU: Számla lekérdezése számlaszám alapján */
    public const FROM_INVOICE_NUMBER = 1;

    /** HU: Számla lekérdezése rendelési szám alapján */
    public const FROM_ORDER_NUMBER = 2;

    /** HU: Számla lekérdezése külső számlaazonosító alapján */
    public const FROM_INVOICE_EXTERNAL_ID = 3;

    /**
     * HU: Jóváírások maximális száma
     * a számla kifizetettségének beállításakor
     */
    public const CREDIT_NOTES_LIMIT = 5;

    /** Számlához csatolandó fájlok maximális száma */
    public const INVOICE_ATTACHMENTS_LIMIT = 5;

    /** Számlázz.hu ajánlott számlakép */
    public const INVOICE_TEMPLATE_DEFAULT = 'SzlaMost';

    /** Tradicionális számlakép */
    public const INVOICE_TEMPLATE_TRADITIONAL = 'SzlaNoEnv';

    /** Borítékbarát számlakép */
    public const INVOICE_TEMPLATE_ENV_FRIENDLY = 'SzlaAlap';

    /** Hőnyomtatós számlakép (8 cm széles) */
    public const INVOICE_TEMPLATE_8CM = 'Szla8cm';

    /** Retró kéziszámla számlakép */
    public const INVOICE_TEMPLATE_RETRO = 'SzlaTomb';

    private InvoiceHeader $header;

    protected Seller $seller;

    protected Buyer $buyer;

    protected Waybill $waybill;

    /**
     * @var InvoiceItem[]
     */
    protected array $items = [];

    /**
     * @var InvoiceCreditNote[]
     */
    protected array $creditNotes = [];

    /**
     * HU: Összeadandó-e a jóváírás
     *
     * Ha igaz, akkor nem törli a korábbi jóváírásokat,
     * hanem hozzáadja az összeget az eddigiekhez.
     */
    protected bool $additive = true;

    protected array $attachments = [];

    /**
     * @throws SzamlaAgentException
     */
    public function __construct(int $type = self::INVOICE_TYPE_E_INVOICE)
    {
        $this->setHeader(new InvoiceHeader($type));
    }

    public function getHeader(): InvoiceHeader
    {
        return $this->header;
    }

    public function setHeader(InvoiceHeader $header): void
    {
        $this->header = $header;
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function setSeller(Seller $seller): void
    {
        $this->seller = $seller;
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }

    public function setBuyer(Buyer $buyer): void
    {
        $this->buyer = $buyer;
    }

    public function getWaybill(): Waybill
    {
        return $this->waybill;
    }

    public function setWaybill(Waybill $waybill): void
    {
        $this->waybill = $waybill;
    }

    public function addItem(InvoiceItem $item): void
    {
        array_push($this->items, $item);
    }

    /**
     * @return InvoiceItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param  InvoiceItem[]  $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * HU: Jóváírás hozzáadása a számlához
     */
    public function addCreditNote(InvoiceCreditNote $creditNote): void
    {
        if (count($this->creditNotes) < self::CREDIT_NOTES_LIMIT) {
            array_push($this->creditNotes, $creditNote);
        }
    }

    /**
     * @return InvoiceCreditNote[]
     */
    public function getCreditNotes(): array
    {
        return $this->creditNotes;
    }

    /**
     * @param  InvoiceCreditNote[]  $creditNotes
     */
    public function setCreditNotes(array $creditNotes): void
    {
        $this->creditNotes = $creditNotes;
    }

    public function isAdditive(): bool
    {
        return $this->additive;
    }

    public function setAdditive(bool $additive): void
    {
        $this->additive = $additive;
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_INVOICE:
                $data = $this->buildFieldsData($request, ['beallitasok', 'fejlec', 'elado', 'vevo', 'fuvarlevel', 'tetelek']);
                break;
            case $request::XML_SCHEMA_DELETE_PROFORMA:
                $data = $this->buildFieldsData($request, ['beallitasok', 'fejlec']);
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_INVOICE:
                $data = $this->buildFieldsData($request, ['beallitasok', 'fejlec', 'elado', 'vevo']);
                break;
            case $request::XML_SCHEMA_PAY_INVOICE:
                $data = $this->buildFieldsData($request, ['beallitasok']);
                $data = array_merge($data, $this->buildCreditsXmlData());
                break;
            case $request::XML_SCHEMA_REQUEST_INVOICE_XML:
            case $request::XML_SCHEMA_REQUEST_INVOICE_PDF:
                $settings = $this->buildFieldsData($request, ['beallitasok']);
                $data = $settings['beallitasok'];
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::XML_SCHEMA_TYPE_NOT_EXISTS.": {$request->getXmlName()}.");
        }

        return $data;
    }

    /**
     * @throws SzamlaAgentException
     */
    private function buildFieldsData(SzamlaAgentRequest $request, array $fields): array
    {
        $data = [];

        if (! empty($fields)) {
            foreach ($fields as $key) {
                switch ($key) {
                    case 'beallitasok': $value = $request->getAgent()->getSetting()->buildXmlData($request);
                    break;
                    case 'fejlec':      $value = $this->getHeader()->buildXmlData($request);
                    break;
                    case 'tetelek':     $value = $this->buildXmlItemsData();
                    break;
                    case 'elado':       $value = (SzamlaAgentUtil::isNotNull($this->getSeller())) ? $this->getSeller()->buildXmlData($request) : [];
                    break;
                    case 'vevo':        $value = (SzamlaAgentUtil::isNotNull($this->getBuyer())) ? $this->getBuyer()->buildXmlData($request) : [];
                    break;
                    case 'fuvarlevel':  $value = (SzamlaAgentUtil::isNotNull($this->getWaybill())) ? $this->getWaybill()->buildXmlData($request) : [];
                    break;
                    default:
                        throw new SzamlaAgentException(SzamlaAgentException::XML_KEY_NOT_EXISTS.": {$key}");
                }

                if (isset($value)) {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * @throws SzamlaAgentException
     */
    protected function buildXmlItemsData(): array
    {
        $data = [];

        if (! empty($this->getItems())) {
            foreach ($this->getItems() as $key => $item) {
                $data["item{$key}"] = $item->buildXmlData();
            }
        }

        return $data;
    }

    /**
     * @throws SzamlaAgentException
     */
    protected function buildCreditsXmlData(): array
    {
        $data = [];
        if (! empty($this->getCreditNotes())) {
            foreach ($this->getCreditNotes() as $key => $note) {
                $data["note{$key}"] = $note->buildXmlData();
            }
        }

        return $data;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * HU: Fájl csatolása a számlához
     *
     * Összesen 5 db mellékletet tudsz egy számlához csatolni.
     * A beküldött fájlok mérete nem haladhatja meg a 2 MB méretet. Ha valamelyik beküldött fájl csatolása valamilyen okból sikertelen,
     * akkor a nem megfelelő csatolmányokról a rendszer figyelmeztető emailt küld a beküldőnek (minden rossz fájlról külön-külön).
     *
     * Hibás csatolmány esetén is kiküldésre kerül az értesítő email úgy, hogy a megfelelő fájlok csatolva lesznek.
     * Ha nem érkezik kérés értesítő email kiküldésére, akkor a beküldött csatolmányok nem kerülnek feldolgozásra.
     *
     * @throws SzamlaAgentException
     */
    public function addAttachment(string $filePath)
    {
        if (empty($filePath)) {
            Log::channel('szamlazzhu')->warning('A csatolandó fájl neve nincs megadva!');
        } else {
            if (count($this->attachments) >= self::INVOICE_ATTACHMENTS_LIMIT) {
                throw new SzamlaAgentException('A következő fájl csatolása sikertelen: "'.$filePath.'". Egy számlához maximum '.self::INVOICE_ATTACHMENTS_LIMIT.' fájl csatolható!');
            }

            if (! file_exists($filePath)) {
                throw new SzamlaAgentException(SzamlaAgentException::ATTACHMENT_NOT_EXISTS.': '.$filePath);
            }
            array_push($this->attachments, $filePath);
        }
    }
}
