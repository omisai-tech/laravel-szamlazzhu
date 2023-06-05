<?php

namespace Omisai\SzamlazzhuAgent\Document\Receipt;

use Omisai\SzamlazzhuAgent\Buyer;
use Omisai\SzamlazzhuAgent\CreditNote\ReceiptCreditNote;
use Omisai\SzamlazzhuAgent\Document\Document;
use Omisai\SzamlazzhuAgent\Header\ReceiptHeader;
use Omisai\SzamlazzhuAgent\Item\ReceiptItem;
use Omisai\SzamlazzhuAgent\Seller;
use Omisai\SzamlazzhuAgent\SzamlaAgentException;
use Omisai\SzamlazzhuAgent\SzamlaAgentRequest;
use Omisai\SzamlazzhuAgent\SzamlaAgentUtil;

/**
 * HU: Nyugta
 */
class Receipt extends Document
{
    public const CREDIT_NOTES_LIMIT = 5;

    private ReceiptHeader $header;

    /**
     * @var ReceiptItem[]
     */
    protected array $items = [];

    /**
     * HU: A kifizetesek nem kötelező, de ha meg van adva,
     * akkor az összegeknek meg kell egyezniük a nyugta végösszegével.
     *
     * @var ReceiptCreditNote[]
     */
    protected array $creditNotes = [];

    protected Seller $seller;

    protected Buyer $buyer;

    public function __construct(string $receiptNumber = '')
    {
        if (!empty($receiptNumber)) {
            $this->setHeader(new ReceiptHeader($receiptNumber));
        }
    }

    public function getHeader(): ReceiptHeader
    {
        return $this->header;
    }

    public function setHeader(ReceiptHeader $header): void
    {
        $this->header = $header;
    }

    public function addItem(ReceiptItem $item)
    {
        array_push($this->items, $item);
    }

    /**
     * @return ReceiptItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param  ReceiptItem[]  $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function addCreditNote(ReceiptCreditNote $creditNote): void
    {
        if (count($this->creditNotes) < self::CREDIT_NOTES_LIMIT) {
            array_push($this->creditNotes, $creditNote);
        }
    }

    /**
     * @return ReceiptCreditNote[]
     */
    public function getCreditNotes(): array
    {
        return $this->creditNotes;
    }

    /**
     * @param  ReceiptCreditNote[]  $creditNotes
     */
    public function setCreditNotes(array $creditNotes): void
    {
        $this->creditNotes = $creditNotes;
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

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $fields = ['beallitasok', 'fejlec'];

        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_RECEIPT:
                $data = $this->buildFieldsData($request, array_merge($fields, ['tetelek', 'kifizetesek']));
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_RECEIPT:
            case $request::XML_SCHEMA_GET_RECEIPT:
                $data = $this->buildFieldsData($request, $fields);
                break;
            case $request::XML_SCHEMA_SEND_RECEIPT:
                $data = $this->buildFieldsData($request, array_merge($fields, ['emailKuldes']));
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

        if (! empty($fields)) {
            $emailSendingData = $this->buildXmlEmailSendingData();
            foreach ($fields as $key) {
                switch ($key) {
                    case 'beallitasok': $value = $request->getAgent()->getSetting()->buildXmlData($request);
                    break;
                    case 'fejlec':      $value = $this->getHeader()->buildXmlData($request);
                    break;
                    case 'tetelek':     $value = $this->buildXmlItemsData();
                    break;
                    case 'kifizetesek': $value = (! empty($this->getCreditNotes())) ? $this->buildCreditsXmlData() : null;
                    break;
                    case 'emailKuldes': $value = (! empty($emailSendingData)) ? $emailSendingData : null;
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

    protected function buildXmlEmailSendingData(): array
    {
        $data = [];

        if (SzamlaAgentUtil::isNotNull($this->getBuyer()) && SzamlaAgentUtil::isNotBlank($this->getBuyer()->getEmail())) {
            $data['email'] = $this->getBuyer()->getEmail();
        }

        if (SzamlaAgentUtil::isNotNull($this->getSeller())) {
            if (SzamlaAgentUtil::isNotBlank($this->getSeller()->getEmailReplyTo())) {
                $data['emailReplyto'] = $this->getSeller()->getEmailReplyTo();
            }
            if (SzamlaAgentUtil::isNotBlank($this->getSeller()->getEmailSubject())) {
                $data['emailTargy'] = $this->getSeller()->getEmailSubject();
            }
            if (SzamlaAgentUtil::isNotBlank($this->getSeller()->getEmailContent())) {
                $data['emailSzoveg'] = $this->getSeller()->getEmailContent();
            }
        }

        return $data;
    }
}
