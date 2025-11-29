<?php

use Omisai\Szamlazzhu\CookieHandler;

describe('CookieHandler', function () {
    it('can be instantiated with default mode', function () {
        $handler = new CookieHandler();
        expect($handler)->toBeInstanceOf(CookieHandler::class);
    });

    it('can be instantiated with text mode', function () {
        $handler = new CookieHandler(CookieHandler::COOKIE_HANDLE_MODE_TEXT);
        expect($handler)->toBeInstanceOf(CookieHandler::class);
    });

    it('can be instantiated with database mode', function () {
        $handler = new CookieHandler(CookieHandler::COOKIE_HANDLE_MODE_DATABASE);
        expect($handler)->toBeInstanceOf(CookieHandler::class);
    });

    it('returns correct handle mode text check', function () {
        $handler = new CookieHandler(CookieHandler::COOKIE_HANDLE_MODE_TEXT);
        expect($handler->isHandleModeText())->toBeTrue();
        expect($handler->isHandleModeDatabase())->toBeFalse();
    });

    it('returns correct handle mode database check', function () {
        $handler = new CookieHandler(CookieHandler::COOKIE_HANDLE_MODE_DATABASE);
        expect($handler->isHandleModeText())->toBeFalse();
        expect($handler->isHandleModeDatabase())->toBeTrue();
    });

    it('can get cookie handle mode', function () {
        $handler = new CookieHandler(CookieHandler::COOKIE_HANDLE_MODE_TEXT);
        expect($handler->getCookieHandleMode())->toBe(CookieHandler::COOKIE_HANDLE_MODE_TEXT);
    });

    it('can set cookie handle mode', function () {
        $handler = new CookieHandler();
        $result = $handler->setCookieHandleMode(CookieHandler::COOKIE_HANDLE_MODE_DATABASE);

        expect($result)->toBeInstanceOf(CookieHandler::class);
        expect($handler->getCookieHandleMode())->toBe(CookieHandler::COOKIE_HANDLE_MODE_DATABASE);
    });

    it('has correct cookie file path constant', function () {
        expect(CookieHandler::COOKIE_FILE_PATH)->toBe('cookies/cookie.txt');
    });

    it('has correct cookie domain constant', function () {
        expect(CookieHandler::COOKIE_DOMAIN)->toBe('www.szamlazz.hu');
    });

    it('returns cookies array with domain', function () {
        $handler = new CookieHandler();
        $cookies = $handler->getCookies();

        expect($cookies)->toBeArray();
        expect($cookies)->toHaveCount(2);
        expect($cookies[1])->toBe(CookieHandler::COOKIE_DOMAIN);
    });
});
