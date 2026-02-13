<?php

namespace Omisai\Szamlazzhu;

/**
 * HU: Adózó (adóalany)
 */
class TaxPayer
{
    use FieldsValidationTrait;

    /**
     * Non-EU enterprise
     */
    public const TAXPAYER_NON_EU_ENTERPRISE = 7;

    /**
     * EU enterprise
     */
    public const TAXPAYER_EU_ENTERPRISE = 6;

    /**
     * has a Hungarian tax number
     */
    public const TAXPAYER_HAS_TAXNUMBER = 1;

    /**
     * we don't know
     */
    public const TAXPAYER_WE_DONT_KNOW = 0;

    /**
     * no tax number
     */
    public const TAXPAYER_NO_TAXNUMBER = -1;

    protected string $taxPayerId;

    protected int $taxPayerType = self::TAXPAYER_WE_DONT_KNOW;

    protected array $requiredFields = ['taxPayerId'];

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $this->validateFields();

        $data = [];
        $data['beallitasok'] = $request->getAgent()->getSetting()->buildXmlData($request);
        $data['torzsszam'] = $this->taxPayerId;

        return $data;
    }

    protected function setRequiredFields(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }

    public function getDefault(): int
    {
        return self::TAXPAYER_WE_DONT_KNOW;
    }

    public function setTaxPayerId(string $taxPayerId)
    {
        $this->taxPayerId = substr($taxPayerId, 0, 8);
    }

    public function getTaxPayerType(): int
    {
        return $this->taxPayerType;
    }

    /**
     * Taxpayer type
     *
     * This information is stored as data by the partner in the system and can be modified there.
     *
     * This field can take the following values:
     *  7: TaxPayer::TAXPAYER_NON_EU_ENTERPRISE - Non-EU enterprise
     *  6: TaxPayer::TAXPAYER_EU_ENTERPRISE     - EU enterprise
     *  1: TaxPayer::TAXPAYER_HAS_TAXNUMBER     - has a Hungarian tax number
     *  0: TaxPayer::TAXPAYER_WE_DONT_KNOW      - we don't know
     * -1: TaxPayer::TAXPAYER_NO_TAXNUMBER      - no tax number
     *
     * @see https://tudastar.szamlazz.hu/gyik/vevo-adoszama-szamlan
     *
     * @param  int  $taxPayerType
     */
    public function setTaxPayerType($taxPayerType)
    {
        $this->taxPayerType = $taxPayerType;
    }
}
