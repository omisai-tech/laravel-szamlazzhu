<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Számlázz.hu API key
    |--------------------------------------------------------------------------
    |
    | This API key is generated by Számlázz.hu and attached to your billing
    | account.
    |
    */

    'api_key' => env('SZAMLAZZHU_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | File save configuration
    |--------------------------------------------------------------------------
    |
    | This API key is generated by Számlázz.hu and attached to your billing
    | account.
    |
    */

    'xml' => [
        /*
         *  Enable/Disable both XML save option
         */
        'file_save' => env('SZAMLAZZHU_XML_FILE_SAVE', true),

        'request_file_save' => env('SZAMLAZZHU_XML_REQUEST_FILE_SAVE', true),
        'response_file_save' => env('SZAMLAZZHU_XML_RESPONSE_FILE_SAVE', true),
    ],

    'pdf' => [
        'file_save' => env('SZAMLAZZHU_PDF_FILE_SAVE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Számlázz.hu logging
    |--------------------------------------------------------------------------
    |
    | The Számlázz.hu sending messages to this email address, if any error
    | accquire.
    |
    */

    'log_email' => env('SZAMLAZZHU_LOG_EMAIL', null),

];
