<?php

use Omisai\Szamlazzhu\Header\ProformaHeader;

describe('ProformaHeader', function () {
    it('can be instantiated', function () {
        $header = new ProformaHeader;

        expect($header)->toBeInstanceOf(ProformaHeader::class);
    });

    it('has proforma type by default', function () {
        $header = new ProformaHeader;

        expect($header->isProforma())->toBeTrue();
    });

    it('is not paid by default', function () {
        $header = new ProformaHeader;

        expect($header->isPaid())->toBeFalse();
    });

    it('can create proforma header using helper', function () {
        $header = makeProformaHeader();

        expect($header)->toBeInstanceOf(ProformaHeader::class);
    });
});
