<?php

namespace Omisai\Szamlazzhu\CreditNote;

use Omisai\Szamlazzhu\FieldsValidationTrait;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\SzamlaAgentException;

class ReceiptCreditNote extends CreditNote
{
    use FieldsValidationTrait;

    public function __construct(PaymentMethod $paymentMethod = PaymentMethod::PAYMENT_METHOD_CASH, float $amount = 0.0, string $description = '')
    {
        parent::__construct($paymentMethod, $amount, $description);
    }

    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(): array
    {
        $this->validateFields();

        $data = [];
        $data['fizetoeszkoz'] = $this->getPaymentMethod();

        if (!empty($this->amount)) {
            $data['osszeg'] = $this->amount;
        }
        if (!empty($this->description)) {
            $data['leiras'] = $this->description;
        }

        return $data;
    }
}
