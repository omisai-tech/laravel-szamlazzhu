<?php

use Omisai\Szamlazzhu\Currency;

describe('Currency Enum', function () {
    it('has correct default currency', function () {
        expect(Currency::getDefault())->toBe(Currency::FT);
    });

    it('has correct value for HUF', function () {
        expect(Currency::HUF->value)->toBe('HUF');
    });

    it('has correct value for EUR', function () {
        expect(Currency::EUR->value)->toBe('EUR');
    });

    it('has correct value for USD', function () {
        expect(Currency::USD->value)->toBe('USD');
    });

    it('has correct value for CHF', function () {
        expect(Currency::CHF->value)->toBe('CHF');
    });

    it('has correct value for GBP', function () {
        expect(Currency::GBP->value)->toBe('GBP');
    });

    it('has all major currencies defined', function () {
        $currencies = Currency::cases();
        $currencyValues = array_map(fn($c) => $c->value, $currencies);

        expect($currencyValues)
            ->toContain('EUR')
            ->toContain('USD')
            ->toContain('GBP')
            ->toContain('CHF')
            ->toContain('HUF')
            ->toContain('Ft');
    });

    it('returns forint for default currency name', function () {
        expect(Currency::getCurrencyName(Currency::FT))->toBe('forint');
        expect(Currency::getCurrencyName(Currency::HUF))->toBe('forint');
    });
});
