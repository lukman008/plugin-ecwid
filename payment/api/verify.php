<?php
/**
 * Verify the status of transaction and either return to storefront approving 
 * the transaction, or return to cart with failed message
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

ini_set('display_errors', 'on'); // display all reported errors when pushing output
error_reporting(-1); // report all errors, warnings and notices

session_start();
//Get data from Initialization page
$reference = $_GET["reference"];

$store_id = $_SESSION["store_id"];
$token_id = $_SESSION["token_id"];
$return_url = $_SESSION["return"];
$secret_key = $_SESSION["secret_key"];

session_unset(); 

$url = "https://api.paystack.co/transaction/verify/" . $reference;

$ch = curl_init();

curl_setopt_array(
    $ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $secret_key"
        ),
    ]
);

$response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}

