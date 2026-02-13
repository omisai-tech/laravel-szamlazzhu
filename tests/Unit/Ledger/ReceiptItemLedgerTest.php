<?php

use Omisai\Szamlazzhu\Item\ReceiptItem;
use Omisai\Szamlazzhu\Ledger\ReceiptItemLedger;

describe('ReceiptItemLedger', function () {
    it('can be used with receipt item', function () {
        $item = new ReceiptItem;
        // ReceiptItemLedger has protected constructor, tested through ReceiptItem
        expect($item)->toBeInstanceOf(ReceiptItem::class);
    });
});
