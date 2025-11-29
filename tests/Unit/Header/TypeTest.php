<?php

use Omisai\Szamlazzhu\Header\Type;

describe('Header Type Enum', function () {
    it('has invoice type', function () {
        expect(Type::INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has reverse invoice type', function () {
        expect(Type::REVERSE_INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has prepayment invoice type', function () {
        expect(Type::PREPAYMENT_INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has final invoice type', function () {
        expect(Type::FINAL_INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has corrective invoice type', function () {
        expect(Type::CORRECTIVE_INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has proforma invoice type', function () {
        expect(Type::PROFORMA_INVOICE)->toBeInstanceOf(Type::class);
    });

    it('has delivery note type', function () {
        expect(Type::DELIVERY_NOTE)->toBeInstanceOf(Type::class);
    });

    it('has receipt type', function () {
        expect(Type::RECEIPT)->toBeInstanceOf(Type::class);
    });

    it('has reverse receipt type', function () {
        expect(Type::REVERSE_RECEIPT)->toBeInstanceOf(Type::class);
    });

    it('has exactly 9 types', function () {
        expect(Type::cases())->toHaveCount(9);
    });
});
