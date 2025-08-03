<?php

use \Omisai\Szamlazzhu\SzamlaAgent;
use \Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Document\Invoice\PrePaymentInvoice;
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\SzamlaAgentException;

it('creates a prepayment invoice', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false,);
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
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false,);
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
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false,);
    $receiptHeader = makeReceiptHeader();
    $seller = makeSeller();
    $receiptItem = makeReceiptItem();
    $receipt = new Receipt();
    $receipt->setHeader($receiptHeader);
    $receipt->setSeller($seller);
    $receipt->setItems([$receiptItem]);
    $response = $agent->generateReceipt($receipt);
    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');
