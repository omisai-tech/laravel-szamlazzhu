<?php

use Omisai\Szamlazzhu\Response\InvoiceResponse;

describe('InvoiceResponse', function () {
    it('has correct INVOICE_NOTIFICATION_SEND_FAILED constant', function () {
        expect(InvoiceResponse::INVOICE_NOTIFICATION_SEND_FAILED)->toBe(56);
    });
});
