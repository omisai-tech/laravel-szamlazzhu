<?php

use Omisai\Szamlazzhu\Document\Proforma;
use Omisai\Szamlazzhu\Header\ProformaHeader;

describe('Proforma Document', function () {
    it('can be instantiated', function () {
        $proforma = new Proforma;

        expect($proforma)->toBeInstanceOf(Proforma::class);
    });

    it('has proforma header by default', function () {
        $proforma = new Proforma;

        expect($proforma->getHeader())->toBeInstanceOf(ProformaHeader::class);
    });

    it('has correct query type constants', function () {
        expect(Proforma::FROM_INVOICE_NUMBER)->toBe(1);
        expect(Proforma::FROM_ORDER_NUMBER)->toBe(2);
    });

    it('header is proforma type', function () {
        $proforma = new Proforma;

        expect($proforma->getHeader()->isProforma())->toBeTrue();
    });

    it('can set seller', function () {
        $proforma = new Proforma;
        $seller = makeSeller();
        $result = $proforma->setSeller($seller);

        expect($result)->toBeInstanceOf(Proforma::class);
    });

    it('can set buyer', function () {
        $proforma = new Proforma;
        $buyer = makeBuyer();
        $result = $proforma->setBuyer($buyer);

        expect($result)->toBeInstanceOf(Proforma::class);
    });

    it('can add items', function () {
        $proforma = new Proforma;
        $item = makeInvoiceItem();
        $result = $proforma->addItem($item);

        expect($result)->toBeInstanceOf(Proforma::class);
    });
});
