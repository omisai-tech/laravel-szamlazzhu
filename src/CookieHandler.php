<?php

namespace Omisai\Szamlazzhu;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class CookieHandler
{
    public const COOKIE_FILE_PATH = 'cookies/cookie.txt';

    public const COOKIE_HANDLE_MODE_TEXT = 0;

    public const COOKIE_HANDLE_MODE_DATABASE = 1;

    public const COOKIE_DOMAIN = 'www.szamlazz.hu';

    private int $cookieHandleMode;

    public function __construct(int $cookieHandleMode = self::COOKIE_HANDLE_MODE_TEXT)
    {
        $this->cookieHandleMode = $cookieHandleMode;
    }

    public function isHandleModeText(): bool
    {
        return $this->cookieHandleMode === self::COOKIE_HANDLE_MODE_TEXT;
    }

    public function isHandleModeDatabase(): bool
    {
        return $this->cookieHandleMode === self::COOKIE_HANDLE_MODE_DATABASE;
    }

    public function getCookieHandleMode(): int
    {
        return $this->cookieHandleMode;
    }

    public function setCookieHandleMode(int $cookieHandleMode): self
    {
        $this->cookieHandleMode = $cookieHandleMode;

        return $this;
    }

    public function getCookies(): array
    {
        return [['JSESSIONID' => $this->getCookieFile()], self::COOKIE_DOMAIN];
    }

    public function getCookieFile(): string
    {
        if ($this->isHandleModeDatabase()) {
            throw new SzamlaAgentException('The Cookie handle mode is "database", please override the CookeHandler::getCookieFile() method with your custom handler.');
        }

        if (!Storage::disk(config('szamlazzhu.cookie.disk'))->exists(self::COOKIE_FILE_PATH)) {
            Log::channel('szamlazzhu')->debug('Cookie file does not exists, creating an empty one.');
            Storage::disk(config('szamlazzhu.cookie.disk'))->put(self::COOKIE_FILE_PATH, '');
        }

        return Storage::disk(config('szamlazzhu.cookie.disk'))->get(self::COOKIE_FILE_PATH);
    }

    public function setCookieFile(Response $response)
    {
        if ($this->isHandleModeDatabase()) {
            throw new SzamlaAgentException('The Cookie handle mode is "database", please override the CookeHandler::setCookieFile() method with your custom handler.');
        }

        $receivedCookies = $response->cookies();
        preg_match_all('/(?<=JSESSIONID=)(.*?)(?=;)/', $receivedCookies->getCookieByName('JSESSIONID'), $receivedCookie);
        $receivedCookie = $receivedCookie[0][0];

        Log::channel('szamlazzhu')->debug('Cookie set JSESSIONID.', [
            'receivedCookie' => $receivedCookie,
        ]);

        if (Storage::disk(config('szamlazzhu.cookie.disk'))->exists(self::COOKIE_FILE_PATH)) {
            $storedCookie = Storage::disk(config('szamlazzhu.cookie.disk'))->get(self::COOKIE_FILE_PATH);
            if ($storedCookie !== $receivedCookie) {
                Storage::disk(config('szamlazzhu.cookie.disk'))->put(self::COOKIE_FILE_PATH, $receivedCookie);
            }
        } else {
            Storage::disk(config('szamlazzhu.cookie.disk'))->put(self::COOKIE_FILE_PATH, $receivedCookie);
        }
    }
}
