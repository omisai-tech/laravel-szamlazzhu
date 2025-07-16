<?php

namespace Omisai\Szamlazzhu\Header;

use Carbon\Carbon;
use Omisai\Szamlazzhu\Document\Document;
use Omisai\Szamlazzhu\Document\Invoice\Invoice;
use Omisai\Szamlazzhu\FieldsValidationTrait;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\HasXmlBuildWithRequestInterface;
use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;
use Omisai\Szamlazzhu\SzamlaAgentUtil;
use Omisai\Szamlazzhu\Header\Type;
use Omisai\Szamlazzhu\Language;

class InvoiceHeader extends DocumentHeader implements HasXmlBuildWithRequestInterface
{
    use FieldsValidationTrait;

    protected string $invoiceNumber;

    /**
     * INVOICE_TYPE_P_INVOICE : papírszámla
     * INVOICE_TYPE_E_INVOICE : e-számla
     */
    protected int $invoiceType;

    protected Carbon $issueDate;

    protected Language $language;

    protected Carbon $fulfillment;

    protected Carbon $paymentDue;

    protected string $extraLogo;

    protected float $correctionToPay;

    protected string $correctivedNumber;

    protected string $orderNumber;

    protected string $proformaNumber;

    protected bool $paid = false;

    /**
     * HU: Ez a bizonylat árrés alapján áfázik-e?
     */
    protected bool $profitVat = false;

    /**
     * INVOICE_TEMPLATE_DEFAULT      : 'SzlaMost';
     * INVOICE_TEMPLATE_TRADITIONAL  : 'SzlaAlap';
     * INVOICE_TEMPLATE_ENV_FRIENDLY : 'SzlaNoEnv';
     * INVOICE_TEMPLATE_8CM          : 'Szla8cm';
     * INVOICE_TEMPLATE_RETRO        : 'SzlaTomb';
     */
    protected string $invoiceTemplate = Invoice::INVOICE_TEMPLATE_DEFAULT;

    protected string $prePaymentInvoiceNumber;

    protected bool $previewPdf = false;

    protected bool $euVat = false;

    protected array $requiredFields = ['issueDate', 'fulfillment', 'paymentDue', 'paymentMethod', 'currency', 'language'];

    /**
     * @throws SzamlaAgentException
     */
    public function __construct(int $type = Invoice::INVOICE_TYPE_P_INVOICE)
    {
        if (!empty($type)) {
            $this->setDefaultData($type);
        }
    }

