[![Latest Stable Version](https://img.shields.io/packagist/v/omisai/laravel-szamlazzhu?style=for-the-badge)](https://packagist.org/packages/omisai/laravel-szamlazzhu)
[![License](https://img.shields.io/packagist/l/omisai/laravel-szamlazzhu?style=for-the-badge)](https://packagist.org/packages/omisai/laravel-szamlazzhu)
[![PHP Version Require](https://img.shields.io/badge/PHP-%3E%3D8.2-blue?style=for-the-badge&logo=php)](https://packagist.org/packages/omisai/laravel-szamlazzhu)
![Számlázz.hu API](https://img.shields.io/badge/Számlázz.hu%20API-2.10.23-yellow?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-9%2C10%2C11%2C12-red?style=for-the-badge&logo=laravel)

## About

**laravel-szamlazzhu** is a Laravel package that provides an easy-to-use interface for communicating with the Számlázz.hu API. It was created by refactoring the original source code (available at [here](https://docs.szamlazz.hu/php)) and integrating it into the [Laravel framework](https://laravel.com/).

Many of the original source code files were reforged to use the built-in features of Laravel, such as HTTP client, Filesystem abstraction, Configuration and service provider. As a result, **laravel-szamlazzhu** provides a more streamlined and idiomatic way of interacting with the Számlázz.hu API.

## Installation

To get started with package, simply install it via Composer:

``` bash
composer require omisai/laravel-szamlazzhu
```


## Configuration

Configure your API credentials in .env:

``` env
SZAMLAZZHU_API_KEY=<yourAPIToken>
```

or in config/szamlazzhu.php file:

``` php
'api_key' => env('SZAMLAZZHU_API_KEY', null),
```

## Usage

WIP

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing

``` bash
composer test
```


## Security

If you discover any security-related issues, please email [security@omisai.com](mailto:security@omisai.com) instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.