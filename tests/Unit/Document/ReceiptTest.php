<?php

use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Item\ReceiptItem;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\CreditNote\ReceiptCreditNote;
use Omisai\Szamlazzhu\PaymentMethod;

describe('Receipt Document', function () {
    it('can be instantiated without receipt number', function () {
        $receipt = new Receipt();

        expect($receipt)->toBeInstanceOf(Receipt::class);
    });

    it('can be instantiated with receipt number', function () {
        $receipt = new Receipt('REC123');

        expect($receipt)->toBeInstanceOf(Receipt::class);
    });

    it('can get and set header', function () {
        $receipt = new Receipt();
        $header = new ReceiptHeader();
        $result = $receipt->setHeader($header);

        expect($result)->toBeInstanceOf(Receipt::class);
        expect($receipt->getHeader())->toBeInstanceOf(ReceiptHeader::class);
    });

    it('can get and set seller', function () {
        $receipt = new Receipt();
        $seller = makeSeller();
        $result = $receipt->setSeller($seller);

        expect($result)->toBeInstanceOf(Receipt::class);
        expect($receipt->getSeller())->toBeInstanceOf(Seller::class);
    });

    it('can get and set buyer', function () {
        $receipt = new Receipt();
        $buyer = makeBuyer();
        $result = $receipt->setBuyer($buyer);

        expect($result)->toBeInstanceOf(Receipt::class);
        expect($receipt->getBuyer())->toBeInstanceOf(Buyer::class);
    });

    it('can add item', function () {
        $receipt = new Receipt();
        $item = makeReceiptItem();
        $receipt->addItem($item);

        expect(true)->toBeTrue();
    });

    it('can set items', function () {
        $receipt = new Receipt();
        $items = [makeReceiptItem(), makeReceiptItem('Another Book', 300.0)];
        $result = $receipt->setItems($items);

        expect($result)->toBeInstanceOf(Receipt::class);
    });

    it('can add credit note', function () {
        $receipt = new Receipt();
        $creditNote = new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_CASH, 100.0, 'Payment');
        $result = $receipt->addCreditNote($creditNote);

        expect($result)->toBeInstanceOf(Receipt::class);
    });

    it('can set credit notes', function () {
        $receipt = new Receipt();
        $creditNotes = [
            new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_CASH, 50.0),
            new ReceiptCreditNote(PaymentMethod::PAYMENT_METHOD_BANKCARD, 50.0),
        ];
        $result = $receipt->setCreditNotes($creditNotes);

        expect($result)->toBeInstanceOf(Receipt::class);
    });

    it('has credit notes limit of 5', function () {
        expect(Receipt::CREDIT_NOTES_LIMIT)->toBe(5);
    });
});
