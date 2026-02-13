<?php

namespace Omisai\Szamlazzhu\Header;

class ReverseReceiptHeader extends ReceiptHeader
{
    protected array $requiredFields = ['receiptNumber'];

    public function __construct(string $receiptNumber = '')
    {
        parent::__construct($receiptNumber);
        $this->setType(Type::REVERSE_RECEIPT);
    }
}
