<?php

namespace Omisai\Szamlazzhu\Waybill;

use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

/**
 * HU: Sprinter fuvarlevél
 */
class SprinterWaybill extends Waybill
{
    /**
     * HU: A Sprinterrel egyeztetett 3 karakteres rövidítés
     */
    protected string $id;

    /**
     * HU: Sprintertől kapott feladókód, 10 jegyű szám
     */
    protected string $senderId;

    /**
     * HU: Sprinteres iránykód, az a sprinter saját "irányítószáma", pl. "106"
     */
    protected string $shipmentZip;

    /**
     * HU: Csomagok száma, ennyi fuvarlevél lesz a számlához összesen
     */
    protected int $numberOfPackages;

    /**
     * HU: Számlánként egyedi vonalkód, 7-13 karakteres azonosító
     */
    protected string $barcodePostfix;

    protected string $shippingTime;

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, self::WAYBILL_TYPE_SPRINTER, $barcode, $comment);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $data = parent::buildXmlData($request);
        $data['sprinter'] = [];
        if (!empty($this->id)) {
            $data['sprinter']['azonosito'] = $this->id;
        }
        if (!empty($this->senderId)) {
            $data['sprinter']['feladokod'] = $this->senderId;
        }
        if (!empty($this->shipmentZip)) {
            $data['sprinter']['iranykod'] = $this->shipmentZip;
        }
        if (!empty($this->numberOfPackages)) {
            $data['sprinter']['csomagszam'] = $this->numberOfPackages;
        }
        if (!empty($this->barcodePostfix)) {
            $data['sprinter']['vonalkodPostfix'] = $this->barcodePostfix;
        }
        if (!empty($this->shippingTime)) {
            $data['sprinter']['szallitasiIdo'] = $this->shippingTime;
        }

        return $data;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setSenderId(string $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function setShipmentZip(string $shipmentZip): self
    {
        $this->shipmentZip = $shipmentZip;

        return $this;
    }

    public function setPacketNumber(int $numberOfPackages): self
    {
        $this->numberOfPackages = $numberOfPackages;

        return $this;
    }

    public function setBarcodePostfix(string $barcodePostfix): self
    {
        $this->barcodePostfix = $barcodePostfix;

        return $this;
    }

    public function setShippingTime(string $shippingTime): self
    {
        $this->shippingTime = $shippingTime;

        return $this;
    }
}
