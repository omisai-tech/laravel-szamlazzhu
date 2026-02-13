<?php

namespace Omisai\Szamlazzhu;

class Buyer
{
    use FieldsValidationTrait;

    protected string $id;

    protected string $name;

    protected string $country = 'Hungary';

    protected string $zipCode;

    protected string $city;

    protected string $address;

    /**
     * If email address is given, the document will be sent to this email address by Számlázz.hu
     * In case of a test account, the system will not send an email for security reasons
     */
    protected string $email;

    protected bool $sendEmail = true;

    protected int $taxPayer; // TODO: Use the TaxPayer object instead

    protected string $taxNumber;

    protected string $groupIdentifier;

    protected string $taxNumberEU;

    /**
     * Postal data is optional
     */
    protected string $postalName;

    /**
     * Postal data is optional
     */
    protected string $postalCountry;

    /**
     * Postal data is optional
     */
    protected string $postalZip;

    /**
     * Postal data is optional
     */
    protected string $postalCity;

    /**
     * Postal data is optional
     */
    protected string $postalAddress;

    /**
     * HU: Vevő főkönyvi adatai
     */
    protected BuyerLedger $ledgerData;

    /**
     * If enabled on the settings page (https://www.szamlazz.hu/szamla/beallitasok)
     * this name will appear below the signature line.
     */
    protected string $signatoryName;

    protected string $phone;

    protected string $comment;

    protected array $requiredFields = ['name', 'zipCode', 'city', 'address', 'country'];

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $this->validateFields();

        $data = [];
        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_INVOICE:
                $data = [
                    'nev' => $this->name,
                    'orszag' => $this->country,
                    'irsz' => $this->zipCode,
                    'telepules' => $this->city,
                    'cim' => $this->address,
                ];

                if (!empty($this->email)) {
                    $data['email'] = $this->email;
                }

                $data['sendEmail'] = $this->shouldSendEmail() ? true : false;

                if (!empty($this->taxPayer)) {
                    $data['adoalany'] = $this->taxPayer;
                }
                if (!empty($this->taxNumber)) {
                    $data['adoszam'] = $this->taxNumber;
                }
                if (!empty($this->groupIdentifier)) {
                    $data['csoportazonosito'] = $this->groupIdentifier;
                }
                if (!empty($this->taxNumberEU)) {
                    $data['adoszamEU'] = $this->taxNumberEU;
                }
                if (!empty($this->postalName)) {
                    $data['postazasiNev'] = $this->postalName;
                }
                if (!empty($this->postalCountry)) {
                    $data['postazasiOrszag'] = $this->postalCountry;
                }
                if (!empty($this->postalZip)) {
                    $data['postazasiIrsz'] = $this->postalZip;
                }
                if (!empty($this->postalCity)) {
                    $data['postazasiTelepules'] = $this->postalCity;
                }
                if (!empty($this->postalAddress)) {
                    $data['postazasiCim'] = $this->postalAddress;
                }

                if (!empty($this->ledgerData)) {
                    $data['vevoFokonyv'] = $this->ledgerData->getXmlData();
                }

                if (!empty($this->id)) {
                    $data['azonosito'] = $this->id;
                }
                if (!empty($this->signatoryName)) {
                    $data['alairoNeve'] = $this->signatoryName;
                }
                if (!empty($this->phone)) {
                    $data['telefonszam'] = $this->phone;
                }
                if (!empty($this->comment)) {
                    $data['megjegyzes'] = $this->comment;
                }
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_INVOICE:
                if (!empty($this->email)) {
                    $data['email'] = $this->email;
                }
                if (!empty($this->taxNumber)) {
                    $data['adoszam'] = $this->taxNumber;
                }
                if (!empty($this->taxNumberEU)) {
                    $data['adoszamEU'] = $this->taxNumberEU;
                }
                break;
            default:
                throw new SzamlaAgentException(sprintf('No XML schema defined: %s', $request->getXmlName()));
        }

        return $data;
    }

    protected function setRequiredFields(array $requiredFields): self
    {
        $this->requiredFields = $requiredFields;

        return $this;
    }

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

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function shouldSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmailState(bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    public function setTaxPayer(int $taxPayer): self
    {
        $this->taxPayer = $taxPayer;

        return $this;
    }

    public function setTaxNumber(string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    public function setGroupIdentifier(string $groupIdentifier): self
    {
        $this->groupIdentifier = $groupIdentifier;

        return $this;
    }

    public function setTaxNumberEU(string $taxNumberEU): self
    {
        $this->taxNumberEU = $taxNumberEU;

        return $this;
    }

    /**
     * Postal data is optional
     */
    public function setPostalName(string $postalName): self
    {
        $this->postalName = $postalName;

        return $this;
    }

    /**
     * Postal data is optional
     */
    public function setPostalCountry(string $postalCountry): self
    {
        $this->postalCountry = $postalCountry;

        return $this;
    }

    /**
     * Postal data is optional
     */
    public function setPostalZip(string $postalZip): self
    {
        $this->postalZip = $postalZip;

        return $this;
    }

    /**
     * Postal data is optional
     */
    public function setPostalCity(string $postalCity): self
    {
        $this->postalCity = $postalCity;

        return $this;
    }

    /**
     * Postal data is optional
     */
    public function setPostalAddress(string $postalAddress): self
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }

    public function setLedgerData(BuyerLedger $ledgerData): self
    {
        $this->ledgerData = $ledgerData;

        return $this;
    }

    /**
     * If enabled on the settings page (https://www.szamlazz.hu/szamla/beallitasok)
     * this name will appear below the signature line.
     */
    public function setSignatoryName(string $signatoryName): self
    {
        $this->signatoryName = $signatoryName;

        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
