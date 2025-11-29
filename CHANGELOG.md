# Changelog

All notable changes to `laravel-szamlazzhu` will be documented in this file.

## [1.8.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.3.0...1.4.0) - 2025-11-29

* Supporting [2.10.23] Szamlazzhu API
* Add singleton pattern support to SzamlaAgent and SzamlaAgentAPI classes
* Update VAT handling in InvoiceItem and ReceiptItem classes using SzamlaAgentUtil
* Remove deprecated VAT_TEHK constant from Item class
* Update PHP version requirement to 8.2
* Drop Laravel 9 version support
* Add Laravel 12 version support
* Update getXmlFileName method to include SzamlaAgent parameter and update filename generation

## [1.7.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.6.1...1.7.0) - 2025-08-02

### Added
* add configuration options for invoice and receipt prefixes
* add PDF file storage configuration options
* (dev) add helper functions for creating invoice, proforma, prepayment, and receipt headers in tests
* (dev) add test for creating a prepayment invoice with detailed itemization
* (dev) add PdfDownloadTest for testing proforma invoice's pdf download

### Fixed
* fix SzamlaAgent sendRequest method to handle deleteProforma type for Proforma entities
* fix SzamlaAgent entity type checks in sendRequest method to use instanceof instead of string comparison
* allow nullable SzamlaAgentRequest in buildXmlData method for invoice and proforma headers
* (dev) ensure max-parallel is set to 1 in test job strategy to handle Szamlazz.hu API limitations

### Changed
* update storage disk references to use configuration settings for cookie, invoice, and XML file handling
* change SzamlaAgentRequest's type visibility to public
* SzamlaAgent::createWithAPIkey does not require 2nd parameter anymore ($downloadPdf), the fallback is "szamlazzhu.pdf.file_save" config or false

## [1.4.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.3.0...1.4.0) - 2025-02-06

* add dataDeletionCode for Items - [2.10.19] Szamlazzhu API support
* fulfillment is not required for ReverseInvoiceHeader anymore - [2.10.19] Szamlazzhu API support
* improve preview pdf file name generation in AbstractResponse - [2.10.20] Szamlazzhu API support
* remove API support (API_SUPPORT) and minimum PHP version (MINIMUM_PHP_VERSION) constants from SzamlaAgent

## [1.3.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.2.0...1.3.0) - 2024-05-01

* supporting [2.10.18] Szamlazzhu API
* add comment option in buildXmlData in ReceiptItem.