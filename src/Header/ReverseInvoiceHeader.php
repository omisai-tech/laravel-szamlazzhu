<?php

namespace Omisai\Szamlazzhu\Header;

use Carbon\Carbon;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

/**
 * Sztornó számla fejléc
 */
class ReverseInvoiceHeader extends InvoiceHeader
{
    protected array $requiredFields = ['invoiceNumber'];

    /**
     * @throws SzamlaAgentException
     */
    public function __construct(int $type = Invoice::INVOICE_TYPE_P_INVOICE)
    {
        parent::__construct($type);
        $this->setType(Type::REVERSE_INVOICE);
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function setDefaultData(int $type)
    {
        $this->setType(Type::INVOICE);

        $this->setInvoiceType($type);

        $this->setIssueDate(Carbon::now());

        $this->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER);

        $this->setCurrency(Document::getDefaultCurrency());

        $this->setLanguage(Document::getDefaultLanguage());

        $this->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(?SzamlaAgentRequest $request = null): array
    {
        if (empty($request)) {
            throw new SzamlaAgentException(SzamlaAgentException::XML_DATA_NOT_AVAILABLE);
        }

        $this->validateFields();

        $data['szamlaszam'] = $this->getInvoiceNumber();

        if (!empty($this->issueDate)) {
            $data['keltDatum'] = $this->issueDate;
        }
        if (!empty($this->fulfillment)) {
            $data['teljesitesDatum'] = $this->fulfillment;
        }
        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }

        $data['tipus'] = Document::DOCUMENT_TYPE_REVERSE_INVOICE_CODE;

        if (!empty($this->invoiceTemplate)) {
            $data['szamlaSablon'] = $this->invoiceTemplate;
        }

        return $data;
    }
}
