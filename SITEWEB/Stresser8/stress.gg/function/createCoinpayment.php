<?php

session_start();

require_once("../include/bdd.php");
require_once("../coinpayment/init.php");


if(isset($_SESSION['id'])){
	if(isset($_POST['amount']) && isset($_POST['crypto'])){
		$amount=intval($_POST['amount']);
		if($amount >= 5 && $amount !=0){
			$username = "Stressing.cc";

			//$email = $_POST['email'];

			$scurrency = "EUR";
			$rcurrency = $_POST['crypto'];

			$request = [
				'amount' => $amount,
				'currency1' => $scurrency,
				'currency2' => $rcurrency,
				'buyer_email' => '',
				'buyer_name' => $_SESSION['username']." (".$_SESSION['id'].")",
				'item' => "Achat de ".$amount." Coins",
				'address' => "",
				'ipn_url' => "https://dev.stressing.cc/coinpayment/webhook.php"
			];

			$result = $coin->CreateTransaction($request);
			if ($result['error'] == "ok") {
				$payment = new Payment;
				$payment->userid = $_SESSION['id'];
				$payment->entered_amount = $amount;
				$payment->amount = $result['result']['amount'];
				$payment->from_currency = $scurrency;
				$payment->to_currency = $rcurrency;
				$payment->status = "initialized";
				$payment->gateway_id = $result['result']['txn_id'];
				$payment->gateway_url = $result['result']['status_url'];
				$payment->save();
				echo json_encode($result);
			} else {
				//print 'Error: ' . $result['error'] . "\n";
				echo "error";
				die();
			}
			
		}
	} else {
		echo "invalid params";
	}
} else {
	echo "not login";
}


?>