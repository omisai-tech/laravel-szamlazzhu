<?php

namespace Omisai\Szamlazzhu\Item;

use Omisai\Szamlazzhu\FieldsValidationTrait;

class Item
{
    use FieldsValidationTrait;

    /**
     * HU: Áfakulcs: tárgyi adómentes
     */
    public const VAT_TAM = 'TAM';

    /**
     * HU: Áfakulcs: alanyi adómentes
     */
    public const VAT_AAM = 'AAM';

    /**
     * HU: Áfakulcs: EU-n belül
     */
    public const VAT_EU = 'EU';

    /**
     * HU: Áfakulcs: EU-n kívül
     */
    public const VAT_EUK = 'EUK';

    /**
     * HU: Áfakulcs: mentes az adó alól
     */
    public const VAT_MAA = 'MAA';

    /**
     * HU: Áfakulcs: fordított áfa
     */
    public const VAT_F_AFA = 'F.AFA';

    /**
     * HU: Áfakulcs: különbözeti áfa
     */
    public const VAT_K_AFA = 'K.AFA';

    /**
     * HU: Áfakulcs: áfakörön kívüli
     */
    public const VAT_AKK = 'ÁKK';

    /**
     * HU: Áfakulcs: áfakörön kívüli
     */
    public const VAT_TAHK = 'TAHK';

    /**
     * HU: Áfakulcs: EU-n belüli termék értékesítés
     */
    public const VAT_EUT = 'EUT';

    /**
     * HU: Áfakulcs: EU-n kívüli termék értékesítés
     */
    public const VAT_EUKT = 'EUKT';

    /**
     * HU: Áfakulcs: EU-n belüli
     */
    public const VAT_KBAET = 'KBAET';

    /**
     * HU: Áfakulcs: EU-n belüli
     */
    public const VAT_KBAUK = 'KBAUK';

    /**
     * HU: Áfakulcs: EU-n kívüli
     */
    public const VAT_EAM = 'EAM';

    /**
     * HU: Áfakulcs: Mentes az adó alól
     */
    public const VAT_NAM = 'KBAUK';

    /**
     * HU: Áfakulcs: áfa tárgyi hatályán kívül
     */
    public const VAT_ATK = 'ATK';

    /**
     * HU: Áfakulcs: EU-n belüli
     */
    public const VAT_EUFAD37 = 'EUFAD37';

    /**
     * HU: Áfakulcs: EU-n belüli
     */
    public const VAT_EUFADE = 'EUFADE';

    /**
     * HU: Áfakulcs: EU-n belüli
     */
    public const VAT_EUE = 'EUE';

    /**
     * HU: Áfakulcs: EU-n kívüli
     */
    public const VAT_HO = 'HO';

    /**
     * HU: Alapértelmezett ÁFA érték
     */
    public const DEFAULT_VAT = '27';

    /**
     * HU: Alapértelmezett mennyiség
     */
    public const DEFAULT_QUANTITY = 1.0;

    /**
     * HU: Alapértelmezett mennyiségi egység
     */
    public const DEFAULT_QUANTITY_UNIT = 'db';

    protected string $id;

    protected string $name;

    protected float $quantity = self::DEFAULT_QUANTITY;

    protected string $quantityUnit = self::DEFAULT_QUANTITY_UNIT;

    protected float $netUnitPrice;

    /**
     * HU:
     *
     * Ugyanaz adható meg, mint a számlakészítés oldalon:
     * https://www.szamlazz.hu/szamla/szamlaszerkeszto
     *
     * Példa konkrét ÁFA értékre:
     * 0,5,7,18,19,20,25,27
     *
     * @var string
     */
    protected string $vat = self::DEFAULT_VAT;

    protected float $priceGapVatBase;

    protected float $netPrice;

    protected float $vatAmount;

    protected float $grossAmount;

    protected string $comment;

    protected ?int $dataDeletionCode = null;

    protected array $requiredFields = ['name', 'quantity', 'quantityUnit', 'netUnitPrice', 'vat', 'netPrice', 'vatAmount', 'grossAmount'];

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = (float) $quantity;

        return $this;
    }

    public function setQuantityUnit(string $quantityUnit): self
    {
        $this->quantityUnit = $quantityUnit;

        return $this;
    }

    public function setNetUnitPrice(float $netUnitPrice): self
    {
        $this->netUnitPrice = (float) $netUnitPrice;

        return $this;
    }

    public function setVat(string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function setPriceGapVatBase(float $priceGapVatBase): self
    {
        $this->priceGapVatBase = (float) $priceGapVatBase;

        return $this;
    }

    public function setNetPrice(float $netPrice): self
    {
        $this->netPrice = (float) $netPrice;

        return $this;
    }

    public function setVatAmount(float $vatAmount): self
    {
        $this->vatAmount = (float) $vatAmount;

        return $this;
    }

    public function setGrossAmount(float $grossAmount): self
    {
        $this->grossAmount = (float) $grossAmount;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setDataDeletionCode(int $dataDeletionCode)
    {
        $this->dataDeletionCode = $dataDeletionCode;
    }
}
