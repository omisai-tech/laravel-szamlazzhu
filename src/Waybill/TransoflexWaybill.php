<?php

namespace Omisai\Szamlazzhu\Waybill;

use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

/**
 * HU: Transoflex fuvarlevél
 */
class TransoflexWaybill extends Waybill
{
    /**
     * HU: A Transoflextól kapott 5 jegyű szám
     */
    protected string $id;

    protected string $customShippingId;

    protected int $numberOfPackages;

    protected string $countryCode;

    protected string $zip;

    protected string $service;

    public function __construct(string $destination = '', string $barcode = '', string $comment = '')
    {
        parent::__construct($destination, self::WAYBILL_TYPE_TRANSOFLEX, $barcode, $comment);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $data = parent::buildXmlData($request);
        $data['tof'] = [];
        if (!empty($this->id)) {
            $data['tof']['azonosito'] = $this->id;
        }
        if (!empty($this->customShippingId)) {
            $data['tof']['shippingID'] = $this->customShippingId;
        }
        if (!empty($this->numberOfPackages)) {
            $data['tof']['csomagszam'] = $this->numberOfPackages;
        }
        if (!empty($this->countryCode)) {
            $data['tof']['countryCode'] = $this->countryCode;
        }
        if (!empty($this->zip)) {
            $data['tof']['zip'] = $this->zip;
        }
        if (!empty($this->service)) {
            $data['tof']['service'] = $this->service;
        }

        return $data;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setCustomShippingId(string $customShippingId): self
    {
        $this->customShippingId = $customShippingId;

        return $this;
    }

    public function setPacketNumber(int $numberOfPackages): self
    {
        $this->numberOfPackages = $numberOfPackages;

        return $this;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }
}
