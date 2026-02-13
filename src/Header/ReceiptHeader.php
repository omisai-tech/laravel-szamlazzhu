<?php

namespace Omisai\Szamlazzhu\Header;

use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\FieldsValidationTrait;
use Omisai\Szamlazzhu\HasXmlBuildWithRequestInterface;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

class ReceiptHeader extends DocumentHeader implements HasXmlBuildWithRequestInterface
{
    use FieldsValidationTrait;

    protected string $receiptNumber;

    protected string $callId;

    protected string $pdfTemplate;

    protected string $buyerLedgerId;

    protected array $requiredFields = ['prefix', 'paymentMethod', 'currency'];

    public function __construct(string $receiptNumber = '')
    {
        $this->setType(Type::RECEIPT);
        $this->setPrefix(config('szamlazzhu.receipt.prefix'));
        $this->setReceiptNumber($receiptNumber);
        $this->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_CASH);
        $this->setCurrency(Document::getDefaultCurrency());
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array
    {
        if (empty($request)) {
            throw new SzamlaAgentException(SzamlaAgentException::XML_DATA_NOT_AVAILABLE);
        }

        $this->validateFields();

        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_CREATE_RECEIPT:
                $data = $this->buildFieldsData($request, [
                    'hivasAzonosito',
                    'elotag',
                    'fizmod',
                    'penznem',
                    'devizabank',
                    'devizaarf',
                    'megjegyzes',
                    'pdfSablon',
                    'fokonyvVevo',
                ]);
                break;
            case $request::XML_SCHEMA_CREATE_REVERSE_RECEIPT:
                $data = $this->buildFieldsData($request, ['nyugtaszam', 'pdfSablon', 'hivasAzonosito']);
                break;
            case $request::XML_SCHEMA_GET_RECEIPT:
                $data = $this->buildFieldsData($request, ['nyugtaszam', 'pdfSablon']);
                break;
            case $request::XML_SCHEMA_SEND_RECEIPT:
                $data = $this->buildFieldsData($request, ['nyugtaszam']);
                break;
            default:
                throw new SzamlaAgentException(SzamlaAgentException::XML_SCHEMA_TYPE_NOT_EXISTS.": {$request->getXmlName()}");
        }

        return $data;
    }

    /**
     * @throws SzamlaAgentException
     */
    private function buildFieldsData(SzamlaAgentRequest $request, array $fields): array
    {
        $data = [];

        if (empty($request) || !empty($field)) {
            throw new SzamlaAgentException(SzamlaAgentException::XML_DATA_NOT_AVAILABLE);
        }

        foreach ($fields as $key) {
            switch ($key) {
                case 'hivasAzonosito':
                    $value = !empty($this->callId) ? $this->callId : null;
                    break;
                case 'elotag':
                    $value = $this->prefix;
                    break;
                case 'fizmod':
                    $value = $this->getPaymentMethod();
                    break;
                case 'penznem':
                    $value = $this->getCurrency();
                    break;
                case 'devizabank':
                    $value = (!empty($this->exchangeBank)) ? $this->exchangeBank : null;
                    break;
                case 'devizaarf':
                    $value = (!empty($this->exchangeRate)) ? $this->exchangeRate : null;
                    break;
                case 'megjegyzes':
                    $value = (!empty($this->comment)) ? $this->comment : null;
                    break;
                case 'pdfSablon':
                    $value = (!empty($this->pdfTemplate)) ? $this->pdfTemplate : null;
                    break;
                case 'fokonyvVevo':
                    $value = (!empty($this->buyerLedgerId)) ? $this->buyerLedgerId : null;
                    break;
                case 'nyugtaszam':
                    $value = $this->receiptNumber;
                    break;
                default:
                    throw new SzamlaAgentException(SzamlaAgentException::XML_KEY_NOT_EXISTS.": {$key}");
            }

            if (isset($value)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * HU: A nyugta létrehozásánál NE használd, mert a kiállított nyugták számait a Számlázz.hu
     * a jogszabálynak megfelelően automatikusan osztja ki: 1-től indulva, kihagyásmentesen.
     *
     * @see https://tudastar.szamlazz.hu/gyik/szamlaszam-formatumok-mikor-kell-megadni
     */
    public function setReceiptNumber(string $receiptNumber): self
    {
        $this->receiptNumber = $receiptNumber;

        return $this;
    }

    protected function setRequiredFields(array $requiredFields): self
    {
        $this->requiredFields = $requiredFields;

        return $this;
    }

    public function setCallId(string $callId): self
    {
        $this->callId = $callId;

        return $this;
    }

    public function setPdfTemplate(string $pdfTemplate): self
    {
        $this->pdfTemplate = $pdfTemplate;

        return $this;
    }

    public function setBuyerLedgerId(string $buyerLedgerId): self
    {
        $this->buyerLedgerId = $buyerLedgerId;

        return $this;
    }
}
