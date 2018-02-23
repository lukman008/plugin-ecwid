<?php
// Configure
define('WESTGATE_SECRET_KEY', 'sk_live_42868a0eb64f427cb52feef8410762021e25af50');
define('UNIVERSAL_MERCHANT_PASSWORD', 'SWyB*V6aC*VL$P*U9Xf*85b!');

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
define(
    'ECWID_NOT_VERIFIED_MESSAGE', 'There might be a configuration error with your integration.' . "\n" .
    'Kindly email support@paystack.com for assistance.'
);

// Database
define('DBPASS', 'vFj7wGqaFffa6ErC');
define('DBNAME', 'ecwid');
define('DBUSER', 'steve');
define('DBHOST', 'localhost');

// Ecwid fields accessed
define('ECWID_RESULT_FIELD', 'x_result');
define('ECWID_RESULT_COMPLETED', 'completed');
define('ECWID_RESULT_FAILED', 'failed');
define('ECWID_RESULT_PENDING', 'pending');

define('ECWID_CANCEL_URL_FIELD', 'x_url_cancel');
define('ECWID_CALLBACK_URL_FIELD', 'x_url_callback');
define('ECWID_COMPLETE_URL_FIELD', 'x_url_complete');