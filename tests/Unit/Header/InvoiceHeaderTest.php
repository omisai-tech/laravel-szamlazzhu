<?php

use Carbon\Carbon;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\PaymentMethod;

describe('InvoiceHeader', function () {
    it('can be instantiated with default paper invoice type', function () {
        $header = new InvoiceHeader;

        expect($header)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can be instantiated with e-invoice type', function () {
        $header = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);

        expect($header)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set issue date', function () {
        $header = new InvoiceHeader;
        $date = Carbon::create(2023, 6, 15);
        $result = $header->setIssueDate($date);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set fulfillment date', function () {
        $header = new InvoiceHeader;
        $date = Carbon::create(2023, 6, 15);
        $result = $header->setFulfillment($date);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set payment due date', function () {
        $header = new InvoiceHeader;
        $date = Carbon::create(2023, 6, 23);
        $result = $header->setPaymentDue($date);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set payment method', function () {
        $header = new InvoiceHeader;
        $result = $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set currency', function () {
        $header = new InvoiceHeader;
        $result = $header->setCurrency(Currency::EUR);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set language', function () {
        $header = new InvoiceHeader;
        $result = $header->setLanguage(Language::EN);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set prefix', function () {
        $header = new InvoiceHeader;
        $result = $header->setPrefix('TEST');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set comment', function () {
        $header = new InvoiceHeader;
        $result = $header->setComment('Test comment');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set exchange bank', function () {
        $header = new InvoiceHeader;
        $result = $header->setExchangeBank('MNB');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set exchange rate', function () {
        $header = new InvoiceHeader;
        $result = $header->setExchangeRate(380.50);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set paid status', function () {
        $header = new InvoiceHeader;
        $result = $header->setPaid(true);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set order number', function () {
        $header = new InvoiceHeader;
        $result = $header->setOrderNumber('ORDER123');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set proforma number', function () {
        $header = new InvoiceHeader;
        $result = $header->setProformaNumber('PROFORMA123');

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set invoice template', function () {
        $header = new InvoiceHeader;
        $result = $header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_DEFAULT);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set EU VAT', function () {
        $header = new InvoiceHeader;
        $result = $header->setEuVat(true);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can set preview PDF', function () {
        $header = new InvoiceHeader;
        $result = $header->setPreviewPdf(true);

        expect($result)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can create complete header using helper', function () {
        $header = makeInvoiceHeader();

        expect($header)->toBeInstanceOf(InvoiceHeader::class);
    });

    it('checks if is invoice', function () {
        $header = new InvoiceHeader;

        expect($header->isInvoice())->toBeTrue();
    });

    it('checks if is not reverse invoice', function () {
        $header = new InvoiceHeader;

        expect($header->isNotReserveInvoice())->toBeTrue();
    });

    it('has correct invoice template constants', function () {
        expect(Invoice::INVOICE_TEMPLATE_DEFAULT)->toBe('SzlaMost');
        expect(Invoice::INVOICE_TEMPLATE_TRADITIONAL)->toBe('SzlaNoEnv');
        expect(Invoice::INVOICE_TEMPLATE_ENV_FRIENDLY)->toBe('SzlaAlap');
        expect(Invoice::INVOICE_TEMPLATE_8CM)->toBe('Szla8cm');
        expect(Invoice::INVOICE_TEMPLATE_RETRO)->toBe('SzlaTomb');
    });

    it('has correct invoice type constants', function () {
        expect(Invoice::INVOICE_TYPE_P_INVOICE)->toBe(1);
        expect(Invoice::INVOICE_TYPE_E_INVOICE)->toBe(2);
    });
});
