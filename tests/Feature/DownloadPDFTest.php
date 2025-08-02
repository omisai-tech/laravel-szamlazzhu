<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Document\Proforma;
use Omisai\Szamlazzhu\Header\ProformaHeader;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Omisai\Szamlazzhu\TaxPayer;

it('can access files', function () {
    $files = Storage::disk(config('szamlazzhu.pdf.disk'))
        ->files(config('szamlazzhu.pdf.path'));

    expect($files)
        ->toBeArray();
});

it('can access directories', function () {
    $directories = Storage::disk(config('szamlazzhu.pdf.disk'))
        ->directories(config('szamlazzhu.pdf.path'));

    expect($directories)
        ->toBeArray();
});

it('can store file', function () {
    $disk = config('szamlazzhu.pdf.disk');
    $path = config('szamlazzhu.pdf.path');
    $filename = 'test-file.txt';
    $content = 'This is a test file.';

    Storage::disk($disk)->put(sprintf('%s/%s', $path, $filename), $content);

    expect(Storage::disk($disk)->exists(sprintf('%s/%s', $path, $filename)))
        ->toBeTrue();
});

it('can delete file', function () {
    $disk = config('szamlazzhu.pdf.disk');
    $path = config('szamlazzhu.pdf.path');
    $filename = 'test-file.txt';

    Storage::disk($disk)->delete(sprintf('%s/%s', $path, $filename));

    expect(Storage::disk($disk)->exists(sprintf('%s/%s', $path, $filename)))
        ->toBeFalse();
});

it('can download proforma invoice PDF', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), true);

    $invoiceHeader = new ProformaHeader();
    $invoiceHeader->setIssueDate(Carbon::now());
    $invoiceHeader->setFulfillment(Carbon::now());
    $invoiceHeader->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    $invoiceHeader->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $invoiceHeader->setCurrency(Currency::EUR);
    $invoiceHeader->setLanguage(Language::EN);
    if (null !== config('szamlazzhu.test_prefix')) {
        $invoiceHeader->setPrefix(config('szamlazzhu.test_prefix'));
    }
    $invoiceHeader->setExchangeBank('MNB');
    $invoiceHeader->setPaid(true);
    $seller = new Seller();
    $seller->setBank('Wise, BIC: TRWIBEB1XXX');
    $seller->setBankAccount('BE12 1234 1234 1234');

    $buyer = new Buyer();
    $buyer->setName('Proforma Doe');
    $buyer->setZipCode('1061');
    $buyer->setCity('Budapest');
    $buyer->setAddress('Test street 11.');
    $buyer->setSendEmailState(false);
    $buyer->setTaxPayer(TaxPayer::TAXPAYER_NO_TAXNUMBER);

    $invoiceItem = new InvoiceItem();
    $invoiceItem->setName('TEST Laptop');
    $invoiceItem->setQuantity(1.0);
    $invoiceItem->setQuantityUnit('qt');
    $invoiceItem->setNetUnitPrice(510.0);
    $invoiceItem->setNetPrice(floatval(510.0 * 1.0));
    $invoiceItem->setVat(InvoiceItem::DEFAULT_VAT);
    $invoiceItem->setVatAmount(floatval(510.0 * 0.27));
    $invoiceItem->setGrossAmount(floatval(510.0 * 1.27));
    $invoiceItem->setComment('Grey series one');


    $proforma = new Proforma(Invoice::INVOICE_TYPE_E_INVOICE);
    $proforma->setHeader($invoiceHeader);
    $proforma->setSeller($seller);
    $proforma->setBuyer($buyer);
    $proforma->setItems([$invoiceItem]);

    $response = $agent->generateProforma($proforma);

    expect($response->isSuccess())->toBeTrue();

    $proformaNumber = $response->getInvoiceNumber() ?? $response->getDocumentNumber();

    expect($proformaNumber)->not->toBeNull();
    expect(Storage::disk(config('szamlazzhu.pdf.disk'))->exists(sprintf('%s/pdf/%s.pdf', config('szamlazzhu.pdf.path'), $proformaNumber)))
        ->toBeTrue();
});
