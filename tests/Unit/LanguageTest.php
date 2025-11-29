<?php

use Omisai\Szamlazzhu\Language;

describe('Language Enum', function () {
    it('has correct default language', function () {
        expect(Language::getDefault())->toBe(Language::HU);
    });

    it('has correct value for HU', function () {
        expect(Language::HU->value)->toBe('hu');
    });

    it('has correct value for EN', function () {
        expect(Language::EN->value)->toBe('en');
    });

    it('has correct value for DE', function () {
        expect(Language::DE->value)->toBe('de');
    });

    it('has correct value for FR', function () {
        expect(Language::FR->value)->toBe('fr');
    });

    it('has all supported languages defined', function () {
        $languages = Language::cases();
        $languageValues = array_map(fn($l) => $l->value, $languages);

        expect($languageValues)
            ->toContain('hu')
            ->toContain('en')
            ->toContain('de')
            ->toContain('it')
            ->toContain('ro')
            ->toContain('sk')
            ->toContain('hr')
            ->toContain('fr')
            ->toContain('es')
            ->toContain('cz')
            ->toContain('pl');
    });

    it('has exactly 11 languages', function () {
        expect(Language::cases())->toHaveCount(11);
    });
});
