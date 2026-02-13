<?php

namespace Omisai\Szamlazzhu\Document;

use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\ProformaHeader;

/**
 * HU: Díjbekérő
 */
class Proforma extends Invoice
{
    /**
     * HU: Díjbekérő számlaszám alapján
     */
    public const FROM_INVOICE_NUMBER = 1;

    /**
     * HU: Díjbekérő rendelésszám alapján
     */
    public const FROM_ORDER_NUMBER = 2;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setHeader(new ProformaHeader);
    }
}
