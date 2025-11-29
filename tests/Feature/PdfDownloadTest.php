<?php

use Illuminate\Support\Facades\Storage;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Document\Proforma;
use Omisai\Szamlazzhu\SzamlaAgent;

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
    $agent->setDownloadPdf(true);
    $agent->setPdfFileSaveable(true);
    $invoiceHeader = makeProformaHeader();
    $seller = makeSeller();
    $buyer = makeBuyer('Proforma Doe', 'Test street 11.');
    $invoiceItem = makeInvoiceItem();
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
})->skipIfConfigNotSet('szamlazzhu.api_key');
