<?php

namespace Omisai\Szamlazzhu\Waybill;

use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

/**
 * HU: Pick Pack Pont fuvarlevél
 */
class PPPWaybill extends Waybill
{
    /**
     * HU: PPP-vel egyeztetett 3 karakteres rövidítés
     */
    protected string $barcodePrefix;

    /**
     * HU: Számlánként egyedi vonalkód, maximum 7 karakteres azonosító
     */
    protected string $barcodePostfix;

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, self::WAYBILL_TYPE_PPP, $barcode, $comment);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $data = parent::buildXmlData($request);
        $data['ppp'] = [];
        if (!empty($this->barcodePrefix)) {
            $data['ppp']['vonalkodPrefix'] = $this->barcodePrefix;
        }
        if (!empty($this->barcodePostfix)) {
            $data['ppp']['vonalkodPostfix'] = $this->barcodePostfix;
        }

        return $data;
    }

    public function setBarcodePrefix(string $barcodePrefix): self
    {
        $this->barcodePrefix = $barcodePrefix;

        return $this;
    }

    public function setBarcodePostfix(string $barcodePostfix): self
    {
        $this->barcodePostfix = $barcodePostfix;

        return $this;
    }
}