    /**
     * @throws SzamlaAgentException
     * @throws \Exception
     */
    public function setDefaultData(int $type)
    {
        $this->setType(Type::INVOICE);

        $this->setInvoiceType($type);

        $this->setIssueDate(Carbon::now());

        $this->setPaymentMethod(PaymentMethod::PAYMENT_METHOD_TRANSFER);

        $this->setCurrency(Document::getDefaultCurrency());

        $this->setLanguage(Document::getDefaultLanguage());

        $this->setFulfillment(Carbon::now());

        $this->setPaymentDue(Carbon::now()->addDays(SzamlaAgentUtil::DEFAULT_ADDED_DAYS));
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(?SzamlaAgentRequest $request = null): array
    {
        if (empty($request)) {
            throw new SzamlaAgentException(SzamlaAgentException::XML_DATA_NOT_AVAILABLE);
        }

        $this->validateFields();

        $data = [
            'keltDatum' => $this->issueDate->format('Y-m-d'),
            'teljesitesDatum' => $this->fulfillment->format('Y-m-d'),
            'fizetesiHataridoDatum' => $this->paymentDue->format('Y-m-d'),
            'fizmod' => $this->getPaymentMethod(),
            'penznem' => $this->getCurrency(),
            'szamlaNyelve' => $this->getLanguage(),
        ];

        if (!empty($this->comment)) {
            $data['megjegyzes'] = $this->comment;
        }
        if (!empty($this->exchangeBank)) {
            $data['arfolyamBank'] = $this->exchangeBank;
        }

        if (!empty($this->exchangeRate)) {
            $data['arfolyam'] = $this->exchangeRate;
        }

        if (!empty($this->orderNumber)) {
            $data['rendelesSzam'] = $this->orderNumber;
        }
        if (!empty($this->proformaNumber)) {
            $data['dijbekeroSzamlaszam'] = $this->proformaNumber;
        }
        if ($this->isPrePayment()) {
            $data['elolegszamla'] = $this->isPrePayment();
        }
        if ($this->isFinal()) {
            $data['vegszamla'] = $this->isFinal();
        }
        if (!empty($this->prePaymentInvoiceNumber)) {
            $data['elolegSzamlaszam'] = $this->prePaymentInvoiceNumber;
        }
        if ($this->isCorrective()) {
            $data['helyesbitoszamla'] = $this->isCorrective();
        }
        if (!empty($this->correctivedNumber)) {
            $data['helyesbitettSzamlaszam'] = $this->correctivedNumber;
        }
        if ($this->isProforma()) {
            $data['dijbekero'] = $this->isProforma();
        }
        if ($this->isDeliveryNote()) {
            $data['szallitolevel'] = $this->isDeliveryNote();
        }
        if (!empty($this->extraLogo)) {
            $data['logoExtra'] = $this->extraLogo;
        }
        if (!empty($this->prefix)) {
            $data['szamlaszamElotag'] = $this->prefix;
        }

        if (!empty($this->correctionToPay)) {
            $data['fizetendoKorrekcio'] = $this->correctionToPay;
        }

        if ($this->isPaid()) {
            $data['fizetve'] = true;
        }
        if ($this->isProfitVat()) {
            $data['arresAfa'] = true;
        }

        $data['eusAfa'] = ($this->isEuVat() ? true : false);

        if (!empty($this->invoiceTemplate)) {
            $data['szamlaSablon'] = $this->invoiceTemplate;
        }

        if ($this->isPreviewPdf()) {
            $data['elonezetpdf'] = true;
        }

        return $data;
    }

    public function setIssueDate(Carbon $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language->value;
    }

    public function setFulfillment(Carbon $fulfillment): self
    {
        $this->fulfillment = $fulfillment;

        return $this;;
    }

    public function setPaymentDue(Carbon $paymentDue): self
    {
        $this->paymentDue = $paymentDue;

        return $this;
    }

    public function setExtraLogo(string $extraLogo): self
    {
        $this->extraLogo = $extraLogo;

        return $this;
    }

    public function setCorrectionToPay(float $correctionToPay): self
    {
        $this->correctionToPay = (float) $correctionToPay;

        return $this;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function setPrePaymentInvoiceNumber(string $prePaymentInvoiceNumber): self
    {
        $this->prePaymentInvoiceNumber = $prePaymentInvoiceNumber;

        return $this;
    }

    public function setProformaNumber(string $proformaNumber): self
    {
        $this->proformaNumber = $proformaNumber;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function isProfitVat(): bool
    {
        return $this->profitVat;
    }

    public function setProfitVat(bool $profitVat): self
    {
        $this->profitVat = $profitVat;

        return $this;
    }

    public function setCorrectivedNumber(string $correctivedNumber): self
    {
        $this->correctivedNumber = $correctivedNumber;

        return $this;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Invoice::INVOICE_TEMPLATE_DEFAULT (számlázz.hu ajánlott számlakép)
     * Invoice::INVOICE_TEMPLATE_TRADITIONAL (tradicionális számlakép)
     * Invoice::INVOICE_TEMPLATE_ENV_FRIENDLY (borítékbarát számlakép)
     * Invoice::INVOICE_TEMPLATE_8CM (hőnyomtatós számlakép - 8 cm széles)
     * Invoice::INVOICE_TEMPLATE_RETRO (retró kéziszámla számlakép)
     */
    public function setInvoiceTemplate(string $invoiceTemplate): self
    {
        $this->invoiceTemplate = $invoiceTemplate;

        return $this;
    }

    public function setInvoiceType($type)
    {
        $this->invoiceType = $type;
    }

    public function isEInvoice(): bool
    {
        return $this->invoiceType == Invoice::INVOICE_TYPE_E_INVOICE;
    }

    protected function setRequiredFields(array $requiredFields): self
    {
        $this->requiredFields = $requiredFields;

        return $this;
    }

    public function isPreviewPdf(): bool
    {
        return $this->previewPdf;
    }

    /**
     * HU: Beállítja a bizonylatot előnézeti PDF-re.
     * Ebben az esetben bizonylat nem készül.
     */
    public function setPreviewPdf(bool $previewPdf): self
    {
        $this->previewPdf = $previewPdf;

        return $this;
    }

    public function isEuVat(): bool
    {
        return $this->euVat;
    }

    /**
     * HU: Beállítja a bizonylathoz, hogy nem magyar áfát tartalmaz-e.
     * Ha tartalmaz, akkor a bizonylat adatai nem lesznek továbbítva a NAV Online Számla rendszere felé.
     */
    public function setEuVat(bool $euVat): self
    {
        $this->euVat = $euVat;

        return $this;
    }
}
