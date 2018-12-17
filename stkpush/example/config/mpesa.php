<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Domain 
    |--------------------------------------------------------------------------
    |
    | This is the url where all the endpoints originates from. 
    */

    'apiUrl' => 'https://api.safaricom.co.ke/',

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    |
    | This determines the state of the package, whether to use in sandbox mode or not.
    |
    */

    'is_sandbox' =>false,

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | These are the credentials to be used to transact with the M-Pesa API
    */
    'apps' => [
        'default' => [
            'consumer_key' =>'iCZxJfHicVi5eYJkeWOyvqtYJA4MGgiS',
    
            'consumer_secret' =>'TZHshDySCybLWxPw',
        ],
        'bulk' => [
            'consumer_key' => '',
            'consumer_secret' => '',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | File Cache Location
    |--------------------------------------------------------------------------
    |
    | This will be the location on the disk where the caching will be done.
    |
    */

    'cache_location' => 'cache',


    /*
    |--------------------------------------------------------------------------
    | Callback Method
    |--------------------------------------------------------------------------
    |
    | This is the request method to be used on the Callback URL on communication
    | with your server.
    |
    | e.g. GET | POST
    |
    */

    'callback_method' => 'POST',

    /*
    |--------------------------------------------------------------------------
    | LipaNaMpesa API Online Config
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */
    'lnmo' => [
        /*
        |--------------------------------------------------------------------------
        | Paybill Number
        |--------------------------------------------------------------------------
        |
        | This is a registered Paybill Number that will be used as the Merchant ID
        | on every transaction. This is also the account to be debited.
        |
        |
        |
        */
        'short_code' =>715423,

        /*
        | STK Push callback URL
        |--------------------------------------------------------------------------
        |
        | This is a fully qualified endpoint that will be queried by Safaricom's
        | API on completion or failure of a push transaction.
        |
        */
        'callback' => null,
        
        /*
        |--------------------------------------------------------------------------
        | SAG Passkey
        |--------------------------------------------------------------------------
        |
        | This is the secret SAG Passkey generated by Safaricom on registration
        | of the Merchant's Paybill Number.
        |
        */
        'passkey' => '0a46258e4fbcdb1b8f766f667bf9394992d2b8552842185816c4aac23956faee',

        /*
        |--------------------------------------------------------------------------
        | Default Transaction Type
        |--------------------------------------------------------------------------
        |
        | This is the Default Transaction Type set on every STK Push request
        |
        */
        'default_transaction_type' => 'CustomerPayBillOnline'

    ],

    /*
    |--------------------------------------------------------------------------
    | C2B Config
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */

    'c2b' => [
        'confirmation_url' => '',

        'validation_url' => '',

        'on_timeout' => 'Completed',

        'short_code' => '715423',

        'test_phone_number' => '254722537792',

        'default_command_id' => 'CustomerPayBillOnline'
    ],

    /*
    |--------------------------------------------------------------------------
    | B2C Config
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */

    'b2c' => [
        'initiator_name' => 'apiop59',

        'default_command_id' => 'BusinessPayment',

        'security_credential' => 'YAL2yKrn',

        'short_code' => '602973',

        'test_phone_number' => '254708374149',

        'result_url' => '',

        'timeout_url' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | B2B API Config
    |--------------------------------------------------------------------------
    |
    | This is a fully qualified endpoint that will be be queried by Safaricom's
    | API on completion or failure of the transaction.
    |
    */

    'b2b' => [
        'initiator_name' => 'testapi0297',

        'default_command_id' => 'BusinessPayBill',

        'security_credential' => 'YAL2yKrn',

        'short_code' => '600256',

        'test_phone_number' => '254708374149',

        'result_url' => '',

        'timeout_url' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Balance Config
    |--------------------------------------------------------------------------
    |
    | This is configurations that is required by Safaricom's Account Balance Api
    | 
    |
    */

    'account_balance' => [
        'initiator_name' => 'testapi0297',

        'security_credential' => 'YAL2yKrn',

        'default_command_id' => 'AccountBalance',

        'short_code' => '600256',

        'result_url' => '',

        'timeout_url' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Status API Config
    |--------------------------------------------------------------------------
    |
    | This is configurations that is required by Safaricom's Transaction Status Api
    | 
    |
    */

    'transaction_status' => [
        'initiator_name' => 'testapi0297',

        'security_credential' => 'YAL2yKrn',

        'default_command_id' => 'TransactionStatusQuery',

        'short_code' => '600256',

        'result_url' => '',

        'timeout_url' => ''
    ],

    /*
    |--------------------------------------------------------------------------
    | Reversal API Config
    |--------------------------------------------------------------------------
    |
    | This is configurations that is required by Safaricom's Transaction Status Api
    | 
    |
    */

    'reversal' => [
        'initiator_name' => 'testapi0297',

        'security_credential' => 'YAL2yKrn',

        'default_command_id' => 'TransactionReversal',

        'short_code' => '600256',

        'result_url' => '',

        'timeout_url' => ''
    ],

];
