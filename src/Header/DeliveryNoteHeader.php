<?php

namespace Omisai\Szamlazzhu\Header;

use Omisai\Szamlazzhu\SzamlaAgentException;

class DeliveryNoteHeader extends InvoiceHeader
{
    /**
     * @throws SzamlaAgentException
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(Type::DELIVERY_NOTE);
        $this->setPaid(false);
    }
}
