<?php
// Configure
define('WESTGATE_SECRET_KEY', 'sk_live_42868a0eb64f427cb52feef8410762021e25af50');
define('UNIVERSAL_MERCHANT_PASSWORD', 'SWyB*V6aC*VL$P*U9Xf*85b!');
define('PS_CALLBACK_URL', 'https://paystackintegrations.com/shopify/out.php');

// static urls
define('PAYSTACK_INIT_ENDPOINT', 'https://api.paystack.co/transaction/initialize');
define('PAYSTACK_IDENTPK_ENDPOINT', 'https://api.paystack.co/identpk');
define('PAYSTACK_VERIFY_ENDPOINT_PREFIX', 'https://api.paystack.co/transaction/verify/');
define('PAYSTACK_HOME', 'https://paystack.co');

// Required by CURL for Setting Headers
define('AUTH_HEADER_TITLE', 'Authorization');
define('BEARER_AND_SPACE', 'Bearer ');
define('CONTENT_TYPE_TITLE', 'Content-Type');
define('APPLICATION_SLASH_JSON', 'application/json');

// Paystack fields
define('PS_REFERENCE_FIELD', 'reference');
define('PS_EMAIL_FIELD', 'email');
define('PS_AMOUNT_IN_KOBO_FIELD', 'amount');
define('PS_DATA_FIELD', 'data');
define('PS_PLAN_FIELD', 'plan'); // Not used
define('PS_AUTH_URL_FIELD', 'authorization_url');
define('PS_CALLBACK_URL_FIELD', 'callback_url');
define('PS_METADATA_FIELD', 'metadata');
define('PS_TRANSACTION_REFERENCE_FIELD', 'reference');

define('PS_STATUS_FIELD', 'status');
define('PS_STATUS_SUCCESS', 'success');
define('PS_STATUS_FAILURE', 'failure');
define('PS_STATUS_PENDING', 'pending');

// Messages
define('ECWID_NOT_NGN_MESSAGE', 'Paystack only accepts NGN payment requests at this time.');
define('ECWID_NOT_SUCCESSFUL_MESSAGE', 'Transaction status is: ');
define('ECWID_NOT_VERIFIED_MESSAGE', 'There might be a configuration error with your integration.' . "\n" .
    'Kindly email support@paystack.com for assistance.');

// Database
define('DBPASS', 'vFj7wGqaFffa6ErC');
define('DBNAME', 'ecwid');
define('DBUSER', 'steve');
define('DBHOST', 'localhost');

// Ecwid fields accessed
define('ECWID_SPECIAL_FIELD_PREFIX', 'x_');
define('ECWID_ACCOUNT_ID_FIELD', 'x_account_id');
define('ECWID_REFERENCE_FIELD', 'x_reference');
define('ECWID_SIGNATURE_FIELD', 'x_signature');
define('ECWID_CURRENCY_FIELD', 'x_currency');
define('ECWID_TEST_FIELD', 'x_test');
define('ECWID_AMOUNT_FIELD', 'x_amount');
define('ECWID_GATEWAY_REFERENCE_FIELD', 'x_gateway_reference');
define('ECWID_TIMESTAMP_FIELD', 'x_timestamp');
define('ECWID_MESSAGE_FIELD', 'x_message');

define('ECWID_RESULT_FIELD', 'x_result');
define('ECWID_RESULT_COMPLETED', 'completed');
define('ECWID_RESULT_FAILED', 'failed');
define('ECWID_RESULT_PENDING', 'pending');

define('ECWID_CANCEL_URL_FIELD', 'x_url_cancel');
define('ECWID_CALLBACK_URL_FIELD', 'x_url_callback');
define('ECWID_COMPLETE_URL_FIELD', 'x_url_complete');