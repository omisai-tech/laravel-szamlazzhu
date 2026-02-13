<?php

namespace Omisai\Szamlazzhu\Header;

use Omisai\Szamlazzhu\SzamlaAgentException;
use Omisai\Szamlazzhu\SzamlaAgentRequest;

class ProformaHeader extends InvoiceHeader
{
    /**
     * @throws SzamlaAgentException
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(Type::PROFORMA_INVOICE);
        $this->setPaid(false);
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

        $data = [];
        switch ($request->getXmlName()) {
            case $request::XML_SCHEMA_DELETE_PROFORMA:
                if (!empty($this->invoiceNumber)) {
                    $data['szamlaszam'] = $this->invoiceNumber;
                }
                if (!empty($this->orderNumber)) {
                    $data['rendelesszam'] = $this->orderNumber;
                }
                break;
            default:
                $data = parent::buildXmlData($request);
        }

        return $data;
    }
}
