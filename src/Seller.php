<?php

namespace Omisai\Szamlazzhu;

class Seller
{
    use FieldsValidationTrait;

    protected string $bank = '';

    protected string $bankAccount = '';

    protected string $emailReplyTo = '';

    protected string $emailSubject = '';

    protected string $emailContent = '';

    protected string $signatoryName = '';

    protected array $requiredFields = [];

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        $this->validateFields();

        $data = [];
        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_INVOICE:
                if (!empty($this->bank)) {
                    $data['bank'] = $this->bank;
                }
                if (!empty($this->bankAccount)) {
                    $data['bankszamlaszam'] = $this->bankAccount;
                }

                $emailData = $this->getXmlEmailData();
                if (!empty($emailData)) {
                    $data = array_merge($data, $emailData);
                }
                if (!empty($this->signatoryName)) {
                    $data['alairoNeve'] = $this->signatoryName;
                }
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_INVOICE:
                $data = $this->getXmlEmailData();
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::XML_SCHEMA_TYPE_NOT_EXISTS.": {$request->getXmlName()}");
        }

        return $data;
    }

    protected function getXmlEmailData(): array
    {
        $data = [];
        if (!empty($this->emailReplyTo)) {
            $data['emailReplyto'] = $this->emailReplyTo;
        }
        if (!empty($this->emailSubject)) {
            $data['emailTargy'] = $this->emailSubject;
        }
        if (!empty($this->emailContent)) {
            $data['emailSzoveg'] = $this->emailContent;
        }

        return $data;
    }

    public function setBank(string $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function setBankAccount(string $bankAccount): self
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    public function setEmailReplyTo(string $emailReplyTo): self
    {
        $this->emailReplyTo = $emailReplyTo;

        return $this;
    }

    public function setEmailSubject(string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    public function setEmailContent(string $emailContent): self
    {
        $this->emailContent = $emailContent;

        return $this;
    }

    public function setSignatoryName(string $signatoryName): self
    {
        $this->signatoryName = $signatoryName;

        return $this;
    }

    public function getEmailReplyTo(): string
    {
        return $this->emailReplyTo;
    }

    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    public function getEmailContent(): string
    {
        return $this->emailContent;
    }
}
