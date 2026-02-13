<?php

namespace Omisai\Szamlazzhu;

class SzamlaAgentException extends \Exception
{
    public const SYSTEM_DOWN = 'The site is currently under maintenance. Please come back in a few minutes.';

    public const REQUEST_TYPE_NOT_EXISTS = 'The request type does not exist';

    public const RESPONSE_TYPE_NOT_EXISTS = 'The response type does not exist';

    public const XML_SCHEMA_TYPE_NOT_EXISTS = 'The XML schema type does not exist';

    public const XML_KEY_NOT_EXISTS = 'XML key does not exist';

    public const XML_NOT_VALID = 'The assembled XML is not valid';

    public const XML_DATA_NOT_AVAILABLE = 'An error occurred while assembling XML data: no data available.';

    public const XML_DATA_BUILD_FAILED = 'Failed to build XML data';

    public const FIELDS_CHECK_ERROR = 'Error during field validation';

    public const DATE_FORMAT_NOT_EXISTS = 'Date format does not exist';

    public const NO_AGENT_INSTANCE_WITH_USERNAME = 'No Invoice Agent instance instantiated with this username!';

    public const NO_AGENT_INSTANCE_WITH_APIKEY = 'No Invoice Agent instance instantiated with this API key!';

    public const NO_SZLAHU_KEY_IN_HEADER = 'Invalid response!';

    public const DOCUMENT_DATA_IS_MISSING = 'Invoice PDF data is missing!';

    public const PDF_FILE_SAVE_SUCCESS = 'PDF file saved successfully';

    public const PDF_FILE_SAVE_FAILED = 'Failed to save PDF file';

    public const AGENT_RESPONSE_NO_CONTENT = 'The Invoice Agent response has no content!';

    public const AGENT_RESPONSE_NO_HEADER = 'The Invoice Agent response does not contain a header!';

    public const AGENT_RESPONSE_IS_EMPTY = 'The Invoice Agent response cannot be empty!';

    public const AGENT_ERROR = 'Agent error';

    public const FILE_CREATION_FAILED = 'Failed to create file.';

    public const INVOICE_NOTIFICATION_SEND_FAILED = 'Failed to deliver invoice notification';

    public const INVALID_JSON = 'Invalid JSON';

    public const INVOICE_EXTERNAL_ID_IS_EMPTY = 'The external invoice ID is empty';

    public const CONNECTION_ERROR = 'Connection failed';

    public const XML_FILE_SAVE_FAILED = 'Failed to save XML file';

    public const TAX_PAYER_RESPONSE = 'The response is TAXPAYER type. Custom XML processing required';

    public function __toString()
    {
        return self::class.": [{$this->code}]: {$this->message}\n";
    }
}
