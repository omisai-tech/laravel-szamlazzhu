<?php

namespace Omisai\Szamlazzhu\Response;

use Carbon\Carbon;
use Omisai\Szamlazzhu\Currency;
use Omisai\Szamlazzhu\PaymentMethod;
use Omisai\Szamlazzhu\SzamlaAgentUtil;

class ReceiptResponse extends AbstractResponse
{
    protected int $id;

    protected string $receiptNumber = '';

    protected string $type;

    protected bool $isReserved;

    protected string $reservedReceiptNumber;

    protected string $createdAt;

    protected PaymentMethod $paymentMethod;

    protected Currency $currency;

    protected bool $isTestAccount;

    protected array $items;

    protected array $amounts;

    protected array $creditNotes;

    protected function parseData()
    {
        if (gettype($this->getData()) !== 'array' || empty($this->getData())) {
            return;
        }

        if ($this->agent->getResponseType() === self::RESULT_AS_TEXT) {
            $xmlData = new \SimpleXMLElement(base64_decode($this->getData()['result']['body']));
            $data = SzamlaAgentUtil::toArray($xmlData);
        } else {
            $data = $this->getData()['result'];
        }

        $base = [];
        if (isset($data['nyugta']['alap'])) {
            $base = $data['nyugta']['alap'];
        }

        if (isset($base['id'])) {
            $this->id = $base['id'];
        }
        if (isset($base['nyugtaszam'])) {
            $this->receiptNumber = $base['nyugtaszam'];
        }
        if (isset($base['tipus'])) {
            $this->type = $base['tipus'];
        }
        if (isset($base['stornozott'])) {
            $this->isReserved = ($base['stornozott'] === 'true');
        }
        if (isset($base['stornozottNyugtaszam'])) {
            $this->reservedReceiptNumber = $base['stornozottNyugtaszam'];
        }
        if (isset($base['kelt'])) {
            $this->createdAt = Carbon::createFromFormat('Y-m-d', $base['kelt']);
        }
        if (isset($base['fizmod'])) {
            $this->setPaymentMethodByString($base['fizmod']);
        }
        if (isset($base['penznem'])) {
            $this->setCurrencyByString($base['penznem']);
        }
        if (isset($base['teszt'])) {
            $this->isTestAccount = ($base['teszt'] === 'true');
        }
        if (isset($data['nyugta']['tetelek'])) {
            $this->items = $data['nyugta']['tetelek'];
        }
        if (isset($data['nyugta']['osszegek'])) {
            $this->amounts = $data['nyugta']['osszegek'];
        }
        if (isset($data['nyugta']['kifizetesek'])) {
            $this->creditNotes = $data['nyugta']['kifizetesek'];
        }
        if (isset($data['sikeres'])) {
            $this->isSuccess = ($data['sikeres'] === 'true');
        }

        if (isset($data['nyugtaPdf']) && !empty($data['nyugtaPdf'])) {
            $this->pdfFile = base64_decode($data['nyugtaPdf']);
        }
        if (isset($data['hibakod'])) {
            $this->errorCode = $data['hibakod'];
        }
        if (isset($data['hibauzenet'])) {
            $this->errorMessage = $data['hibauzenet'];
        }
    }

    protected function setPaymentMethodByString(string $paymentMethod)
    {
        $this->paymentMethod = PaymentMethod::tryFrom($paymentMethod);
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod->value;
    }

    public function setCurrencyByString(string $currency): void
    {
        $this->currency = Currency::tryFrom($currency);
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReservedReceiptNumber(): string
    {
        return $this->reservedReceiptNumber;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isReserved(): bool
    {
        return $this->isReserved;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function isTestAccount(): bool
    {
        return $this->isTestAccount;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getAmounts(): array
    {
        return $this->amounts;
    }

    public function getCreditNotes(): array
    {
        return $this->creditNotes;
    }

    public function getDocumentNumber(): string
    {
        return $this->receiptNumber;
    }
}
