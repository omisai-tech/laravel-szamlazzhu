<?php

use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Header\ProformaHeader;
use Omisai\Szamlazzhu\Header\PrePaymentInvoiceHeader;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Item\ReceiptItem;
use Omisai\Szamlazzhu\TaxPayer;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Carbon\Carbon;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;

function makeInvoiceHeader($type = null)
{
    $header = new InvoiceHeader($type ?? Invoice::INVOICE_TYPE_E_INVOICE);
    $header->setIssueDate(Carbon::now());
    $header->setFulfillment(Carbon::now());
    $header->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $header->setCurrency(Currency::EUR);
    $header->setLanguage(Language::EN);
    $header->setExchangeBank('MNB');
    $header->setPaid(true);
    return $header;
}

function makeProformaHeader()
{
    $header = new ProformaHeader();
    $header->setIssueDate(Carbon::now());
    $header->setFulfillment(Carbon::now());
    $header->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $header->setCurrency(Currency::EUR);
    $header->setLanguage(Language::EN);
    $header->setExchangeBank('MNB');
    $header->setPaid(true);
    return $header;
}

function makePrePaymentInvoiceHeader()
{
    $header = new PrePaymentInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
    $header->setIssueDate(Carbon::now());
    $header->setFulfillment(Carbon::now());
    $header->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $header->setCurrency(Currency::EUR);
    $header->setLanguage(Language::EN);
    $header->setExchangeBank('MNB');
    $header->setPaid(true);
    return $header;
}

function makeReceiptHeader()
{
    $header = new ReceiptHeader();
    $header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_BANKCARD);
    $header->setCurrency(Currency::EUR);
    $header->setExchangeBank('MNB');
    return $header;
}

function makeSeller()
{
    $seller = new Seller();
    $seller->setBank('Wise, BIC: TRWIBEB1XXX');
    $seller->setBankAccount('BE12 1234 1234 1234');
    return $seller;
}

function makeBuyer($name = 'John Smith', $address = 'Test street 44.')
{
    $buyer = new Buyer();
    $buyer->setName($name);
    $buyer->setZipCode('1061');
    $buyer->setCity('Budapest');
    $buyer->setAddress($address);
    $buyer->setSendEmailState(false);
    $buyer->setTaxPayer(TaxPayer::TAXPAYER_NO_TAXNUMBER);
    return $buyer;
}

function makeInvoiceItem($name = 'TEST Laptop', $price = 510.0)
{
    $item = new InvoiceItem();
    $item->setName($name);
    $item->setQuantity(1.0);
    $item->setQuantityUnit('qt');
    $item->setNetUnitPrice($price);
    $item->setNetPrice(floatval($price * 1.0));
    $item->setVat(InvoiceItem::DEFAULT_VAT);
    $item->setVatAmount(floatval($price * 0.27));
    $item->setGrossAmount(floatval($price * 1.27));
    $item->setComment('Grey series one');
    return $item;
}

function makeReceiptItem($name = 'TEST Book', $price = 510.0)
{
    $item = new ReceiptItem();
    $item->setName($name);
    $item->setQuantity(1.0);
    $item->setQuantityUnit('qt');
    $item->setNetUnitPrice($price);
    $item->setNetPrice(floatval($price * 1.0));
    $item->setVat(ReceiptItem::DEFAULT_VAT);
    $item->setVatAmount(floatval($price * 0.27));
    $item->setGrossAmount(floatval($price * 1.27));
    $item->setComment('NoCode, the 1000 blank pages');
    return $item;
}
