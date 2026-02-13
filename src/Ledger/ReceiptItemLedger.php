<?php

namespace Omisai\Szamlazzhu\Ledger;

use Omisai\Szamlazzhu\HasXmlBuildInterface;
use Omisai\Szamlazzhu\SzamlaAgentException;

class ReceiptItemLedger extends ItemLedger implements HasXmlBuildInterface
{
    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(): array
    {
        $data = [];
        if (!empty($this->revenueLedgerNumber)) {
            $data['arbevetel'] = $this->revenueLedgerNumber;
        }
        if (!empty($this->vatLedgerNumber)) {
            $data['afa'] = $this->vatLedgerNumber;
        }

        return $data;
    }
}
