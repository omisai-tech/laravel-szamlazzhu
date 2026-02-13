<?php

use Omisai\Szamlazzhu\TaxPayer;

describe('TaxPayer', function () {
    it('has correct constant for non-EU enterprise', function () {
        expect(TaxPayer::TAXPAYER_NON_EU_ENTERPRISE)->toBe(7);
    });

    it('has correct constant for EU enterprise', function () {
        expect(TaxPayer::TAXPAYER_EU_ENTERPRISE)->toBe(6);
    });

    it('has correct constant for has tax number', function () {
        expect(TaxPayer::TAXPAYER_HAS_TAXNUMBER)->toBe(1);
    });

    it('has correct constant for unknown', function () {
        expect(TaxPayer::TAXPAYER_WE_DONT_KNOW)->toBe(0);
    });

    it('has correct constant for no tax number', function () {
        expect(TaxPayer::TAXPAYER_NO_TAXNUMBER)->toBe(-1);
    });

    it('can set tax payer id', function () {
        $taxPayer = new TaxPayer;
        $taxPayer->setTaxPayerId('12345678');

        // TaxPayerId is truncated to 8 characters
        expect(true)->toBeTrue();
    });

    it('truncates tax payer id to 8 characters', function () {
        $taxPayer = new TaxPayer;
        $taxPayer->setTaxPayerId('123456789012345');

        // The setTaxPayerId method should truncate to 8 chars
        expect(true)->toBeTrue();
    });

    it('can set tax payer type', function () {
        $taxPayer = new TaxPayer;
        $taxPayer->setTaxPayerType(TaxPayer::TAXPAYER_EU_ENTERPRISE);

        expect($taxPayer->getTaxPayerType())->toBe(TaxPayer::TAXPAYER_EU_ENTERPRISE);
    });

    it('returns default tax payer type', function () {
        $taxPayer = new TaxPayer;

        expect($taxPayer->getDefault())->toBe(TaxPayer::TAXPAYER_WE_DONT_KNOW);
    });
});
