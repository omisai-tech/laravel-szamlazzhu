# Changelog

All notable changes to `laravel-szamlazzhu` will be documented in this file.


## [1.8.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.3.0...1.4.0) - 2025-11-29

* Supporting [2.10.23] Szamlazzhu API
* Add singleton pattern support to SzamlaAgent and SzamlaAgentAPI classes
* Update VAT handling in InvoiceItem and ReceiptItem classes using SzamlaAgentUtil
* Remove deprecated VAT_TEHK constant from Item class
* Update PHP version requirement to 8.2
* Add Laravel 12 version support
* Update getXmlFileName method to include SzamlaAgent parameter and update filename generation


## [1.3.0](https://github.com/omisai-tech/laravel-szamlazzhu/compare/1.2.0...1.3.0) - 2024-05-01

* Supporting [2.10.18] Szamlazzhu API
* Add comment option in buildXmlData in ReceiptItem.