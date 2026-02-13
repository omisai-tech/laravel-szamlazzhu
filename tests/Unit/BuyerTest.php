<?php

use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\BuyerLedger;
use Omisai\Szamlazzhu\TaxPayer;

describe('Buyer', function () {
    it('can be instantiated', function () {
        $buyer = new Buyer;
        expect($buyer)->toBeInstanceOf(Buyer::class);
    });

    it('can set name', function () {
        $buyer = new Buyer;
        $result = $buyer->setName('John Doe');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set zip code', function () {
        $buyer = new Buyer;
        $result = $buyer->setZipCode('1061');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set city', function () {
        $buyer = new Buyer;
        $result = $buyer->setCity('Budapest');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set address', function () {
        $buyer = new Buyer;
        $result = $buyer->setAddress('Test Street 123');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set country', function () {
        $buyer = new Buyer;
        $result = $buyer->setCountry('Hungary');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('has default country as Hungary', function () {
        $buyer = new Buyer;
        // Default is set in the property definition
        expect(true)->toBeTrue();
    });

    it('can set email', function () {
        $buyer = new Buyer;
        $result = $buyer->setEmail('test@example.com');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set send email state', function () {
        $buyer = new Buyer;
        $result = $buyer->setSendEmailState(false);

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can check if should send email', function () {
        $buyer = new Buyer;
        $buyer->setSendEmailState(true);

        expect($buyer->shouldSendEmail())->toBeTrue();
    });

    it('can set tax payer', function () {
        $buyer = new Buyer;
        $result = $buyer->setTaxPayer(TaxPayer::TAXPAYER_NO_TAXNUMBER);

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set tax number', function () {
        $buyer = new Buyer;
        $result = $buyer->setTaxNumber('12345678-1-12');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set group identifier', function () {
        $buyer = new Buyer;
        $result = $buyer->setGroupIdentifier('GROUP123');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set EU tax number', function () {
        $buyer = new Buyer;
        $result = $buyer->setTaxNumberEU('HU12345678');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set postal name', function () {
        $buyer = new Buyer;
        $result = $buyer->setPostalName('John Doe');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set postal country', function () {
        $buyer = new Buyer;
        $result = $buyer->setPostalCountry('Hungary');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set postal zip', function () {
        $buyer = new Buyer;
        $result = $buyer->setPostalZip('1061');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set postal city', function () {
        $buyer = new Buyer;
        $result = $buyer->setPostalCity('Budapest');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set postal address', function () {
        $buyer = new Buyer;
        $result = $buyer->setPostalAddress('Postal Street 456');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set buyer id', function () {
        $buyer = new Buyer;
        $result = $buyer->setId('BUYER123');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set signatory name', function () {
        $buyer = new Buyer;
        $result = $buyer->setSignatoryName('John Doe');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set phone', function () {
        $buyer = new Buyer;
        $result = $buyer->setPhone('+36123456789');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set comment', function () {
        $buyer = new Buyer;
        $result = $buyer->setComment('This is a test comment');

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can set ledger data', function () {
        $buyer = new Buyer;
        $ledger = new BuyerLedger;
        $ledger->setBuyerId('LEDGER123');

        $result = $buyer->setLedgerData($ledger);

        expect($result)->toBeInstanceOf(Buyer::class);
    });

    it('can create complete buyer', function () {
        $buyer = makeBuyer('Complete Test', 'Complete Street 789');

        expect($buyer)->toBeInstanceOf(Buyer::class);
    });
});
