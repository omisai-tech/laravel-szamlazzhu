<?php

namespace Omisai\Szamlazzhu\Waybill;

use Omisai\Szamlazzhu\HasXmlBuildWithRequestInterface;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

/**
 * HU: Fuvarlevél
 */
class Waybill implements HasXmlBuildWithRequestInterface
{
    // Transoflex
    public const WAYBILL_TYPE_TRANSOFLEX = 'Transoflex';

    // Sprinter
    public const WAYBILL_TYPE_SPRINTER = 'Sprinter';

    // Pick-Pack-Pont
    public const WAYBILL_TYPE_PPP = 'PPP';

    // Magyar Posta
    public const WAYBILL_TYPE_MPL = 'MPL';

    protected string $destination;

    /**
     * @example TOF, PPP, SPRINTER, MPL, FOXPOST, GLS, EMPTY
     */
    protected string $parcel;

    /**
     * If no specified delivery data, then the barcode will be used
     */
    protected string $barcode;

    protected string $comment;

    protected function __construct(string $destination = '', string $parcel = '', string $barcode = '', string $comment = '')
    {
        $this->setDestination($destination);
        $this->setParcel($parcel);
        $this->setBarcode($barcode);
        $this->setComment($comment);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $data = [];
        if (!empty($this->destination)) {
            $data['uticel'] = $this->destination;
        }
        if (!empty($this->parcel)) {
            $data['futarSzolgalat'] = $this->parcel;
        }
        if (!empty($this->barcode)) {
            $data['vonalkod'] = $this->barcode;
        }
        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }

        return $data;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function setParcel(string $parcel): self
    {
        $this->parcel = $parcel;

        return $this;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
