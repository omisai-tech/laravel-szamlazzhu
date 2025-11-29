<?php

describe('Architecture', function () {
    arch('source files should not have dd or dump statements')
        ->expect('Omisai\Szamlazzhu')
        ->not->toUse(['dd', 'dump', 'ray', 'var_dump']);

    arch('exceptions should extend the base exception')
        ->expect('Omisai\Szamlazzhu\SzamlaAgentException')
        ->toExtend(Exception::class);

    arch('enums should be enums')
        ->expect('Omisai\Szamlazzhu\Currency')
        ->toBeEnum();

    arch('language should be enum')
        ->expect('Omisai\Szamlazzhu\Language')
        ->toBeEnum();

    arch('payment method should be enum')
        ->expect('Omisai\Szamlazzhu\PaymentMethod')
        ->toBeEnum();

    arch('header type should be enum')
        ->expect('Omisai\Szamlazzhu\Header\Type')
        ->toBeEnum();

    arch('traits should be traits')
        ->expect('Omisai\Szamlazzhu\FieldsValidationTrait')
        ->toBeTrait();

    arch('interfaces should be interfaces')
        ->expect('Omisai\Szamlazzhu\HasXmlBuildInterface')
        ->toBeInterface();

    arch('has xml build with request interface should be interface')
        ->expect('Omisai\Szamlazzhu\HasXmlBuildWithRequestInterface')
        ->toBeInterface();

    arch('service provider extends laravel service provider')
        ->expect('Omisai\Szamlazzhu\SzamlaAgentServiceProvider')
        ->toExtend('Illuminate\Support\ServiceProvider');

    arch('responses extend abstract response')
        ->expect('Omisai\Szamlazzhu\Response')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\Response\AbstractResponse');

    arch('document headers extend document header')
        ->expect('Omisai\Szamlazzhu\Header')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\Header\DocumentHeader')
        ->ignoring('Omisai\Szamlazzhu\Header\DocumentHeader');

    arch('items extend item base class')
        ->expect('Omisai\Szamlazzhu\Item')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\Item\Item')
        ->ignoring('Omisai\Szamlazzhu\Item\Item');

    arch('ledgers extend item ledger base class')
        ->expect('Omisai\Szamlazzhu\Ledger')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\Ledger\ItemLedger')
        ->ignoring('Omisai\Szamlazzhu\Ledger\ItemLedger');

    arch('credit notes extend credit note base class')
        ->expect('Omisai\Szamlazzhu\CreditNote')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\CreditNote\CreditNote')
        ->ignoring('Omisai\Szamlazzhu\CreditNote\CreditNote');

    arch('documents extend document base class')
        ->expect('Omisai\Szamlazzhu\Document')
        ->classes()
        ->toExtend('Omisai\Szamlazzhu\Document\Document')
        ->ignoring([
            'Omisai\Szamlazzhu\Document\Document',
            'Omisai\Szamlazzhu\Document\Invoice',
            'Omisai\Szamlazzhu\Document\Receipt',
        ]);
});

describe('Naming Conventions', function () {
    arch('classes should have correct suffix for headers')
        ->expect('Omisai\Szamlazzhu\Header')
        ->classes()
        ->toHaveSuffix('Header')
        ->ignoring('Omisai\Szamlazzhu\Header\Type');

    arch('classes should have correct suffix for items')
        ->expect('Omisai\Szamlazzhu\Item')
        ->classes()
        ->toHaveSuffix('Item');

    arch('classes should have correct suffix for ledgers')
        ->expect('Omisai\Szamlazzhu\Ledger')
        ->classes()
        ->toHaveSuffix('Ledger');

    arch('classes should have correct suffix for responses')
        ->expect('Omisai\Szamlazzhu\Response')
        ->classes()
        ->toHaveSuffix('Response');

    arch('tests should have correct suffix')
        ->expect('Omisai\Szamlazzhu\Tests')
        ->classes()
        ->toHaveSuffix('Test')
        ->ignoring([
            'Omisai\Szamlazzhu\Tests\TestCase',
            'Omisai\Szamlazzhu\Tests\Helpers',
        ]);
});

describe('Dependencies', function () {
    arch('models should not depend on controllers')
        ->expect('Omisai\Szamlazzhu')
        ->not->toUse('Illuminate\Http\Controllers');

    arch('source should not use direct database facades')
        ->expect('Omisai\Szamlazzhu')
        ->not->toUse([
            'Illuminate\Support\Facades\DB',
            'Illuminate\Support\Facades\Auth',
            'Illuminate\Support\Facades\Session',
        ]);
});

describe('Class Structure', function () {
    arch('abstract response should be abstract')
        ->expect('Omisai\Szamlazzhu\Response\AbstractResponse')
        ->toBeAbstract();

    arch('buyer should be a class')
        ->expect('Omisai\Szamlazzhu\Buyer')
        ->toBeClass();

    arch('seller should be a class')
        ->expect('Omisai\Szamlazzhu\Seller')
        ->toBeClass();

    arch('szamla agent should be a class')
        ->expect('Omisai\Szamlazzhu\SzamlaAgent')
        ->toBeClass();

    arch('szamla agent setting should be a class')
        ->expect('Omisai\Szamlazzhu\SzamlaAgentSetting')
        ->toBeClass();

    arch('cookie handler should be a class')
        ->expect('Omisai\Szamlazzhu\CookieHandler')
        ->toBeClass();

    arch('buyer ledger should be a class')
        ->expect('Omisai\Szamlazzhu\BuyerLedger')
        ->toBeClass();

    arch('tax payer should be a class')
        ->expect('Omisai\Szamlazzhu\TaxPayer')
        ->toBeClass();

    arch('szamla agent exception should be a class')
        ->expect('Omisai\Szamlazzhu\SzamlaAgentException')
        ->toBeClass();
});

describe('Namespace Organization', function () {
    arch('header classes should be in Header namespace')
        ->expect('Omisai\Szamlazzhu\Header')
        ->classes()
        ->toBeClasses();

    arch('item classes should be in Item namespace')
        ->expect('Omisai\Szamlazzhu\Item')
        ->toBeClasses();

    arch('document classes should be in Document namespace')
        ->expect('Omisai\Szamlazzhu\Document')
        ->toBeClasses();

    arch('response classes should be in Response namespace')
        ->expect('Omisai\Szamlazzhu\Response')
        ->toBeClasses();

    arch('ledger classes should be in Ledger namespace')
        ->expect('Omisai\Szamlazzhu\Ledger')
        ->toBeClasses();

    arch('credit note classes should be in CreditNote namespace')
        ->expect('Omisai\Szamlazzhu\CreditNote')
        ->toBeClasses();

    arch('waybill classes should be in Waybill namespace')
        ->expect('Omisai\Szamlazzhu\Waybill')
        ->toBeClasses();
});
