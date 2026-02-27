<?php

use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Document\Invoice\PrePaymentInvoice;
use Omisai\Szamlazzhu\Document\Invoice\ReverseInvoice;
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\SzamlaAgentException;

it('creates a prepayment invoice', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false);
    $invoiceHeader = makePrePaymentInvoiceHeader();
    $seller = makeSeller();
    $buyer = makeBuyer('Pre Smith');
    $invoiceItem = makeInvoiceItem();
    $invoice = new PrePaymentInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
    $invoice->setHeader($invoiceHeader);
    $invoice->setSeller($seller);
    $invoice->setBuyer($buyer);
    $invoice->setItems([$invoiceItem]);
    $response = $agent->generateInvoice($invoice);
    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');

it('creates an invoice', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false);
    $invoiceHeader = makeInvoiceHeader();
    $seller = makeSeller();
    $buyer = makeBuyer();
    $invoiceItem = makeInvoiceItem();
    $invoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);
    $invoice->setHeader($invoiceHeader);
    $invoice->setSeller($seller);
    $invoice->setBuyer($buyer);
    $invoice->setItems([$invoiceItem]);
    $response = $agent->generateInvoice($invoice);
    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');

it('creates a receipt', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false);
    $receiptHeader = makeReceiptHeader();
    $seller = makeSeller();
    $receiptItem = makeReceiptItem();
    $receipt = new Receipt;
    $receipt->setHeader($receiptHeader);
    $receipt->setSeller($seller);
    $receipt->setItems([$receiptItem]);
    $response = $agent->generateReceipt($receipt);
    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');

it('creates a reverse invoice for an original invoice', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false);

    $originalInvoiceHeader = makeInvoiceHeader();
    $seller = makeSeller();
    $buyer = makeBuyer('Reverse Smith');
    $invoiceItem = makeInvoiceItem();
    $originalInvoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);
    $originalInvoice->setHeader($originalInvoiceHeader);
    $originalInvoice->setSeller($seller);
    $originalInvoice->setBuyer($buyer);
    $originalInvoice->setItems([$invoiceItem]);
    $originalResponse = $agent->generateInvoice($originalInvoice);

    expect($originalResponse->isSuccess())->toBeTrue();

    $invoiceDocument = new ReverseInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
    $header = $invoiceDocument->getHeader();
    $header->setInvoiceNumber($originalResponse->getInvoiceNumber());

    $invoiceDocument
        ->setHeader($header)
        ->setSeller($seller)
        ->setBuyer($buyer);

    $response = $agent->generateReverseInvoice($invoiceDocument);

    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');
