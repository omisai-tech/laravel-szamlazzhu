<?php

use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\Language;

describe('Document', function () {
    it('returns default currency', function () {
        $currency = Document::getDefaultCurrency();

        expect($currency)->toBe(Currency::FT);
    });

    it('returns default language', function () {
        $language = Document::getDefaultLanguage();

        expect($language)->toBe(Language::HU);
    });

    it('has correct document type constants for invoice', function () {
        expect(Document::DOCUMENT_TYPE_INVOICE)->toBe('invoice');
        expect(Document::DOCUMENT_TYPE_INVOICE_CODE)->toBe('SZ');
    });

    it('has correct document type constants for reverse invoice', function () {
        expect(Document::DOCUMENT_TYPE_REVERSE_INVOICE)->toBe('reverseInvoice');
        expect(Document::DOCUMENT_TYPE_REVERSE_INVOICE_CODE)->toBe('SS');
    });

    it('has correct document type constants for pay invoice', function () {
        expect(Document::DOCUMENT_TYPE_PAY_INVOICE)->toBe('payInvoice');
        expect(Document::DOCUMENT_TYPE_PAY_INVOICE_CODE)->toBe('JS');
    });

    it('has correct document type constants for corrective invoice', function () {
        expect(Document::DOCUMENT_TYPE_CORRECTIVE_INVOICE)->toBe('correctiveInvoice');
        expect(Document::DOCUMENT_TYPE_CORRECTIVE_INVOICE_CODE)->toBe('HS');
    });

    it('has correct document type constants for prepayment invoice', function () {
        expect(Document::DOCUMENT_TYPE_PREPAYMENT_INVOICE)->toBe('prePaymentInvoice');
        expect(Document::DOCUMENT_TYPE_PREPAYMENT_INVOICE_CODE)->toBe('ES');
    });

    it('has correct document type constants for final invoice', function () {
        expect(Document::DOCUMENT_TYPE_FINAL_INVOICE)->toBe('finalInvoice');
        expect(Document::DOCUMENT_TYPE_FINAL_INVOICE_CODE)->toBe('VS');
    });

    it('has correct document type constants for proforma', function () {
        expect(Document::DOCUMENT_TYPE_PROFORMA)->toBe('proforma');
        expect(Document::DOCUMENT_TYPE_PROFORMA_CODE)->toBe('D');
    });

    it('has correct document type constants for delivery note', function () {
        expect(Document::DOCUMENT_TYPE_DELIVERY_NOTE)->toBe('deliveryNote');
        expect(Document::DOCUMENT_TYPE_DELIVERY_NOTE_CODE)->toBe('SL');
    });

    it('has correct document type constants for receipt', function () {
        expect(Document::DOCUMENT_TYPE_RECEIPT)->toBe('receipt');
        expect(Document::DOCUMENT_TYPE_RECEIPT_CODE)->toBe('NY');
    });

    it('has correct document type constants for reserve receipt', function () {
        expect(Document::DOCUMENT_TYPE_RESERVE_RECEIPT)->toBe('reserveReceipt');
        expect(Document::DOCUMENT_TYPE_RESERVE_RECEIPT_CODE)->toBe('SN');
    });
});
