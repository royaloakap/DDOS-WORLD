<?php

	if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
		error_log("No marchenet or secret were set \n", 3, "error_log");
		die;
	}

	require_once("../inc/config.php");
	require_once("../inc/init.php");
	
	set_time_limit(0);

	if ($_SERVER['PHP_AUTH_USER'] != $coinpayments || $_SERVER['PHP_AUTH_PW'] != $ipnSecret) {
		error_log("Marchenet or secret are incorrect \n", 3, "error_log");
		die;
	}

	if ($_POST['status'] < 100 && $_POST['status'] != 2) {
		error_log("Bad status \n", 3, "error_log");
		die;
	}

	if ($_POST['currency1'] != "USD") {
		error_log("Bad currency \n", 3, "error_log");
		die;
	}

	$orderId = $_POST['txn_id'];
	list($planID,$userID) = explode("_",$_POST['custom']);
	
	if (!is_numeric($planID) || !is_numeric($userID)) {
		die;
	}

	$SQL = $odb -> prepare("SELECT COUNT(*) FROM `payments` WHERE `tid` = :orderId");
	$SQL -> execute(array(':orderId' => $orderId));
	$countInvoice = $SQL -> fetchColumn(0);
	
	if ($countInvoice != 0){
		error_log("Invoice exists \n", 3, "error_log");
		die;
	}

	$SQL = $odb -> prepare("SELECT * FROM `plans` WHERE `ID` = :id");
	$SQL -> execute(array(':id' => $planID));
	$plan = $SQL -> fetch();

	if ($_POST['amount1'] != $plan['price']) {
		error_log("Bad amount \n", 3, "error_log");
		die;
	}

	$SQL = $odb -> prepare("INSERT INTO `payments` VALUES(NULL, :price, :planid, :userid, :payer, :transactionid, UNIX_TIMESTAMP())");
	$SQL -> execute(array(':price' => $_POST['amount1'], ':planid' => $planID, ':userid' => $userID, ':payer' => $_POST['email'], ':transactionid' => $orderId));

	$unit = $plan['unit'];
	$length = $plan['length'];
	$newExpire = strtotime("+{$length} {$unit}");
	$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
	$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => (int)$planID, ':id' => (int)$userID));

 ?>