<?php

namespace Omisai\Szamlazzhu\Document\Receipt;

use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\CreditNote\ReceiptCreditNote;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\HasXmlBuildWithRequestInterface;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Item\ReceiptItem;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

/**
 * HU: Nyugta
 */
class Receipt extends Document implements HasXmlBuildWithRequestInterface
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

    public function setHeader(ReceiptHeader $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function addItem(ReceiptItem $item)
    {
        array_push($this->items, $item);
    }

    /**
     * @param  ReceiptItem[]  $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addCreditNote(ReceiptCreditNote $creditNote): self
    {
        if (count($this->creditNotes) < self::CREDIT_NOTES_LIMIT) {
            array_push($this->creditNotes, $creditNote);
        }

        return $this;
    }

    /**
     * @param  ReceiptCreditNote[]  $creditNotes
     */
    public function setCreditNotes(array $creditNotes): self
    {
        $this->creditNotes = $creditNotes;

        return $this;
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getBuyer(): Buyer
    {
        return $this->buyer;
    }

    public function setBuyer(Buyer $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
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
                $data = $this->buildFieldsData($request, array_merge($fields, $this->buyer->shouldSendEmail() ? ['emailKuldes'] : []));
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

        if (!empty($fields)) {
            foreach ($fields as $key) {
                switch ($key) {
                    case 'beallitasok':
                        $value = $request->getAgent()->getSetting()->buildXmlData($request);
                        break;
                    case 'fejlec':
                        $value = $this->header->buildXmlData($request);
                        break;
                    case 'tetelek':
                        $value = $this->buildXmlItemsData();
                        break;
                    case 'kifizetesek':
                        $value = (!empty($this->creditNotes)) ? $this->buildCreditsXmlData() : null;
                        break;
                    case 'emailKuldes':
                        $emailSendingData = $this->buildXmlEmailSendingData();
                        $value = (!empty($emailSendingData)) ? $emailSendingData : null;
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

        if (!empty($this->items)) {
            foreach ($this->items as $key => $item) {
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
        if (!empty($this->creditNotes)) {
            foreach ($this->creditNotes as $key => $note) {
                $data["note{$key}"] = $note->buildXmlData();
            }
        }

        return $data;
    }

    protected function buildXmlEmailSendingData(): array
    {
        $data = [];

        if (!empty($this->buyer) && !empty($this->buyer->getEmail())) {
            $data['email'] = $this->buyer->getEmail();
        }

        if (!empty($this->seller)) {
            if (!empty($this->seller->getEmailReplyTo())) {
                $data['emailReplyto'] = $this->seller->getEmailReplyTo();
            }
            if (!empty($this->seller->getEmailSubject())) {
                $data['emailTargy'] = $this->seller->getEmailSubject();
            }
            if (!empty($this->seller->getEmailContent())) {
                $data['emailSzoveg'] = $this->seller->getEmailContent();
            }
        }

        return $data;
    }
}
