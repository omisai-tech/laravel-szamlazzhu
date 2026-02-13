<?php

use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Header\ProformaHeader;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\PaymentMethod;

describe('DocumentHeader', function () {
    it('can set payment method', function () {
        $header = new InvoiceHeader;
        $result = $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can get payment method value', function () {
        $header = new InvoiceHeader;
        $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($header->getPaymentMethod())->toBe('bankkártya');
    });

    it('can set payment method by string', function () {
        $header = new InvoiceHeader;
        $result = $header->setPaymentMethodByString('bankkártya');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
        expect($header->getPaymentMethod())->toBe('bankkártya');
    });

    it('can set currency', function () {
        $header = new InvoiceHeader;
        $result = $header->setCurrency(Currency::EUR);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can get currency value', function () {
        $header = new InvoiceHeader;
        $header->setCurrency(Currency::EUR);

        expect($header->getCurrency())->toBe('EUR');
    });

    it('can set prefix', function () {
        $header = new InvoiceHeader;
        $result = $header->setPrefix('TEST');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set comment', function () {
        $header = new InvoiceHeader;
        $result = $header->setComment('Test comment');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set exchange bank', function () {
        $header = new InvoiceHeader;
        $result = $header->setExchangeBank('MNB');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set exchange rate', function () {
        $header = new InvoiceHeader;
        $result = $header->setExchangeRate(380.50);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('correctly identifies invoice type', function () {
        $header = new InvoiceHeader;

        expect($header->isInvoice())->toBeTrue();
        expect($header->isProforma())->toBeFalse();
        expect($header->isReceipt())->toBeFalse();
    });

    it('correctly identifies proforma type', function () {
        $header = new ProformaHeader;

        expect($header->isProforma())->toBeTrue();
        expect($header->isInvoice())->toBeFalse();
    });

    it('correctly identifies receipt type', function () {
        $header = new ReceiptHeader;

        expect($header->isReceipt())->toBeTrue();
        expect($header->isInvoice())->toBeFalse();
    });

    it('correctly identifies not reverse invoice', function () {
        $header = new InvoiceHeader;

        expect($header->isNotReserveInvoice())->toBeTrue();
    });
});
