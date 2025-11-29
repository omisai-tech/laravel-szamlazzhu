<?php

use Omisai\Szamlazzhu\CreditNote\ReceiptCreditNote;
use Omisai\Szamlazzhu\PaymentMethod;

describe('ReceiptCreditNote', function () {
    it('can be instantiated with default values', function () {
        $creditNote = new ReceiptCreditNote();

        expect($creditNote)->toBeInstanceOf(ReceiptCreditNote::class);
    });

    it('can be instantiated with parameters', function () {
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_BANKCARD, 150.0, 'Card payment');

        expect($creditNote)->toBeInstanceOf(ReceiptCreditNote::class);
    });

    it('can set payment method', function () {
        $creditNote = new ReceiptCreditNote();
        $result = $creditNote->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($result)->toBeInstanceOf(ReceiptCreditNote::class);
    });

    it('can get payment method', function () {
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_CASH);

        expect($creditNote->getPaymentMethod())->toBe('készpénz');
    });

    it('can set amount', function () {
        $creditNote = new ReceiptCreditNote();
        $result = $creditNote->setAmount(200.0);

        expect($result)->toBeInstanceOf(ReceiptCreditNote::class);
    });

    it('can set description', function () {
        $creditNote = new ReceiptCreditNote();
        $result = $creditNote->setDescription('Receipt payment');

        expect($result)->toBeInstanceOf(ReceiptCreditNote::class);
    });

    it('builds xml data correctly', function () {
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_CASH, 100.0, 'Cash payment');

        $data = $creditNote->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('fizetoeszkoz', 'készpénz');
        expect($data)->toHaveKey('osszeg', 100.0);
        expect($data)->toHaveKey('leiras', 'Cash payment');
    });

    it('builds xml data without description when empty', function () {
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_BANKCARD, 50.0, '');

        $data = $creditNote->buildXmlData();

        expect($data)->not->toHaveKey('leiras');
    });

    it('uses cash as default payment method', function () {
        $creditNote = new ReceiptCreditNote();

        expect($creditNote->getPaymentMethod())->toBe('készpénz');
    });

    it('has default amount of 0', function () {
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_CASH, 0.0);

        $data = $creditNote->buildXmlData();

        expect($data)->not->toHaveKey('osszeg');
    });
});
