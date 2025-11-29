<?php

use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\Response\AbstractResponse;

describe('SzamlaAgent', function () {
    it('has correct API_ENDPOINT_URL constant', function () {
        expect(SzamlaAgent::API_ENDPOINT_URL)->toBe('https://www.szamlazz.hu/szamla/');
    });

    it('has correct PDF_FILE_SAVE_PATH constant', function () {
        expect(SzamlaAgent::PDF_FILE_SAVE_PATH)->toBe('pdf');
    });

    it('has correct XML_FILE_SAVE_PATH constant', function () {
        expect(SzamlaAgent::XML_FILE_SAVE_PATH)->toBe('xmls');
    });

    it('can be created with API key', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-1234567890123456789012', true);

        expect($agent)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can be created with API key and download pdf option', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-1234567890123456789013', true);

        expect($agent)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can be created with API key and response type', function () {
        $agent = SzamlaAgent::createWithAPIkey(
            'test-api-key-1234567890123456789014',
            true,
            AbstractResponse::RESULT_AS_XML
        );

        expect($agent)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can be created with API key and aggregator', function () {
        $agent = SzamlaAgent::createWithAPIkey(
            'test-api-key-1234567890123456789015',
            true,
            AbstractResponse::RESULT_AS_XML,
            'WooCommerce'
        );

        expect($agent)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can set download pdf', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx01', true);
        $result = $agent->setDownloadPdf(true);

        expect($result)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can get download pdf status', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx02', true);
        $agent->setDownloadPdf(true);

        expect($agent->isDownloadPdf())->toBeTrue();
    });

    it('can set pdf file saveable', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx03', true);
        $agent->setPdfFileSaveable(true);

        expect($agent->isPdfFileSaveable())->toBeTrue();
    });

    it('can get pdf file saveable status', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx04', true);
        $agent->setPdfFileSaveable(false);

        expect($agent->isPdfFileSaveable())->toBeFalse();
    });

    it('can set xml file saveable', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx05', true);
        $agent->setXmlFileSave(true);

        expect($agent->isXmlFileSave())->toBeTrue();
    });

    it('can get xml file saveable status', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx06', true);
        $agent->setXmlFileSave(false);

        expect($agent->isXmlFileSave())->toBeFalse();
    });

    it('can set response type', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx07', true);
        $result = $agent->setResponseType(AbstractResponse::RESULT_AS_XML);

        expect($result)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can get response type', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx08', true);
        $agent->setResponseType(AbstractResponse::RESULT_AS_XML);

        expect($agent->getResponseType())->toBe(AbstractResponse::RESULT_AS_XML);
    });

    it('can set request timeout', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx09', true);
        $agent->setRequestTimeout(60);

        expect($agent->getRequestTimeout())->toBe(60);
    });

    it('can get setting', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx10', true);

        expect($agent->getSetting())->toBeInstanceOf(\Omisai\Szamlazzhu\SzamlaAgentSetting::class);
    });

    it('can get cookie handler', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx11', true);

        expect($agent->getCookieHandler())->toBeInstanceOf(\Omisai\Szamlazzhu\CookieHandler::class);
    });

    it('can get singleton status', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx12', true);

        expect($agent->getSingleton())->toBeBool();
    });

    it('can add custom HTTP headers', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx13', true);
        $result = $agent->addCustomHTTPHeader('X-Custom-Header', 'custom-value');

        expect($result)->toBeInstanceOf(SzamlaAgent::class);
    });

    it('can get custom HTTP headers', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx14', true);
        $agent->addCustomHTTPHeader('X-Custom-Header', 'custom-value');

        expect($agent->getCustomHTTPHeaders())->toBeArray();
        expect($agent->getCustomHTTPHeaders())->toHaveKey('X-Custom-Header', 'custom-value');
    });

    it('can remove custom HTTP headers', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx15', true);
        $agent->addCustomHTTPHeader('X-Custom-Header', 'custom-value');
        $result = $agent->removeCustomHTTPHeader('X-Custom-Header');

        expect($result)->toBeInstanceOf(SzamlaAgent::class);
        expect($agent->getCustomHTTPHeaders())->not->toHaveKey('X-Custom-Header');
    });

    it('can set and get aggregator', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx16', true);
        $agent->setAggregator('WooCommerce');

        expect($agent->getAggregator())->toBe('WooCommerce');
    });

    it('can set and get download copies count', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx17', true);
        $agent->setDownloadCopiesCount(3);

        expect($agent->getDownloadCopiesCount())->toBe(3);
    });

    it('can set and get username', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx18', true);
        $agent->setUsername('test@example.com');

        expect($agent->getUsername())->toBe('test@example.com');
    });

    it('can set and get password', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx19', true);
        $agent->setPassword('secret123');

        expect($agent->getPassword())->toBe('secret123');
    });

    it('can get agents list', function () {
        $agents = SzamlaAgent::getAgents();

        expect($agents)->toBeArray();
    });

    it('can check if not xml file save', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx20', true);
        $agent->setXmlFileSave(false);

        expect($agent->isNotXmlFileSave())->toBeTrue();
    });

    it('can set request xml file save', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx21', true);
        $agent->setRequestXmlFileSave(true);

        expect($agent->isRequestXmlFileSave())->toBeTrue();
    });

    it('can set response xml file save', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx22', true);
        $agent->setResponseXmlFileSave(true);

        expect($agent->isResponseXmlFileSave())->toBeTrue();
    });

    it('can set guardian', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx23', true);
        $agent->setGuardian(true);

        expect($agent->getGuardian())->toBeTrue();
    });

    it('can set invoice external id', function () {
        $agent = SzamlaAgent::createWithAPIkey('test-api-key-abcdefghijklmnopqrstuvwx24', true);
        $agent->setInvoiceExternalId('EXT123');

        expect($agent->getInvoiceExternalId())->toBe('EXT123');
    });
});
