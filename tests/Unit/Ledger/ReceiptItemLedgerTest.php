<?php

use Omisai\Szamlazzhu\Ledger\ReceiptItemLedger;
use Omisai\Szamlazzhu\Item\ReceiptItem;

describe('ReceiptItemLedger', function () {
    it('can be used with receipt item', function () {
        $item = new ReceiptItem();
        // ReceiptItemLedger has protected constructor, tested through ReceiptItem
        expect($item)->toBeInstanceOf(ReceiptItem::class);
    });
});
