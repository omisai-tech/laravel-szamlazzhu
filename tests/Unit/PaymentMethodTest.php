<?php

use Omisai\Szamlazzhu\PaymentMethod;

describe('PaymentMethod Enum', function () {
    it('has correct value for transfer', function () {
        expect(PaymentMethod::PAYMENT_METHOD_TRANSFER->value)->toBe('átutalás');
    });

    it('has correct value for cash', function () {
        expect(PaymentMethod::PAYMENT_METHOD_CASH->value)->toBe('készpénz');
    });

    it('has correct value for bankcard', function () {
        expect(PaymentMethod::PAYMENT_METHOD_BANKCARD->value)->toBe('bankkártya');
    });

    it('has correct value for cheque', function () {
        expect(PaymentMethod::PAYMENT_METHOD_CHEQUE->value)->toBe('csekk');
    });

    it('has correct value for cash on delivery', function () {
        expect(PaymentMethod::PAYMENT_METHOD_CASH_ON_DELIVERY->value)->toBe('utánvét');
    });

    it('has correct value for PayPal', function () {
        expect(PaymentMethod::PAYMENT_METHOD_PAYPAL->value)->toBe('PayPal');
    });

    it('has correct value for SZEP card', function () {
        expect(PaymentMethod::PAYMENT_METHOD_SZEP_CARD->value)->toBe('SZÉP kártya');
    });

    it('has correct value for OTP Simple', function () {
        expect(PaymentMethod::PAYMENT_METHOD_OTP_SIMPLE->value)->toBe('OTP Simple');
    });

    it('has all payment methods defined', function () {
        expect(PaymentMethod::cases())->toHaveCount(8);
    });

    it('can be created from string value', function () {
        $method = PaymentMethod::tryFrom('bankkártya');
        expect($method)->toBe(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    });

    it('returns null for invalid payment method string', function () {
        $method = PaymentMethod::tryFrom('invalid');
        expect($method)->toBeNull();
    });
});
