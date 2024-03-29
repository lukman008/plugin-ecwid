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

require_once './helpers.php';
require_once './paystack-plugin-tracker.php';


session_start();
//Get data from Initialization page
$reference = filter_input(INPUT_GET, 'reference');
session_unset(); 

$trx = R::findOne('request', 'reference = ?', [$reference]);

$ecwid_ref = $trx->ecwid_ref;
$verify_data = $trx->verify_data;
$verify = json_decode($verify_data);

$store_id = $verify->storeId;
$token_id = $verify->token;
$return_url = $verify->returnUrl;
$secret_key = $verify->secretKey;
$public_key = $verify->publicKey;


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
    $result = json_decode($response, true);

    if (!$result['data']) {
        echo $result['message'];
        exit();
    } else {
        $url = "https://app.ecwid.com/api/v3/" . $store_id . "/orders/" . $ecwid_ref . "?token=" . $token_id;

        if ($result['data']['status'] == 'success') {
            //PSTK plugin tracker
            $pstk_logger = new ecwid_paystack_plugin_tracker('ecwid', $public_key);
            $pstk_logger->log_transaction_success($reference);

            //----------------------------------
            
            //UPDATE ORDER STATUS TO PAID

            $data = array('paymentStatus'=>'PAID');
        } else {
            //UPDATE ORDER STATUS TO CANCELLED
            $data = array('paymentStatus'=>'CANCELLED');            
        }

        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response  = curl_exec($ch);

        curl_close($ch);

        header("Location: " . $return_url);
    }
}

