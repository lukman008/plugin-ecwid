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

    // Receive the POST request
    $response = postRequestFromEcwid($url);

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
    //$json = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv); // You can use this instead of openssl_decrupt, if mcrypt is enabled in your system

    return $json;
}

// Get payload from the POST and process it
$ecwid_payload = $_POST['data'];
$client_secret = "ZQNXyVFBbUjAgoYGqDihDtsi3uKkAyTt";

// The resulting JSON array will be in $result variable
$result = getEcwidPayload($client_secret, $ecwid_payload);

// Get store info from the payload
$token = $result['access_token'];
$storeId = $result['store_id'];
$lang = $result['lang'];
$viewMode = $result['view_mode'];

if (isset($result['public_token'])) {
    $public_token = $result['public_token'];
}

// URL Encoded App state passed to the app
if (isset($_GET['app_state'])) {
    $app_state = $_GET['app_state'];
}

// Get store specific data from storage endpoint
$url = 'https://app.ecwid.com/api/v3/' .$storeId. '/storage/' .'?token=' .$token;

$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$curlResult = curl_exec($ch);
curl_close($ch);

$curlResult = (json_decode($curlResult));
$apikeys = $curlResult -> {'value'};

if ($apikeys !== null ) {
    // set keys from storage
} else {
    // set default keys
}

//
//  Start the flow of your application
//  ...
?>