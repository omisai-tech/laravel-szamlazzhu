# Usage Guide

This comprehensive guide shows you how to use the Laravel Szamlazz.hu package for various billing operations.

## Table of Contents

- [Installation & Configuration](#installation--configuration)
- [Basic Setup](#basic-setup)
- [Creating Invoices](#creating-invoices)
- [Creating Receipts](#creating-receipts)
- [Creating Proforma Invoices](#creating-proforma-invoices)
- [Working with Waybills](#working-with-waybills)
- [Advanced Features](#advanced-features)
- [Error Handling](#error-handling)
- [Configuration Options](#configuration-options)

## Installation & Configuration

### Install the package

```bash
composer require omisai/laravel-szamlazzhu
```

### Publish the configuration

```bash
php artisan vendor:publish --tag=szamlazzhu-config
```

### Set up your environment

Add your Szamlazz.hu API credentials to your `.env` file:

```env
SZAMLAZZHU_API_KEY=your_api_key_here

# Optional file saving configurations
SZAMLAZZHU_XML_FILE_SAVE=false
SZAMLAZZHU_XML_REQUEST_FILE_SAVE=false
SZAMLAZZHU_XML_RESPONSE_FILE_SAVE=false
SZAMLAZZHU_PDF_FILE_SAVE=true

# Optional logging configuration
SZAMLAZZHU_LOG_FILENAME=szamlazzhu
SZAMLAZZHU_LOG_LEVEL=warning
SZAMLAZZHU_LOG_EMAIL=your-email@example.com
```

## Basic Setup

### Initialize the SzamlaAgent

```php
use Omisai\Szamlazzhu\SzamlaAgent;

// Create agent with API key
$agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), true);

// Or create with username/password (legacy)
$agent = SzamlaAgent::create('username', 'password', true);
```

### Set up Seller (Your Company)

```php
use Omisai\Szamlazzhu\Seller;

$seller = new Seller();
$seller->setBank('Your Bank Name, BIC: YOURBIC');
$seller->setBankAccount('Your Bank Account Number');
```

### Set up Buyer (Customer)

```php
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\TaxPayer;

$buyer = new Buyer();
$buyer->setName('Customer Name')
      ->setZipCode('1061')
      ->setCity('Budapest')
      ->setAddress('Customer Address 123.')
      ->setEmail('customer@example.com')
      ->setSendEmailState(true)
      ->setTaxPayer(TaxPayer::TAXPAYER_NO_TAXNUMBER);

// For taxpayers with tax number
$buyer->setTaxNumber('12345678-1-23')
      ->setTaxPayer(TaxPayer::TAXPAYER_HAS_TAXNUMBER);
```

## Creating Invoices

### Basic Invoice

```php
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Language;
use Carbon\Carbon;

// Create invoice header
$header = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
$header->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8))
       ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
       ->setCurrency(Currency::HUF)
       ->setLanguage(Language::HU)
       ->setPrefix('INV')
       ->setPaid(false);

// Create invoice items
$item = new InvoiceItem();
$item->setName('Product Name')
     ->setQuantity(2.0)
     ->setQuantityUnit('pcs')
     ->setNetUnitPrice(10000.0)
     ->setNetPrice(20000.0)
     ->setVat(InvoiceItem::VAT_27)
     ->setVatAmount(5400.0)
     ->setGrossAmount(25400.0)
     ->setComment('Product description');

// Create and send invoice
$invoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);
$invoice->setHeader($header)
        ->setSeller($seller)
        ->setBuyer($buyer)
        ->setItems([$item]);

$response = $agent->generateInvoice($invoice);

if ($response->isSuccess()) {
    echo "Invoice created successfully!";
    echo "Invoice number: " . $response->getInvoiceNumber();
    echo "Invoice ID: " . $response->getInvoiceId();
} else {
    echo "Error: " . $response->getErrorMessage();
}
```

### Pre-payment Invoice

```php
use Omisai\Szamlazzhu\Document\Invoice\PrePaymentInvoice;
use Omisai\Szamlazzhu\Header\PrePaymentInvoiceHeader;

$header = new PrePaymentInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
$header->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8))
       ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
       ->setCurrency(Currency::HUF)
       ->setLanguage(Language::HU);

$invoice = new PrePaymentInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
$invoice->setHeader($header)
        ->setSeller($seller)
        ->setBuyer($buyer)
        ->setItems([$item]);

$response = $agent->generateInvoice($invoice);
```

### Final Invoice

```php
use Omisai\Szamlazzhu\Document\Invoice\FinalInvoice;
use Omisai\Szamlazzhu\Header\FinalInvoiceHeader;

$header = new FinalInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
$header->setPrePaymentInvoiceNumber('PREP-2024-001') // Reference to pre-payment invoice
       ->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8))
       ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
       ->setCurrency(Currency::HUF);

$invoice = new FinalInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
// ... set other properties
```

### Corrective Invoice

```php
use Omisai\Szamlazzhu\Document\Invoice\CorrectiveInvoice;
use Omisai\Szamlazzhu\Header\CorrectiveInvoiceHeader;

$header = new CorrectiveInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
$header->setCorrectiveNumber('INV-2024-001') // Original invoice to correct
       ->setCorrectionToPay(-1000.0) // Amount to correct
       ->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8));

$invoice = new CorrectiveInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
// ... set other properties
```

### Reverse Invoice

```php
use Omisai\Szamlazzhu\Document\Invoice\ReverseInvoice;
use Omisai\Szamlazzhu\Header\ReverseInvoiceHeader;

$header = new ReverseInvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
$header->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8));

$invoice = new ReverseInvoice(Invoice::INVOICE_TYPE_E_INVOICE);
// ... set other properties
```

## Creating Receipts

### Basic Receipt

```php
use Omisai\Szamlazzhu\Document\Receipt\Receipt;
use Omisai\Szamlazzhu\Header\ReceiptHeader;
use Omisai\Szamlazzhu\Item\ReceiptItem;

// Create receipt header
$header = new ReceiptHeader();
$header->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_CASH)
       ->setCurrency(Currency::HUF)
       ->setExchangeBank('MNB');

// Create receipt item
$item = new ReceiptItem();
$item->setName('Product Name')
     ->setQuantity(1.0)
     ->setQuantityUnit('pcs')
     ->setNetUnitPrice(1000.0)
     ->setNetPrice(1000.0)
     ->setVat(ReceiptItem::VAT_27)
     ->setVatAmount(270.0)
     ->setGrossAmount(1270.0)
     ->setComment('Product description');

// Create and send receipt
$receipt = new Receipt();
$receipt->setHeader($header)
        ->setSeller($seller)
        ->setItems([$item]);
        // Note: Buyer is optional for receipts

$response = $agent->generateReceipt($receipt);

if ($response->isSuccess()) {
    echo "Receipt created successfully!";
    echo "Receipt number: " . $response->getReceiptNumber();
}
```

### Reverse Receipt (Receipt Cancellation)

```php
use Omisai\Szamlazzhu\Document\Receipt\ReverseReceipt;
use Omisai\Szamlazzhu\Header\ReverseReceiptHeader;

$header = new ReverseReceiptHeader();
$header->setReceiptNumber('NY-2024-001'); // Original receipt number to cancel

$reverseReceipt = new ReverseReceipt();
$reverseReceipt->setHeader($header);

$response = $agent->generateReceipt($reverseReceipt);
```

## Creating Proforma Invoices

```php
use Omisai\Szamlazzhu\Document\Proforma;
use Omisai\Szamlazzhu\Header\ProformaHeader;
use Omisai\Szamlazzhu\Item\ProformaItem;

// Create proforma header
$header = new ProformaHeader();
$header->setIssueDate(Carbon::now())
       ->setFulfillment(Carbon::now())
       ->setPaymentDue(Carbon::now()->addDays(8))
       ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
       ->setCurrency(Currency::HUF)
       ->setLanguage(Language::HU);

// Create proforma item
$item = new ProformaItem();
$item->setName('Product Name')
     ->setQuantity(1.0)
     ->setQuantityUnit('pcs')
     ->setNetUnitPrice(10000.0)
     ->setNetPrice(10000.0)
     ->setVat(ProformaItem::VAT_27)
     ->setVatAmount(2700.0)
     ->setGrossAmount(12700.0);

// Create and send proforma
$proforma = new Proforma();
$proforma->setHeader($header)
         ->setSeller($seller)
         ->setBuyer($buyer)
         ->setItems([$item]);

$response = $agent->generateProforma($proforma);

// Delete proforma (if needed)
$deleteResponse = $agent->deleteProforma($proforma);
```

## Working with Waybills

Waybills (fuvarlevél) are used for shipping integrations. Different courier services have different waybill types.

### MPL (Magyar Posta) Waybill

```php
use Omisai\Szamlazzhu\Waybill\MPLWaybill;

$waybill = new MPLWaybill('Destination Address', 'BARCODE123', 'Shipping comment');
$waybill->setBuyerCode('BUYER123')
        ->setWeight('2.5')
        ->setService('EXPRESS')
        ->setInsuredValue(50000.0);

// Add to invoice
$invoice->setWaybill($waybill);
```

### Sprinter Waybill

```php
use Omisai\Szamlazzhu\Waybill\SprinterWaybill;

$waybill = new SprinterWaybill('Destination Address', 'BARCODE123', 'Comment');
$waybill->setId('SPR')
        ->setSenderId('1234567890')
        ->setShipmentZip('106')
        ->setPacketNumber(2)
        ->setBarcodePostfix('ABC1234')
        ->setShippingTime('2024-12-31');

$invoice->setWaybill($waybill);
```

### Transoflex Waybill

```php
use Omisai\Szamlazzhu\Waybill\TransoflexWaybill;

$waybill = new TransoflexWaybill('Destination Address', 'BARCODE123', 'Comment');
$waybill->setId('12345')
        ->setCustomShippingId('CUSTOM123')
        ->setPacketNumber(1)
        ->setCountryCode('HU')
        ->setZip('1061')
        ->setService('STANDARD');

$invoice->setWaybill($waybill);
```

### Pick-Pack-Pont (PPP) Waybill

```php
use Omisai\Szamlazzhu\Waybill\PPPWaybill;

$waybill = new PPPWaybill('Destination Address', 'BARCODE123', 'Comment');
$waybill->setBarcodePrefix('PPP')
        ->setBarcodePostfix('1234567');

$invoice->setWaybill($waybill);
```

## Advanced Features

### Working with Multiple Currencies

```php
use Omisai\Szamlazzhu\Currency;

$header->setCurrency(Currency::EUR)
       ->setExchangeBank('MNB')
       ->setExchangeRate(380.5); // Manual exchange rate
```

### Adding Ledger Data for Accounting

```php
use Omisai\Szamlazzhu\BuyerLedger;
use Omisai\Szamlazzhu\Ledger\InvoiceItemLedger;

// Buyer ledger data
$buyerLedger = new BuyerLedger();
$buyerLedger->setBuyerId('CUSTOMER001')
           ->setBookingDate(Carbon::now())
           ->setBuyerLedgerNumber('VEV001');

$buyer->setLedgerData($buyerLedger);

// Item ledger data
$itemLedger = new InvoiceItemLedger();
$itemLedger->setEconomicEventType('SALES')
          ->setVatEconomicEventType('VAT_SALES')
          ->setRevenueLedgerNumber('REV001')
          ->setVatLedgerNumber('VAT001');

$item->setLedgerData($itemLedger);
```

### Adding Attachments to Invoices

```php
// Add file attachments (max 5 files)
$invoice->addAttachment([
    'name' => 'contract.pdf',
    'content' => file_get_contents('/path/to/contract.pdf')
]);

$invoice->addAttachment([
    'name' => 'specification.docx',
    'content' => file_get_contents('/path/to/spec.docx')
]);
```

### Retrieving Invoice Data

```php
// Get invoice by invoice number
$response = $agent->getInvoiceData('INV-2024-001', Invoice::FROM_INVOICE_NUMBER, true);

// Get invoice by order number
$response = $agent->getInvoiceData('ORDER-123', Invoice::FROM_ORDER_NUMBER, true);

if ($response->isSuccess()) {
    $invoiceXml = $response->getXmlData();
    $pdfContent = $response->getPdfData();
}
```

### Paying an Invoice

```php
$invoice = new Invoice();
$invoice->getHeader()->setInvoiceNumber('INV-2024-001');

$response = $agent->payInvoice($invoice);

if ($response->isSuccess()) {
    echo "Invoice marked as paid successfully!";
}
```

### Working with Different VAT Rates

```php
use Omisai\Szamlazzhu\Item\InvoiceItem;

// Standard VAT rates
$item->setVat(InvoiceItem::VAT_27);    // 27%
$item->setVat(InvoiceItem::VAT_18);    // 18%
$item->setVat(InvoiceItem::VAT_5);     // 5%
$item->setVat(InvoiceItem::VAT_0);     // 0%

// Special VAT cases
$item->setVat(InvoiceItem::VAT_AM);    // Tax-free
$item->setVat(InvoiceItem::VAT_EU);    // EU VAT
$item->setVat(InvoiceItem::VAT_EUFAD37); // EU VAT directive 37
$item->setVat(InvoiceItem::VAT_EUKT);  // EU VAT exempt
$item->setVat(InvoiceItem::VAT_MAA);   // Outside scope of VAT
```

## Error Handling

### Handling API Responses

```php
try {
    $response = $agent->generateInvoice($invoice);

    if ($response->isSuccess()) {
        // Success
        $invoiceNumber = $response->getInvoiceNumber();
        $invoiceId = $response->getInvoiceId();
        $pdfData = $response->getPdfData(); // if PDF download enabled
    } else {
        // Handle API errors
        $errorCode = $response->getErrorCode();
        $errorMessage = $response->getErrorMessage();

        Log::error('Szamlazz.hu API Error', [
            'code' => $errorCode,
            'message' => $errorMessage
        ]);
    }
} catch (\Omisai\Szamlazzhu\SzamlaAgentException $e) {
    // Handle package-specific errors
    Log::error('SzamlaAgent Error: ' . $e->getMessage());
} catch (\Exception $e) {
    // Handle general errors
    Log::error('General Error: ' . $e->getMessage());
}
```

### Common Error Scenarios

```php
// Validation errors
try {
    $invoice->buildXmlData($request);
} catch (SzamlaAgentException $e) {
    if (strpos($e->getMessage(), 'FIELDS_CHECK_ERROR') !== false) {
        // Handle validation errors
        echo "Please check required fields";
    }
}

// Network/API errors
if (!$response->isSuccess()) {
    switch ($response->getErrorCode()) {
        case 'INVALID_API_KEY':
            echo "Invalid API key provided";
            break;
        case 'INSUFFICIENT_BALANCE':
            echo "Insufficient account balance";
            break;
        default:
            echo "API Error: " . $response->getErrorMessage();
    }
}
```

## Configuration Options

### File Storage Configuration

```php
// In config/szamlazzhu.php

return [
    'xml' => [
        'file_save' => true,               // Save XML files
        'request_file_save' => true,       // Save request XML files
        'response_file_save' => true,      // Save response XML files
    ],
    'pdf' => [
        'file_save' => true,               // Save PDF files
    ],
];
```

### Custom Storage Disk

The package automatically creates a `szamlazzhu` disk for file storage:

```php
// Files are stored in storage/app/szamlazzhu/
// - PDF files: storage/app/szamlazzhu/pdf/
// - XML files: storage/app/szamlazzhu/xmls/request/ and xmls/response/
```

### Logging Configuration

```php
// Configure logging in config/szamlazzhu.php
return [
    'log_email' => 'admin@example.com', // Email for error notifications
];

// Or in .env
SZAMLAZZHU_LOG_EMAIL=admin@example.com
SZAMLAZZHU_LOG_LEVEL=warning
SZAMLAZZHU_LOG_FILENAME=szamlazzhu
```

### Testing Configuration

```php
// Use test prefix for development
SZAMLAZZHU_TEST_PREFIX=TEST

// This will prefix all document numbers with "TEST"
```

### Using Different Invoice Templates

```php
use Omisai\Szamlazzhu\Document\Invoice\Invoice;

// Available templates
$header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_DEFAULT);      // SzlaMost (recommended)
$header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_TRADITIONAL);  // SzlaNoEnv
$header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_ENV_FRIENDLY);  // SzlaAlap
$header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_8CM);           // Szla8cm (thermal)
$header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_RETRO);         // SzlaTomb (retro)
```

## Best Practices

1. **Always validate data before sending**: Use try-catch blocks around API calls
2. **Store invoice numbers**: Keep track of generated invoice numbers for future reference
3. **Handle PDF storage**: Configure PDF file saving for invoice archiving
4. **Use appropriate VAT rates**: Ensure correct VAT rates for your products/services
5. **Test with sandbox**: Use test prefixes during development
6. **Monitor API responses**: Log errors and successful operations for debugging
7. **Backup configurations**: Store your API keys and configurations securely

## Complete Example

Here's a complete example that creates an invoice with all common features:

```php
<?php

use Omisai\Szamlazzhu\SzamlaAgent;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\Header\InvoiceHeader;
use Omisai\Szamlazzhu\Item\InvoiceItem;
use Omisai\Szamlazzhu\Seller;
use Omisai\Szamlazzhu\Buyer;
use Omisai\Szamlazzhu\TaxPayer;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\Language;
use Omisai\Szamlazzhu\Waybill\MPLWaybill;
use Carbon\Carbon;

try {
    // Initialize agent
    $agent = SzamlaAgent::createWithAPIkey(config('szamlazzhu.api_key'), true);

    // Set up seller
    $seller = new Seller();
    $seller->setBank('Example Bank, BIC: EXAMPLEXX')
           ->setBankAccount('12345678-12345678-12345678');

    // Set up buyer
    $buyer = new Buyer();
    $buyer->setName('Example Customer Ltd.')
          ->setZipCode('1061')
          ->setCity('Budapest')
          ->setAddress('Example Street 123.')
          ->setEmail('customer@example.com')
          ->setSendEmailState(true)
          ->setTaxNumber('12345678-1-23')
          ->setTaxPayer(TaxPayer::TAXPAYER_HAS_TAXNUMBER);

    // Set up header
    $header = new InvoiceHeader(Invoice::INVOICE_TYPE_E_INVOICE);
    $header->setIssueDate(Carbon::now())
           ->setFulfillment(Carbon::now())
           ->setPaymentDue(Carbon::now()->addDays(8))
           ->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER)
           ->setCurrency(Currency::HUF)
           ->setLanguage(Language::HU)
           ->setPrefix('INV')
           ->setOrderNumber('ORDER-2024-001')
           ->setComment('Thank you for your business!');

    // Set up items
    $item1 = new InvoiceItem();
    $item1->setName('Professional Service')
          ->setQuantity(10.0)
          ->setQuantityUnit('hours')
          ->setNetUnitPrice(5000.0)
          ->setNetPrice(50000.0)
          ->setVat(InvoiceItem::VAT_27)
          ->setVatAmount(13500.0)
          ->setGrossAmount(63500.0)
          ->setComment('Consulting services');

    $item2 = new InvoiceItem();
    $item2->setName('Software License')
          ->setQuantity(1.0)
          ->setQuantityUnit('pcs')
          ->setNetUnitPrice(20000.0)
          ->setNetPrice(20000.0)
          ->setVat(InvoiceItem::VAT_27)
          ->setVatAmount(5400.0)
          ->setGrossAmount(25400.0)
          ->setComment('Annual license');

    // Set up waybill (optional)
    $waybill = new MPLWaybill('Customer Address', 'BARCODE123', 'Handle with care');
    $waybill->setBuyerCode('CUST001')
            ->setWeight('0.5')
            ->setService('STANDARD');

    // Create invoice
    $invoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);
    $invoice->setHeader($header)
            ->setSeller($seller)
            ->setBuyer($buyer)
            ->setItems([$item1, $item2])
            ->setWaybill($waybill);

    // Send to Szamlazz.hu
    $response = $agent->generateInvoice($invoice);

    if ($response->isSuccess()) {
        echo "Invoice created successfully!\n";
        echo "Invoice Number: " . $response->getInvoiceNumber() . "\n";
        echo "Invoice ID: " . $response->getInvoiceId() . "\n";

        // Save PDF if needed
        if ($response->hasPdfData()) {
            file_put_contents("invoice_{$response->getInvoiceNumber()}.pdf", $response->getPdfData());
            echo "PDF saved successfully!\n";
        }
    } else {
        echo "Error creating invoice: " . $response->getErrorMessage() . "\n";
        echo "Error code: " . $response->getErrorCode() . "\n";
    }

} catch (\Omisai\Szamlazzhu\SzamlaAgentException $e) {
    echo "SzamlaAgent Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
```

This guide covers the most common use cases for the Laravel Szamlazz.hu package. For more advanced features or specific requirements, refer to the source code and test files in the package.
