<?php

/**
 * Integration for Paystack to the Ecwid platform
 *
 * PHP version 7.0
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Controller
 * @package    PaystackEcwid
 * @author     Stephen Amaza <steve@paystack.com>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    SVN: $Id$
 * @link       #
 * @deprecated No depracation
 */

require_once './helpers.php';

/**
 * Get and decode Ecwid details
 *
 * @param string $app_secret_key encryption key gotten from app secret key
 * @param object $data           response from POST request

 * @throws ErrorException if the response is not of type 'object'.
 * @author Ecwid team
 * @return object $json
 */ 
function getEcwidPayload($app_secret_key, $data) 
{
    $url = 'https://paystackintegrations.com/s/ecwid/payment/api/paystack.php';

    // Get the encryption key (16 first bytes of the app's client_secret key)
    $encryption_key = substr($app_secret_key, 0, 16);

    // Decrypt payload
    $json_data = AES_128_decrypt($encryption_key, $data);

    // Decode json
    $json_decoded = json_decode($json_data, true);
    return $json_decoded;
}

/**
 * Does something interesting
 *
 * @param string $key  encryption key gotten from app secret key
 * @param object $data response from POST request
 * 
 * @throws ErrorException if the response is not of type 'object'.
 * @author Ecwid team
 * @return object $json
 */ 
function AES_128_decrypt($key, $data) 
{
    // Ecwid sends data in url-safe base64. 
    // Convert the raw data to the original base64 first
    $base64_original = str_replace(array('-', '_'), array('+', '/'), $data);

    // Get binary data
    $decoded = base64_decode($base64_original);

    // Initialization vector is the first 16 bytes of the received data
    $iv = substr($decoded, 0, 16);

    // The payload itself is is the rest of the received data
    $payload = substr($decoded, 16);

    // Decrypt raw binary payload
    $json = openssl_decrypt($payload, "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv);

    return $json;
}

/**
 * Updates the order status to failed
 *
 * @param string $store_id  Ecwid identifier for store
 * @param string $reference encryption key gotten from app secret key
 * @param string $token     response from POST request
 * 
 * @throws ErrorException if the response is not of type 'object'.
 * @author Stephen Amaza
 * @return object $json
 */ 
function updateOrder($store_id, $reference, $token)
{
    $url = "https://app.ecwid.com/api/v3/" . $store_id . "/orders/" . $reference .
    "?" . $token;
    $data = array('paymentStatus'=>'INCOMPLETE');
    $data_json = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
            )
    );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response  = curl_exec($ch);
    curl_close($ch);
}

// Get payload from the POST and process it
$ecwid_payload = $_POST['data'];
$client_secret = "ZQNXyVFBbUjAgoYGqDihDtsi3uKkAyTt";

// The resulting JSON array will be in $result variable
$result = getEcwidPayload($client_secret, $ecwid_payload);

// Get store info from the payload
$token            = $result['token'];
$storeId          = $result['storeId'];
$merchantSettings = $result['merchantAppSettings'];
$returnUrl        = $result['returnUrl'];
$cartDetails      = $result['cart'];
$currency         = $cartDetails['currency'];
$orderDetails     = $cartDetails['order'];
$email            = $orderDetails['email'];
$amount           = $orderDetails['total'];
$refererUrl       = $orderDetails['refererUrl'];
$ecwid_ref        = $orderDetails['referenceTransactionId'];

$reference        = $ecwid_ref . '_' . ((rand(0, 9) * 100) + 1);

if ($merchantSettings['liveMode'] == "true") {
    $secretKey = trim($merchantSettings['liveSecretKey']);
} else {
    $secretKey = trim($merchantSettings['testSecretKey']);
}

if (!isset($verifyData)) {
    $verifyData = new stdClass();
}

$verifyData->token     = $token;
$verifyData->storeId   = $storeId;
$verifyData->secretKey = $secretKey;
$verifyData->returnUrl = $returnUrl;

$verify = json_encode($verifyData);

// Initialize transaction
$postdata = [
    'email' => $email, 
    'amount' => $amount * 100,
    'reference' => $reference,
    'currency' => $currency,
    'callback_url' => 
    'https://paystackintegrations.com/s/ecwid/payment/api/verify.php',
    'metadata' => [
        'custom_fields' => [
            [
            "display_name" => "Store Reference",
            "variable_name" => "ecwid_ref",
            "value" => $ecwid_ref,
            ],
            [
            "display_name" => "Paid On",
            "variable_name" => "paid_on",
            "value" => "Ecwid Store",
            ],
        ]
    ],
];

R::setAutoResolve(true);
$request = R::dispense('request');

// store columns for database
$request->email       = $email;
$request->reference   = $reference;
$request->ecwid_ref   = $ecwid_ref;
$request->amount      = $amount;
$request->referer     = $refererUrl;
$request->verify_data = $verify;

//Store columns in database
$id = R::store($request);

$url = 'https://api.paystack.co/transaction/initialize';

$result = [];
$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
curl_setopt($ch, CURLOPT_URL, $url);
$headers = [
    "Authorization: Bearer $secretKey",
    'Content-Type: application/json',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$api_request = curl_exec($ch);

curl_close($ch);

if ($api_request) {
     $response = json_decode($api_request);
}

if ($response->status) {
    header('Location: ' . $response->data->authorization_url);
} else {
    updateOrder($storeId, $ecwid_ref, $token);
    echo "<p style='text-align: center; margin-top:50px'>$response->message ...</p>";
    echo "<script>setTimeout(\"location.href = '$returnUrl';\",1500);</script>";
}

?>