<?php

require "init.php";

$merchant_id = "054c947f58c060a12b7aa03b8361a710";
$ipn_secret = "Predatore13@";
$debug_email = "darlingsh1337@protonmail.com";

$txn_id = $_POST['txn_id'];
$payment = Payment::where("gateway_id", $txn_id)->first();
$order_currency = $payment->to_currency; //BTC
$order_total = $payment->amount; //BTC

function edie($error_msg)
{
    global $debug_email;
    $report =  "ERROR : " . $error_msg . "\n\n";
    $report.= "POST DATA\n\n";
    foreach ($_POST as $key => $value) {
        $report .= "|$k| = |$v| \n";
    }
    mail($debug_email, "Payment Error", $report);
    die($error_msg);
}

if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
    edie("IPN Mode is not HMAC");
}

if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
    edie("No HMAC Signature Sent.");
}

$request = file_get_contents('php://input');
if ($request === false || empty($request)) {
    edie("Error in reading Post Data");
}

if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($merchant_id)) {
    edie("No or incorrect merchant id.");
}

$hmac =  hash_hmac("sha512", $request, trim($ipn_secret));
if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
    edie("HMAC signature does not match.");
}

$amount1 = floatval($_POST['amount1']); //IN USD
$amount2 = floatval($_POST['amount2']); //IN BTC
$currency1 = $_POST['currency1']; //USD
$currency2 = $_POST['currency2']; //BTC
$status = intval($_POST['status']);

if ($currency2 != $order_currency) {
    edie("Currency Mismatch");
}

if ($amount2 < $order_total) {
    edie("Amount is lesser than order total");
}

if ($status >= 100 || $status == 2) {
    // Payment is complete
    $payment->status = "success";
    $payment->save();
} else if ($status < 0) {
    // Payment Error
    $payment->status = "error";
    $payment->save();
} else {
    // Payment Pending
    $payment->status = "pending";
    $payment->save();
}
die("IPN OK");