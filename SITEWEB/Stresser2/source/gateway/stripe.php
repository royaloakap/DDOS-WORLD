<?php

ob_start();
require_once("../includes/db.php");
require_once("../includes/init.php");
require_once("../includes/Stripe/Stripe.php");

Stripe::setApiKey($stripe[$stripe['mode']]['secret']);

header('HTTP/1.1 400 Bad Request');

$failure = true;

fwrite($fh, print_r($GLOBALS, true) . PHP_EOL);
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data)) die ("Good bye!");

$id = $data['id'];
$custom = $data['data']['object']['metadata'];
$event = Stripe_Event::retrieve($id);
if ($event->type != 'charge.succeeded') {
	die();
}

$fh = fopen(time() . "_stripe.log", "a");
fwrite($fh, print_r($data, true));

if (isset($custom['key']) && $custom['key'] == $secret) {
	
	$checkItem = $odb->prepare("SELECT * FROM `plans` WHERE `ID` = :id");
	$checkItem->execute(array(":id" => $custom['item_id']));

	if ($checkItem->rowCount() == 0) {
		fwrite($fh, "[!!] PLAN DOESN'T EXISTS (Plan: " . $custom['item_id'] . ")" . PHP_EOL);
		die();
	}
	
	$planData = $checkItem->fetch(PDO::FETCH_ASSOC);

	if (($data['data']['object']['amount']/100) < $planData['price'])
	{
		fwrite($fh, "[!!] TAMPERED PAYMENT PRICE [!!]");
		die();
	}
	
	fwrite($fh, "[!!] Using plan #" . $custom['item_id'] . PHP_EOL);
	fwrite($fh, "[!!] Plan info: " . print_r($planData, true) . PHP_EOL);
	fwrite($fh, "[!!] Creating transaction row" . PHP_EOL);
	
	$insertPayment = $odb->prepare("INSERT INTO `payments` VALUES (NULL, :paid, :plan, :uid, :email, NULL, 'stripe', :tid, :time)");
	$insertPayment->execute(array(
		":paid" => ($data['data']['object']['amount']/100),
		":plan" => $custom['item_id'],
		":uid" => $custom['u_id'],
		":email" => $custom['email'],
		":tid" => $data['id'],
		":time" => $data['created'],
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

fclose($fh);

if ($failure) {
	header('HTTP/1.1 400 Bad Request');
	echo 400;
} else {
	header('HTTP/1.1 200 OK');
	echo 200;
}

