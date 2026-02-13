<?php

use Omisai\Szamlazzhu\Response\AbstractResponse;
use Omisai\Szamlazzhu\SzamlaAgentSetting;

describe('SzamlaAgentSetting', function () {
    it('can be instantiated with all parameters', function () {
        $setting = new SzamlaAgentSetting(
            'user@example.com',
            'password123',
            'test-api-key-12345678901234567890',
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            'WooCommerce'
        );

        expect($setting)->toBeInstanceOf(SzamlaAgentSetting::class);
        expect($setting->getUsername())->toBe('user@example.com');
        expect($setting->getPassword())->toBe('password123');
        expect($setting->getApiKey())->toBe('test-api-key-12345678901234567890');
    });

    it('can set and get username', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setUsername('newuser@example.com');

        expect($setting->getUsername())->toBe('newuser@example.com');
    });

    it('can set and get password', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setPassword('newpassword');

        expect($setting->getPassword())->toBe('newpassword');
    });

    it('can set and get api key', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setApiKey('new-api-key-12345678901234567890');

        expect($setting->getApiKey())->toBe('new-api-key-12345678901234567890');
    });

    it('can set and get download pdf', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setDownloadPdf(false);

        expect($setting->isDownloadPdf())->toBeFalse();
    });

    it('can set and get download copies count', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setDownloadCopiesCount(3);

        expect($setting->getDownloadCopiesCount())->toBe(3);
    });

    it('can set and get response type', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setResponseType(AbstractResponse::RESULT_AS_XML);

        expect($setting->getResponseType())->toBe(AbstractResponse::RESULT_AS_XML);
    });

    it('can set and get aggregator', function () {
        $setting = new SzamlaAgentSetting(
            null,
            null,
            null,
            true,
            1,
            AbstractResponse::RESULT_AS_TEXT,
            ''
        );
        $setting->setAggregator('WooCommerce');

        expect($setting->getAggregator())->toBe('WooCommerce');
    });

    it('has correct API_KEY_LENGTH constant', function () {
        expect(SzamlaAgentSetting::API_KEY_LENGTH)->toBe(42);
    });

    it('has correct DOWNLOAD_COPIES_COUNT constant', function () {
        expect(SzamlaAgentSetting::DOWNLOAD_COPIES_COUNT)->toBe(1);
    });
});
