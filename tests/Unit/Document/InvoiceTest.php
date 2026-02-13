<?php

use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Seller;

describe('Invoice Document', function () {
    it('can be instantiated with default e-invoice type', function () {
        $invoice = new Invoice;

        expect($invoice)->toBeInstanceOf(Invoice::class);
    });

    it('can be instantiated with paper invoice type', function () {
        $invoice = new Invoice(Invoice::INVOICE_TYPE_P_INVOICE);

        expect($invoice)->toBeInstanceOf(Invoice::class);
    });

    it('can be instantiated with e-invoice type', function () {
        $invoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);

        expect($invoice)->toBeInstanceOf(Invoice::class);
    });

    it('can get and set header', function () {
        $invoice = new Invoice;
        $header = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
        $result = $invoice->setHeader($header);

        expect($result)->toBeInstanceOf(Invoice::class);
        expect($invoice->getHeader())->toBeInstanceOf(InvoiceHeader::class);
    });

    it('can get and set seller', function () {
        $invoice = new Invoice;
        $seller = makeSeller();
        $result = $invoice->setSeller($seller);

        expect($result)->toBeInstanceOf(Invoice::class);
        expect($invoice->getSeller())->toBeInstanceOf(Seller::class);
    });

    it('can get and set buyer', function () {
        $invoice = new Invoice;
        $buyer = makeBuyer();
        $result = $invoice->setBuyer($buyer);

        expect($result)->toBeInstanceOf(Invoice::class);
        expect($invoice->getBuyer())->toBeInstanceOf(Buyer::class);
    });

    it('can add item', function () {
        $invoice = new Invoice;
        $item = makeInvoiceItem();
        $result = $invoice->addItem($item);

        expect($result)->toBeInstanceOf(Invoice::class);
    });

    it('can set items', function () {
        $invoice = new Invoice;
        $items = [makeInvoiceItem(), makeInvoiceItem('Another Product', 200.0)];
        $result = $invoice->setItems($items);

        expect($result)->toBeInstanceOf(Invoice::class);
    });

    it('can set additive', function () {
        $invoice = new Invoice;
        $result = $invoice->setAdditive(false);

        expect($result)->toBeInstanceOf(Invoice::class);
        expect($invoice->isAdditive())->toBeFalse();
    });

    it('is additive by default', function () {
        $invoice = new Invoice;

        expect($invoice->isAdditive())->toBeTrue();
    });

    it('has correct invoice type constants', function () {
        expect(Invoice::INVOICE_TYPE_P_INVOICE)->toBe(1);
        expect(Invoice::INVOICE_TYPE_E_INVOICE)->toBe(2);
    });

    it('has correct query type constants', function () {
        expect(Invoice::FROM_INVOICE_NUMBER)->toBe(1);
        expect(Invoice::FROM_ORDER_NUMBER)->toBe(2);
        expect(Invoice::FROM_INVOICE_EXTERNAL_ID)->toBe(3);
    });

    it('has correct limits', function () {
        expect(Invoice::CREDIT_NOTES_LIMIT)->toBe(5);
        expect(Invoice::INVOICE_ATTACHMENTS_LIMIT)->toBe(5);
    });

    it('has correct template constants', function () {
        expect(Invoice::INVOICE_TEMPLATE_DEFAULT)->toBe('SzlaMost');
        expect(Invoice::INVOICE_TEMPLATE_TRADITIONAL)->toBe('SzlaNoEnv');
        expect(Invoice::INVOICE_TEMPLATE_ENV_FRIENDLY)->toBe('SzlaAlap');
        expect(Invoice::INVOICE_TEMPLATE_8CM)->toBe('Szla8cm');
        expect(Invoice::INVOICE_TEMPLATE_RETRO)->toBe('SzlaTomb');
    });
});
