<?php

namespace Omisai\Szamlazzhu\Header;

enum Type
{
    case INVOICE;

    case REVERSE_INVOICE;

    case PREPAYMENT_INVOICE;

    case FINAL_INVOICE;

    case CORRECTIVE_INVOICE;

    case PROFORMA_INVOICE;

    case DELIVERY_NOTE;

    case RECEIPT;

    case REVERSE_RECEIPT;
}
