<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\BuyerLedger;

describe('BuyerLedger', function () {
    it('can be instantiated', function () {
        $ledger = new BuyerLedger;
        expect($ledger)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set buyer id', function () {
        $ledger = new BuyerLedger;
        $result = $ledger->setBuyerId('BUYER123');

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set booking date', function () {
        $ledger = new BuyerLedger;
        $date = Carbon::create(2023, 6, 15);
        $result = $ledger->setBookingDate($date);

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set buyer ledger number', function () {
        $ledger = new BuyerLedger;
        $result = $ledger->setBuyerLedgerNumber('LEDGER456');

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set continued fulfillment', function () {
        $ledger = new BuyerLedger;
        $result = $ledger->setContinuedFulfillment(true);

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set settlement period start', function () {
        $ledger = new BuyerLedger;
        $date = Carbon::create(2023, 1, 1);
        $result = $ledger->setSettlementPeriodStart($date);

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('can set settlement period end', function () {
        $ledger = new BuyerLedger;
        $date = Carbon::create(2023, 12, 31);
        $result = $ledger->setSettlementPeriodEnd($date);

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('returns fluent interface for all setters', function () {
        $ledger = new BuyerLedger;

        $result = $ledger
            ->setBuyerId('ID123')
            ->setBookingDate(Carbon::now())
            ->setBuyerLedgerNumber('LEDGER789')
            ->setContinuedFulfillment(true)
            ->setSettlementPeriodStart(Carbon::now()->subMonth())
            ->setSettlementPeriodEnd(Carbon::now());

        expect($result)->toBeInstanceOf(BuyerLedger::class);
    });

    it('generates xml data with buyer id', function () {
        $ledger = new BuyerLedger;
        $ledger->setBuyerId('BUYER123')
            ->setContinuedFulfillment(false);

        $data = $ledger->getXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('vevoAzonosito', 'BUYER123');
    });

    it('generates xml data with booking date', function () {
        $ledger = new BuyerLedger;
        $ledger->setBookingDate(Carbon::create(2023, 6, 15))
            ->setContinuedFulfillment(false);

        $data = $ledger->getXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('konyvelesDatum', '2023-06-15');
    });

    it('generates xml data with settlement period', function () {
        $ledger = new BuyerLedger;
        $ledger->setSettlementPeriodStart(Carbon::create(2023, 1, 1))
            ->setSettlementPeriodEnd(Carbon::create(2023, 12, 31))
            ->setContinuedFulfillment(false);

        $data = $ledger->getXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('elszDatumTol', '2023-01-01');
        expect($data)->toHaveKey('elszDatumIg', '2023-12-31');
    });

    it('generates empty xml data when no properties set except continued fulfillment', function () {
        $ledger = new BuyerLedger;
        $ledger->setContinuedFulfillment(false);

        $data = $ledger->getXmlData();

        expect($data)->toBeArray();
        expect($data)->toBeEmpty();
    });

    it('generates xml data with continued fulfillment set to true', function () {
        $ledger = new BuyerLedger;
        $ledger->setContinuedFulfillment(true);

        $data = $ledger->getXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('folyamatosTelj', true);
    });
});
