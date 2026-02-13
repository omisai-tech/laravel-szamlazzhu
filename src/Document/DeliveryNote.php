<?php

namespace Omisai\Szamlazzhu\Document;

use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\DeliveryNoteHeader;

/**
 * HU: Szállítólevél
 */
class DeliveryNote extends Invoice
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setHeader(new DeliveryNoteHeader);
    }
}
