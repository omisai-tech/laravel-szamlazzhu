<?php

namespace Omisai\Szamlazzhu\Item;

use Omisai\Szamlazzhu\HasXmlBuildInterface;
use Omisai\Szamlazzhu\Ledger\InvoiceItemLedger;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

class InvoiceItem extends Item implements HasXmlBuildInterface
{
    protected ?InvoiceItemLedger $ledgerData = null;

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
        if (!empty($this->priceGapVatBase)) {
            $data['arresAfaAlap'] = $this->priceGapVatBase;
        }
        $data['nettoErtek'] = $this->netPrice == 0 ? number_format($this->netPrice, 2) : $this->netPrice;
        $data['afaErtek'] = $this->vatAmount == 0 ? number_format($this->vatAmount, 2) : $this->vatAmount;
        $data['bruttoErtek'] = $this->grossAmount == 0 ? number_format($this->grossAmount, 2) : $this->grossAmount;

        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }

        if ($this->ledgerData !== null) {
            $data['tetelFokonyv'] = $this->ledgerData->buildXmlData();
        }

        if ($this->dataDeletionCode !== null) {
            $data['torloKod'] = $this->dataDeletionCode;
        }

        return $data;
    }

    public function setLedgerData(InvoiceItemLedger $ledgerData): self
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }
}
