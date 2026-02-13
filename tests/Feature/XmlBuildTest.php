<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\CookieHandler;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\SzamlaAgentRequest;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

it('builds InvoiceHeader xml data', function () {
    $invoiceHeader = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
    $invoiceHeader->setIssueDate(Carbon::create('2023-06-08'));
    $invoiceHeader->setFulfillment(Carbon::create('2023-06-08'));
    $invoiceHeader->setPaymentDue(Carbon::create('2023-06-08')->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    $invoiceHeader->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $invoiceHeader->setCurrency(Currency::EUR);
    $invoiceHeader->setLanguage(Language::EN);
    $invoiceHeader->setPrefix('TEST');
    $invoiceHeader->setExchangeBank('MNB');
    $invoiceHeader->setPaid(true);

    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false);
    $request = new SzamlaAgentRequest($agent, new CookieHandler, 'generateInvoice', new Invoice);
    $xmlData = $invoiceHeader->buildXmlData($request);

    expect($xmlData)
        ->toBeArray()
        ->not()->toBeEmpty()
        ->toBe([
            'keltDatum' => '2023-06-08',
            'teljesitesDatum' => '2023-06-08',
            'fizetesiHataridoDatum' => '2023-06-16',
            'fizmod' => 'bankkártya',
            'penznem' => 'EUR',
            'szamlaNyelve' => 'en',
            'arfolyamBank' => 'MNB',
            'szamlaszamElotag' => 'TEST',
            'fizetve' => true,
            'eusAfa' => false,
            'szamlaSablon' => 'SzlaMost',
        ]);
});
