<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\Ledger\InvoiceItemLedger;

describe('InvoiceItemLedger', function () {
    it('can be instantiated with default values', function () {
        $ledger = new InvoiceItemLedger();

        expect($ledger)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('can be instantiated with all parameters', function () {
        $ledger = new InvoiceItemLedger('economic', 'vatEconomic', '123', '456');

        expect($ledger)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('can set economic event type', function () {
        $ledger = new InvoiceItemLedger();
        $result = $ledger->setEconomicEventType('EconomicEvent');

        expect($result)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('can set VAT economic event type', function () {
        $ledger = new InvoiceItemLedger();
        $result = $ledger->setVatEconomicEventType('VATEvent');

        expect($result)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('can set settlement period start', function () {
        $ledger = new InvoiceItemLedger();
        $date = Carbon::create(2023, 1, 1);
        $result = $ledger->setSettlementPeriodStart($date);

        expect($result)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('can set settlement period end', function () {
        $ledger = new InvoiceItemLedger();
        $date = Carbon::create(2023, 12, 31);
        $result = $ledger->setSettlementPeriodEnd($date);

        expect($result)->toBeInstanceOf(InvoiceItemLedger::class);
    });

    it('builds xml data with economic event type', function () {
        $ledger = new InvoiceItemLedger('EconomicEvent', '', '', '');

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('gazdasagiEsem', 'EconomicEvent');
    });

    it('builds xml data with vat economic event type', function () {
        $ledger = new InvoiceItemLedger('', 'VATEvent', '', '');

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('gazdasagiEsemAfa', 'VATEvent');
    });

    it('builds xml data with revenue ledger number', function () {
        $ledger = new InvoiceItemLedger('', '', '123', '');

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('arbevetelFokonyviSzam', '123');
    });

    it('builds xml data with vat ledger number', function () {
        $ledger = new InvoiceItemLedger('', '', '', '456');

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('afaFokonyviSzam', '456');
    });

    it('builds xml data with settlement period', function () {
        $ledger = new InvoiceItemLedger();
        $ledger->setSettlementPeriodStart(Carbon::create(2023, 1, 1));
        $ledger->setSettlementPeriodEnd(Carbon::create(2023, 12, 31));

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('elszDatumTol', '2023-01-01');
        expect($data)->toHaveKey('elszDatumIg', '2023-12-31');
    });

    it('builds empty xml data when no properties set', function () {
        $ledger = new InvoiceItemLedger();

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toBeEmpty();
    });

    it('builds complete xml data', function () {
        $ledger = new InvoiceItemLedger('economic', 'vatEconomic', '123', '456');
        $ledger->setSettlementPeriodStart(Carbon::create(2023, 1, 1));
        $ledger->setSettlementPeriodEnd(Carbon::create(2023, 12, 31));

        $data = $ledger->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveCount(6);
    });
});
