<?php

use Omisai\Szamlazzhu\SzamlaAgentException;

describe('SzamlaAgentException', function () {
    it('can be instantiated', function () {
        $exception = new SzamlaAgentException('Test error');

        expect($exception)->toBeInstanceOf(SzamlaAgentException::class);
        expect($exception)->toBeInstanceOf(\Exception::class);
    });

    it('has correct SYSTEM_DOWN constant', function () {
        expect(SzamlaAgentException::SYSTEM_DOWN)
            ->toBe('The site is currently under maintenance. Please come back in a few minutes.');
    });

    it('has correct REQUEST_TYPE_NOT_EXISTS constant', function () {
        expect(SzamlaAgentException::REQUEST_TYPE_NOT_EXISTS)
            ->toBe('The request type does not exist');
    });

    it('has correct RESPONSE_TYPE_NOT_EXISTS constant', function () {
        expect(SzamlaAgentException::RESPONSE_TYPE_NOT_EXISTS)
            ->toBe('The response type does not exist');
    });

    it('has correct XML_SCHEMA_TYPE_NOT_EXISTS constant', function () {
        expect(SzamlaAgentException::XML_SCHEMA_TYPE_NOT_EXISTS)
            ->toBe('The XML schema type does not exist');
    });

    it('has correct XML_KEY_NOT_EXISTS constant', function () {
        expect(SzamlaAgentException::XML_KEY_NOT_EXISTS)
            ->toBe('XML key does not exist');
    });

    it('has correct XML_NOT_VALID constant', function () {
        expect(SzamlaAgentException::XML_NOT_VALID)
            ->toBe('The assembled XML is not valid');
    });

    it('has correct XML_DATA_NOT_AVAILABLE constant', function () {
        expect(SzamlaAgentException::XML_DATA_NOT_AVAILABLE)
            ->toBe('An error occurred while assembling XML data: no data available.');
    });

    it('has correct XML_DATA_BUILD_FAILED constant', function () {
        expect(SzamlaAgentException::XML_DATA_BUILD_FAILED)
            ->toBe('Failed to build XML data');
    });

    it('has correct FIELDS_CHECK_ERROR constant', function () {
        expect(SzamlaAgentException::FIELDS_CHECK_ERROR)
            ->toBe('Error during field validation');
    });

    it('has correct DATE_FORMAT_NOT_EXISTS constant', function () {
        expect(SzamlaAgentException::DATE_FORMAT_NOT_EXISTS)
            ->toBe('Date format does not exist');
    });

    it('has correct NO_AGENT_INSTANCE_WITH_USERNAME constant', function () {
        expect(SzamlaAgentException::NO_AGENT_INSTANCE_WITH_USERNAME)
            ->toBe('No Invoice Agent instance instantiated with this username!');
    });

    it('has correct NO_AGENT_INSTANCE_WITH_APIKEY constant', function () {
        expect(SzamlaAgentException::NO_AGENT_INSTANCE_WITH_APIKEY)
            ->toBe('No Invoice Agent instance instantiated with this API key!');
    });

    it('has correct NO_SZLAHU_KEY_IN_HEADER constant', function () {
        expect(SzamlaAgentException::NO_SZLAHU_KEY_IN_HEADER)
            ->toBe('Invalid response!');
    });

    it('has correct DOCUMENT_DATA_IS_MISSING constant', function () {
        expect(SzamlaAgentException::DOCUMENT_DATA_IS_MISSING)
            ->toBe('Invoice PDF data is missing!');
    });

    it('has correct PDF_FILE_SAVE_SUCCESS constant', function () {
        expect(SzamlaAgentException::PDF_FILE_SAVE_SUCCESS)
            ->toBe('PDF file saved successfully');
    });

    it('has correct PDF_FILE_SAVE_FAILED constant', function () {
        expect(SzamlaAgentException::PDF_FILE_SAVE_FAILED)
            ->toBe('Failed to save PDF file');
    });

    it('has correct AGENT_RESPONSE_NO_CONTENT constant', function () {
        expect(SzamlaAgentException::AGENT_RESPONSE_NO_CONTENT)
            ->toBe('The Invoice Agent response has no content!');
    });

    it('has correct AGENT_RESPONSE_NO_HEADER constant', function () {
        expect(SzamlaAgentException::AGENT_RESPONSE_NO_HEADER)
            ->toBe('The Invoice Agent response does not contain a header!');
    });

    it('has correct AGENT_RESPONSE_IS_EMPTY constant', function () {
        expect(SzamlaAgentException::AGENT_RESPONSE_IS_EMPTY)
            ->toBe('The Invoice Agent response cannot be empty!');
    });

    it('has correct AGENT_ERROR constant', function () {
        expect(SzamlaAgentException::AGENT_ERROR)
            ->toBe('Agent error');
    });

    it('has correct FILE_CREATION_FAILED constant', function () {
        expect(SzamlaAgentException::FILE_CREATION_FAILED)
            ->toBe('Failed to create file.');
    });

    it('has correct INVOICE_NOTIFICATION_SEND_FAILED constant', function () {
        expect(SzamlaAgentException::INVOICE_NOTIFICATION_SEND_FAILED)
            ->toBe('Failed to deliver invoice notification');
    });

    it('has correct INVALID_JSON constant', function () {
        expect(SzamlaAgentException::INVALID_JSON)
            ->toBe('Invalid JSON');
    });

    it('has correct INVOICE_EXTERNAL_ID_IS_EMPTY constant', function () {
        expect(SzamlaAgentException::INVOICE_EXTERNAL_ID_IS_EMPTY)
            ->toBe('The external invoice ID is empty');
    });

    it('has correct CONNECTION_ERROR constant', function () {
        expect(SzamlaAgentException::CONNECTION_ERROR)
            ->toBe('Connection failed');
    });

    it('has correct XML_FILE_SAVE_FAILED constant', function () {
        expect(SzamlaAgentException::XML_FILE_SAVE_FAILED)
            ->toBe('Failed to save XML file');
    });

    it('has correct TAX_PAYER_RESPONSE constant', function () {
        expect(SzamlaAgentException::TAX_PAYER_RESPONSE)
            ->toBe('The response is TAXPAYER type. Custom XML processing required');
    });

    it('can convert to string', function () {
        $exception = new SzamlaAgentException('Test error message', 500);

        $string = $exception->__toString();

        expect($string)->toContain('SzamlaAgentException');
        expect($string)->toContain('500');
        expect($string)->toContain('Test error message');
    });
});
