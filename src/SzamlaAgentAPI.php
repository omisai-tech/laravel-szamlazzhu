<?php

namespace Omisai\Szamlazzhu;

use Omisai\Szamlazzhu\Response\AbstractResponse as SzamlaAgentResponse;

/**
 * A Számla Agent inicializálását, az adatok küldését és fogadását kezelő osztály
 */
class SzamlaAgentAPI extends SzamlaAgent
{
    /**
     * Számla Agent API létrehozása
     *
     * @param  string  $apiKey       Számla Agent kulcs
     * @param  bool  $downloadPdf  szeretnénk-e letölteni a bizonylatot PDF formátumban
     * @param  int  $logLevel     naplózási szint
     * @param  int  $responseType válasz típusa (szöveges vagy XML)
     * @param  string  $aggregator   webáruházat futtató motor neve
     * @return SzamlaAgent
     *
     * @throws SzamlaAgentException
     */
    public static function create(string $apiKey, bool $downloadPdf = true, int $responseType = SzamlaAgentResponse::RESULT_AS_TEXT, string $aggregator = '')
    {
        $index = self::getHash($apiKey);

        $agent = null;
        if (isset(self::$agents[$index])) {
            $agent = self::$agents[$index];
        }

        if ($agent === null) {
            return self::$agents[$index] = new self(null, null, $apiKey, $downloadPdf, $responseType, $aggregator);
        } else {
            return $agent;
        }
    }
}
