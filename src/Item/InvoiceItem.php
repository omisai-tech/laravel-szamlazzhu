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
        $data['nettoErtek'] = 0 == $this->netPrice ? number_format($this->netPrice, 2) : $this->netPrice;
        $data['afaErtek'] = 0 == $this->vatAmount ? number_format($this->vatAmount, 2) : $this->vatAmount;
        $data['bruttoErtek'] = 0 == $this->grossAmount ? number_format($this->grossAmount, 2) : $this->grossAmount;

        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }

        if (null !== $this->ledgerData) {
            $data['tetelFokonyv'] = $this->ledgerData->buildXmlData();
        }

        if (null !== $this->dataDeletionCode) {
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
