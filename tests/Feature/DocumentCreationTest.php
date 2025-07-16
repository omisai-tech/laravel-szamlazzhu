<?php

use Carbon\Carbon;
use \Omisai\Szamlazzhu\SzamlaAgent;
use \Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Document\Invoice\PrePaymentInvoice;
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Header\PrePaymentInvoiceHeader;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Item\ReceiptItem;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Omisai\Szamlazzhu\TaxPayer;
use Omisai\Szamlazzhu\SzamlaAgentException;

it('creates a prepayment invoice', function () {
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), false,);

    $invoiceHeader = new PrePaymentInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
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
    $buyer->setName('Pre Smith');
    $buyer->setZipCode('1061');
    $buyer->setCity('Budapest');
    $buyer->setAddress('Test street 44.');
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

    $invoiceHeader = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
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
    $buyer->setName('John Smith');
    $buyer->setZipCode('1061');
    $buyer->setCity('Budapest');
    $buyer->setAddress('Test street 44.');
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

    $receiptHeader = new ReceiptHeader();
    $receiptHeader->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $receiptHeader->setCurrency(Currency::EUR);
    $receiptHeader->setExchangeBank('MNB');
    $receiptHeader->setPrefix('TREFX');


    $seller = new Seller();
    $seller->setBank('Wise, BIC: TRWIBEB1XXX');
    $seller->setBankAccount('BE12 1234 1234 1234');

    $buyer = new Buyer();
    $buyer->setName('Sarah Johnson');
    $buyer->setZipCode('1061');
    $buyer->setCity('Budapest');
    $buyer->setAddress('Test street 44.');
    $buyer->setSendEmailState(false);
    $buyer->setTaxPayer(TaxPayer::TAXPAYER_NO_TAXNUMBER);

    $receiptItem = new ReceiptItem();
    $receiptItem->setName('TEST Book');
    $receiptItem->setQuantity(1.0);
    $receiptItem->setQuantityUnit('qt');
    $receiptItem->setNetUnitPrice(510.0);
    $receiptItem->setNetPrice(floatval(510.0 * 1.0));
    $receiptItem->setVat(ReceiptItem::DEFAULT_VAT);
    $receiptItem->setVatAmount(floatval(510.0 * 0.27));
    $receiptItem->setGrossAmount(floatval(510.0 * 1.27));
    $receiptItem->setComment('NoCode, the 1000 blank pages');

    $receipt = new Receipt();
    $receipt->setHeader($receiptHeader);
    $receipt->setSeller($seller);
    $receipt->setBuyer($buyer);
    $receipt->setItems([$receiptItem]);

    $response = $agent->generateReceipt($receipt);

    expect($response->isSuccess())
        ->toBeTrue()
        ->not->toThrow(SzamlaAgentException::class);
})->skipIfConfigNotSet('szamlazzhu.api_key');
