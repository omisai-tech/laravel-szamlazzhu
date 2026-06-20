<?php

namespace Omisai\Szamlazzhu\Header;

use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;

class DocumentHeader
{
    protected Type $type;

    protected PaymentMethod $paymentMethod;

    protected Currency $currency;

    protected string $prefix = '';

    protected string $comment = '';

    /**
     * HU: Devizás bizonylat esetén meg kell adni, hogy melyik bank árfolyamával
     * számoltuk a bizonylaton a forintos ÁFA értéket.
     * Ha 'MNB' és nincs megadva az árfolyam ($exchangeRate),
     * akkor az 'MNB' aktuális árfolyamát használjuk a bizonylat elkészítésekor.
     */
    protected string $exchangeBank = 'MNB';

    /**
     * HU: Ha nincs megadva vagy 0-t adunk meg az árfolyam ($exchangeRate) értékének
     * és a megadott pénznem ($currency) létezik az MNB adatbázisában,
     * akkor az MNB aktuális árfolyamát használjuk a számlakészítéskor.
     */
    protected float $exchangeRate = 0.0;

    protected array $requiredFields = [];

    protected string $orderNumber = '';

    public function setType(Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod->value;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function setPaymentMethodByString(string $paymentMethod): self
    {
        $this->paymentMethod = PaymentMethod::tryFrom($paymentMethod);

        return $this;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setExchangeBank(string $exchangeBank): self
    {
        $this->exchangeBank = $exchangeBank;

        return $this;
    }

    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = (float) $exchangeRate;

        return $this;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function isInvoice(): bool
    {
        return $this->type === Type::INVOICE;
    }

    public function isReserveInvoice(): bool
    {
        return $this->type === Type::REVERSE_INVOICE;
    }

    public function isNotReserveInvoice(): bool
    {
        return $this->type !== Type::REVERSE_INVOICE;
    }

    public function isPrePayment(): bool
    {
        return $this->type === Type::PREPAYMENT_INVOICE;
    }

    public function isFinal(): bool
    {
        return $this->type === Type::FINAL_INVOICE;
    }

    public function isCorrective(): bool
    {
        return $this->type === Type::CORRECTIVE_INVOICE;
    }

    public function isProforma(): bool
    {
        return $this->type === Type::PROFORMA_INVOICE;
    }

    public function isDeliveryNote(): bool
    {
        return $this->type === Type::DELIVERY_NOTE;
    }

    public function isReceipt(): bool
    {
        return $this->type === Type::RECEIPT;
    }

    public function isReverseReceipt(): bool
    {
        return $this->type === Type::REVERSE_RECEIPT;
    }
}
