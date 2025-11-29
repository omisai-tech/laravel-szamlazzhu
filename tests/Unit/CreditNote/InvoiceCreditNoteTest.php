<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\CreditNote\InvoiceCreditNote;
use Omisai\Szamlazzhu\PaymentMethod;

describe('InvoiceCreditNote', function () {
    it('can be instantiated with required parameters', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0, PaymentMethod::PAYMENT_METHOD_TRANSFER);

        expect($creditNote)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('can be instantiated with description', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0, PaymentMethod::PAYMENT_METHOD_TRANSFER, 'Payment received');

        expect($creditNote)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('can set payment method', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0);
        $result = $creditNote->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($result)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('can get payment method', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0, PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($creditNote->getPaymentMethod())->toBe('bankkártya');
    });

    it('can set amount', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 50.0);
        $result = $creditNote->setAmount(150.0);

        expect($result)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('can set description', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0);
        $result = $creditNote->setDescription('Updated description');

        expect($result)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('can set date', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0);
        $newDate = Carbon::create(2023, 7, 20);
        $result = $creditNote->setDate($newDate);

        expect($result)->toBeInstanceOf(InvoiceCreditNote::class);
    });

    it('builds xml data correctly', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 250.50, PaymentMethod::PAYMENT_METHOD_TRANSFER, 'Invoice payment');

        $data = $creditNote->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('datum', '2023-06-15');
        expect($data)->toHaveKey('jogcim', 'átutalás');
        expect($data)->toHaveKey('osszeg', 250.50);
        expect($data)->toHaveKey('leiras', 'Invoice payment');
    });

    it('builds xml data without description when empty', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0, PaymentMethod::PAYMENT_METHOD_CASH, '');

        $data = $creditNote->buildXmlData();

        expect($data)->not->toHaveKey('leiras');
    });

    it('uses transfer as default payment method', function () {
        $date = Carbon::create(2023, 6, 15);
        $creditNote = new InvoiceCreditNote($date, 100.0);

        expect($creditNote->getPaymentMethod())->toBe('átutalás');
    });
});
