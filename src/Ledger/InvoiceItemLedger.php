<?php

namespace Omisai\Szamlazzhu\Ledger;

use Carbon\Carbon;
use Omisai\Szamlazzhu\HasXmlBuildInterface;
use Omisai\Szamlazzhu\SzamlaAgentException;

class InvoiceItemLedger extends ItemLedger implements HasXmlBuildInterface
{
    protected string $economicEventType;

    protected string $vatEconomicEventType;

    protected Carbon $settlementPeriodStart;

    protected Carbon $settlementPeriodEnd;

    public function __construct(string $economicEventType = '', string $vatEconomicEventType = '', string $revenueLedgerNumber = '', string $vatLedgerNumber = '')
    {
        parent::__construct((string) $revenueLedgerNumber, (string) $vatLedgerNumber);
        $this->setEconomicEventType($economicEventType);
        $this->setVatEconomicEventType($vatEconomicEventType);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(): array
    {
        $data = [];
        if (!empty($this->economicEventType)) {
            $data['gazdasagiEsem'] = $this->economicEventType;
        }
        if (!empty($this->vatEconomicEventType)) {
            $data['gazdasagiEsemAfa'] = $this->vatEconomicEventType;
        }
        if (!empty($this->revenueLedgerNumber)) {
            $data['arbevetelFokonyviSzam'] = $this->revenueLedgerNumber;
        }
        if (!empty($this->vatLedgerNumber)) {
            $data['afaFokonyviSzam'] = $this->vatLedgerNumber;
        }
        if (!empty($this->settlementPeriodStart)) {
            $data['elszDatumTol'] = $this->settlementPeriodStart->format('Y-m-d');
        }
        if (!empty($this->settlementPeriodEnd)) {
            $data['elszDatumIg'] = $this->settlementPeriodEnd->format('Y-m-d');
        }

        return $data;
    }

    public function setEconomicEventType(string $economicEventType): self
    {
        $this->economicEventType = $economicEventType;

        return $this;
    }

    public function setVatEconomicEventType(string $vatEconomicEventType): self
    {
        $this->vatEconomicEventType = $vatEconomicEventType;

        return $this;
    }

    public function setSettlementPeriodStart(Carbon $settlementPeriodStart): self
    {
        $this->settlementPeriodStart = $settlementPeriodStart;

        return $this;
    }

    public function setSettlementPeriodEnd(Carbon $settlementPeriodEnd): self
    {
        $this->settlementPeriodEnd = $settlementPeriodEnd;

        return $this;
    }
}
