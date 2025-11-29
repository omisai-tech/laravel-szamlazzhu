<?php

use Omisai\Szamlazzhu\Response\AbstractResponse;

describe('AbstractResponse', function () {
    it('has correct RESULT_AS_TEXT constant', function () {
        expect(AbstractResponse::RESULT_AS_TEXT)->toBe(1);
    });

    it('has correct RESULT_AS_XML constant', function () {
        expect(AbstractResponse::RESULT_AS_XML)->toBe(2);
    });

    it('has correct RESULT_AS_TAXPAYER_XML constant', function () {
        expect(AbstractResponse::RESULT_AS_TAXPAYER_XML)->toBe(3);
    });
});
