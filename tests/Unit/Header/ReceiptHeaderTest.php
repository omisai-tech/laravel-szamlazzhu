<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Header\Type;
use Omisai\Szamlazzhu\PaymentMethod;

describe('ReceiptHeader', function () {
    it('can be instantiated without receipt number', function () {
        $header = new ReceiptHeader();

        expect($header)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can be instantiated with receipt number', function () {
        $header = new ReceiptHeader('RECEIPT123');

        expect($header)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('has receipt type by default', function () {
        $header = new ReceiptHeader();

        expect($header->isReceipt())->toBeTrue();
    });

    it('has cash payment method by default', function () {
        $header = new ReceiptHeader();

        expect($header->getPaymentMethod())->toBe('készpénz');
    });

    it('can set receipt number', function () {
        $header = new ReceiptHeader();
        $result = $header->setReceiptNumber('REC456');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can set call id', function () {
        $header = new ReceiptHeader();
        $result = $header->setCallId('CALL123');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can set pdf template', function () {
        $header = new ReceiptHeader();
        $result = $header->setPdfTemplate('CustomTemplate');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can set buyer ledger id', function () {
        $header = new ReceiptHeader();
        $result = $header->setBuyerLedgerId('LEDGER123');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can set payment method', function () {
        $header = new ReceiptHeader();
        $result = $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
        expect($header->getPaymentMethod())->toBe('bankkártya');
    });

    it('can set currency', function () {
        $header = new ReceiptHeader();
        $result = $header->setCurrency(Currency::EUR);

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
        expect($header->getCurrency())->toBe('EUR');
    });

    it('can set exchange bank', function () {
        $header = new ReceiptHeader();
        $result = $header->setExchangeBank('MNB');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can set comment', function () {
        $header = new ReceiptHeader();
        $result = $header->setComment('Receipt comment');

        expect($result)->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can create complete receipt header using helper', function () {
        $header = makeReceiptHeader();

        expect($header)->toBeInstanceOf(ReceiptHeader::class);
    });
});
