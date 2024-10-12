<?php

ini_set("display_errors", "on");
error_reporting(E_ALL);

ob_start();
require_once("../includes/db.php");
require_once("../includes/init.php");

header('HTTP/1.1 400 Bad Request');

$failure = true;
$fh = fopen(time() . "_BTC.log", "a");

fwrite($fh, print_r($GLOBALS,true) . PHP_EOL);

$data = json_decode(file_get_contents("php://input"), true);
$custom = json_decode($data['order']['custom'], true);
fwrite($fh, print_r($data, true));
fwrite($fh, print_r($custom, true));

if (isset($custom['key']) && $custom['key'] == $secret) {
	
	$checkItem = $odb->prepare("SELECT * FROM `plans` WHERE `ID` = :id");
	$checkItem->execute(array(":id" => $custom['item_id']));

	if ($checkItem->rowCount() == 0) {
		fwrite($fh, "[!!] PLAN DOESN'T EXISTS (Plan: " . $custom['item_id'] . ")" . PHP_EOL);
		die();
	}

	$planData = $checkItem->fetch(PDO::FETCH_ASSOC);
	fwrite($fh, "[!!] Using plan #" . $custom['item_id'] . PHP_EOL);
	fwrite($fh, "[!!] Plan info: " . print_r($planData, true) . PHP_EOL);
	fwrite($fh, "[!!] Creating transaction row" . PHP_EOL);
	
	$insertPayment = $odb->prepare("INSERT INTO `payments` VALUES (NULL, :paid, :plan, :uid, NULL, :addr, 'btc', :tid, :time)");
	$insertPayment->execute(array(
		":paid" => ($data['order']['total_native']['cents']/100),
		":plan" => $custom['item_id'],
		":uid" => $custom['u_id'],
		":addr" => $data['order']['receive_address'],
		//":email" => $_POST['custom']['email'],
		":tid" => $data['order']['id'],
		":time" => strtotime($data['order']['created_at']),
	));
	
	if ($insertPayment->rowCount() != 0) {
		$id = $odb->lastInsertId();
		fwrite($fh, "[!!] Transaction should have been stored as ID:" . $id . PHP_EOL);
	} else {
		fwrite($fh, "[!!] Failed to insert transaction! ABORT BEFORE IT EXPLODES!!!" . PHP_EOL);
		die();
	}
	fwrite($fh, "[!!] Attempting to update user ID: " . $custom['u_id'] . PHP_EOL);
	
	$newExpire = strtotime("+" . $planData['length'] . " " . $planData['unit']);
	$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
	$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => (int)$planData['ID'], ':id' => (int)$custom['u_id']));
	
	if ($updateSQL->rowCount() != 0) {
		fwrite($fh, "[*] Account should have been updated!" . PHP_EOL);
		$failure = false;
	} else {
		fwrite($fh, "[!] Failed to update account" . PHP_EOL);
	}
} else {
	fwrite($fh, "[!!] KEY DOESN'T MATCH [!!]" . PHP_EOL);
}

$id = $data['order']['id'];
//$amount = $data['order']['total_btc']

fclose($fh);

if ($failure) {
	header('HTTP/1.1 400 Bad Request');
	echo 400;
} else {
	header('HTTP/1.1 200 OK');
	echo 200;
}

