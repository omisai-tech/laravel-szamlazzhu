<?php

namespace Omisai\Szamlazzhu\Item;

use Omisai\Szamlazzhu\HasXmlBuildInterface;
use Omisai\Szamlazzhu\Ledger\ReceiptItemLedger;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

class ReceiptItem extends Item implements HasXmlBuildInterface
{
    protected ?ReceiptItemLedger $ledgerData = null;

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(): array
    {
        $this->validateFields();

        $data = [];
        $data['megnevezes'] = $this->name;

        if (!empty($this->id)) {
            $data['azonosito'] = $this->id;
        }

        $data['mennyiseg'] = number_format($this->quantity, 2);
        $data['mennyisegiEgyseg'] = $this->quantityUnit;
        $data['nettoEgysegar'] = $this->netUnitPrice;
        $data['afakulcs'] = SzamlaAgentUtil::dotCheck($this->vat);
        $data['netto'] = number_format($this->netPrice, 2);
        $data['afa'] = number_format($this->vatAmount, 2);
        $data['brutto'] = number_format($this->grossAmount, 2);

        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }

        if (null !== $this->ledgerData) {
            $data['fokonyv'] = $this->ledgerData->buildXmlData();
        }

        if (null !== $this->dataDeletionCode) {
            $data['torloKod'] = $this->dataDeletionCode;
        }

        return $data;
    }

    public function setLedgerData(ReceiptItemLedger $ledgerData): self
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }
}
