<?php

use Omisai\Szamlazzhu\Seller;

describe('Seller', function () {
    it('can be instantiated', function () {
        $seller = new Seller;
        expect($seller)->toBeInstanceOf(Seller::class);
    });

    it('can set bank', function () {
        $seller = new Seller;
        $result = $seller->setBank('OTP Bank');

        expect($result)->toBeInstanceOf(Seller::class);
    });

    it('can set bank account', function () {
        $seller = new Seller;
        $result = $seller->setBankAccount('HU12 1234 5678 9012 3456 7890 1234');

        expect($result)->toBeInstanceOf(Seller::class);
    });

    it('can set email reply to', function () {
        $seller = new Seller;
        $result = $seller->setEmailReplyTo('reply@example.com');

        expect($result)->toBeInstanceOf(Seller::class);
        expect($seller->getEmailReplyTo())->toBe('reply@example.com');
    });

    it('can set email subject', function () {
        $seller = new Seller;
        $result = $seller->setEmailSubject('Invoice Notification');

        expect($result)->toBeInstanceOf(Seller::class);
        expect($seller->getEmailSubject())->toBe('Invoice Notification');
    });

    it('can set email content', function () {
        $seller = new Seller;
        $content = 'Dear Customer, please find your invoice attached.';
        $result = $seller->setEmailContent($content);

        expect($result)->toBeInstanceOf(Seller::class);
        expect($seller->getEmailContent())->toBe($content);
    });

    it('can set signatory name', function () {
        $seller = new Seller;
        $result = $seller->setSignatoryName('John Seller');

        expect($result)->toBeInstanceOf(Seller::class);
    });

    it('returns fluent interface for all setters', function () {
        $seller = new Seller;

        $result = $seller
            ->setBank('Test Bank')
            ->setBankAccount('1234567890')
            ->setEmailReplyTo('test@test.com')
            ->setEmailSubject('Subject')
            ->setEmailContent('Content')
            ->setSignatoryName('Signatory');

        expect($result)->toBeInstanceOf(Seller::class);
    });

    it('can create complete seller using helper', function () {
        $seller = makeSeller();

        expect($seller)->toBeInstanceOf(Seller::class);
    });
});
