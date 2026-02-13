<?php

use Omisai\Szamlazzhu\Item\ReceiptItem;

describe('ReceiptItem', function () {
    it('can be instantiated', function () {
        $item = new ReceiptItem;
        expect($item)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set name', function () {
        $item = new ReceiptItem;
        $result = $item->setName('Test Receipt Product');

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set id', function () {
        $item = new ReceiptItem;
        $result = $item->setId('REC123');

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set quantity', function () {
        $item = new ReceiptItem;
        $result = $item->setQuantity(3.0);

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set quantity unit', function () {
        $item = new ReceiptItem;
        $result = $item->setQuantityUnit('qt');

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set net unit price', function () {
        $item = new ReceiptItem;
        $result = $item->setNetUnitPrice(50.0);

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set vat', function () {
        $item = new ReceiptItem;
        $result = $item->setVat('27');

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set net price', function () {
        $item = new ReceiptItem;
        $result = $item->setNetPrice(150.0);

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set vat amount', function () {
        $item = new ReceiptItem;
        $result = $item->setVatAmount(40.5);

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set gross amount', function () {
        $item = new ReceiptItem;
        $result = $item->setGrossAmount(190.5);

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can set comment', function () {
        $item = new ReceiptItem;
        $result = $item->setComment('Receipt item comment');

        expect($result)->toBeInstanceOf(ReceiptItem::class);
    });

    it('can create complete receipt item using helper', function () {
        $item = makeReceiptItem('Test Book', 510.0);

        expect($item)->toBeInstanceOf(ReceiptItem::class);
    });

    it('builds xml data correctly', function () {
        $item = new ReceiptItem;
        $item->setName('Receipt Product');
        $item->setQuantity(2.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(200.0);
        $item->setVatAmount(54.0);
        $item->setGrossAmount(254.0);

        $data = $item->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('megnevezes', 'Receipt Product');
        expect($data)->toHaveKey('mennyiseg', '2.00');
        expect($data)->toHaveKey('mennyisegiEgyseg', 'pcs');
        expect($data)->toHaveKey('nettoEgysegar', 100.0);
        expect($data)->toHaveKey('afakulcs', '27');
        expect($data)->toHaveKey('netto', '200.00');
        expect($data)->toHaveKey('afa', '54.00');
        expect($data)->toHaveKey('brutto', '254.00');
    });

    it('includes comment in xml data when set', function () {
        $item = new ReceiptItem;
        $item->setName('Receipt Product');
        $item->setQuantity(1.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(100.0);
        $item->setVatAmount(27.0);
        $item->setGrossAmount(127.0);
        $item->setComment('Receipt comment');

        $data = $item->buildXmlData();

        expect($data)->toHaveKey('megjegyzes', 'Receipt comment');
    });

    it('includes id in xml data when set', function () {
        $item = new ReceiptItem;
        $item->setId('REC456');
        $item->setName('Receipt Product');
        $item->setQuantity(1.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(100.0);
        $item->setVatAmount(27.0);
        $item->setGrossAmount(127.0);

        $data = $item->buildXmlData();

        expect($data)->toHaveKey('azonosito', 'REC456');
    });
});
