<?php

use Omisai\Szamlazzhu\Item\Item;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Ledger\InvoiceItemLedger;

describe('InvoiceItem', function () {
    it('can be instantiated', function () {
        $item = new InvoiceItem();
        expect($item)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set name', function () {
        $item = new InvoiceItem();
        $result = $item->setName('Test Product');

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set id', function () {
        $item = new InvoiceItem();
        $result = $item->setId('PROD123');

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set quantity', function () {
        $item = new InvoiceItem();
        $result = $item->setQuantity(5.0);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set quantity unit', function () {
        $item = new InvoiceItem();
        $result = $item->setQuantityUnit('pcs');

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set net unit price', function () {
        $item = new InvoiceItem();
        $result = $item->setNetUnitPrice(100.0);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set vat', function () {
        $item = new InvoiceItem();
        $result = $item->setVat('27');

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set net price', function () {
        $item = new InvoiceItem();
        $result = $item->setNetPrice(500.0);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set vat amount', function () {
        $item = new InvoiceItem();
        $result = $item->setVatAmount(135.0);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set gross amount', function () {
        $item = new InvoiceItem();
        $result = $item->setGrossAmount(635.0);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set comment', function () {
        $item = new InvoiceItem();
        $result = $item->setComment('Test comment');

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('can set ledger data', function () {
        $item = new InvoiceItem();
        $ledger = new InvoiceItemLedger('economic', 'vat', '123', '456');
        $result = $item->setLedgerData($ledger);

        expect($result)->toBeInstanceOf(InvoiceItem::class);
    });

    it('has default vat of 27', function () {
        expect(Item::DEFAULT_VAT)->toBe('27');
    });

    it('has default quantity of 1.0', function () {
        expect(Item::DEFAULT_QUANTITY)->toBe(1.0);
    });

    it('has default quantity unit of db', function () {
        expect(Item::DEFAULT_QUANTITY_UNIT)->toBe('db');
    });

    it('has VAT constants defined', function () {
        expect(Item::VAT_TAM)->toBe('TAM');
        expect(Item::VAT_AAM)->toBe('AAM');
        expect(Item::VAT_EU)->toBe('EU');
        expect(Item::VAT_EUK)->toBe('EUK');
        expect(Item::VAT_MAA)->toBe('MAA');
        expect(Item::VAT_F_AFA)->toBe('F.AFA');
        expect(Item::VAT_K_AFA)->toBe('K.AFA');
        expect(Item::VAT_AKK)->toBe('ÁKK');
    });

    it('can create complete invoice item using helper', function () {
        $item = makeInvoiceItem('Test Laptop', 510.0);

        expect($item)->toBeInstanceOf(InvoiceItem::class);
    });

    it('builds xml data correctly', function () {
        $item = new InvoiceItem();
        $item->setName('Test Product');
        $item->setQuantity(2.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(200.0);
        $item->setVatAmount(54.0);
        $item->setGrossAmount(254.0);

        $data = $item->buildXmlData();

        expect($data)->toBeArray();
        expect($data)->toHaveKey('megnevezes', 'Test Product');
        expect($data)->toHaveKey('mennyiseg', '2.00');
        expect($data)->toHaveKey('mennyisegiEgyseg', 'pcs');
        expect($data)->toHaveKey('nettoEgysegar', 100.0);
        expect($data)->toHaveKey('afakulcs', '27');
        expect($data)->toHaveKey('nettoErtek', 200.0);
        expect($data)->toHaveKey('afaErtek', 54.0);
        expect($data)->toHaveKey('bruttoErtek', 254.0);
    });

    it('includes comment in xml data when set', function () {
        $item = new InvoiceItem();
        $item->setName('Test Product');
        $item->setQuantity(1.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(100.0);
        $item->setVatAmount(27.0);
        $item->setGrossAmount(127.0);
        $item->setComment('This is a comment');

        $data = $item->buildXmlData();

        expect($data)->toHaveKey('megjegyzes', 'This is a comment');
    });

    it('includes id in xml data when set', function () {
        $item = new InvoiceItem();
        $item->setId('ITEM123');
        $item->setName('Test Product');
        $item->setQuantity(1.0);
        $item->setQuantityUnit('pcs');
        $item->setNetUnitPrice(100.0);
        $item->setVat('27');
        $item->setNetPrice(100.0);
        $item->setVatAmount(27.0);
        $item->setGrossAmount(127.0);

        $data = $item->buildXmlData();

        expect($data)->toHaveKey('azonosito', 'ITEM123');
    });
});
