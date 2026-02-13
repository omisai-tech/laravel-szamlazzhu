<?php

namespace Omisai\Szamlazzhu\Header;

use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\SzamlaAgentException;

class CorrectiveInvoiceHeader extends InvoiceHeader
{
    /**
     * @throws SzamlaAgentException
     */
    public function __construct(int $type = Invoice::INVOICE_TYPE_P_INVOICE)
    {
        parent::__construct($type);
        $this->setType(Type::CORRECTIVE_INVOICE);
    }
}
