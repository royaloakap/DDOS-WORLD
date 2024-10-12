<?php

	ob_start();
	session_start();
	require_once("../inc/config.php");
	require_once("../inc/init.php");
	
	if(isset($_GET['id']) && Is_Numeric($_GET['id']) && $user -> LoggedIn()){
	
		$id = (int)$_GET['id'];
		$row = $odb -> query("SELECT * FROM `plans` WHERE `ID` = '$id'") -> fetch();

		$query = array(
			"cmd" => "_pay",
			"reset" => "1",
			"ipn_url" => "http://". $_SERVER['SERVER_NAME'] ."/panel/gateway/plan_ipn.php",
			"merchant" => $coinpayments,
			"item_name" => 'Game: ' . rand(5994, 19963), 
			"currency" => "USD",
			"amountf" => $row['price'],
			"quantity" => "1",
			"custom" => $id . "_" . $_SESSION['ID'],
			"allow_quantity" => "0",
			"want_shipping" => "0",
			"allow_extra" => "0" 
		);

		$header = "https://www.coinpayments.net/index.php?". http_build_query($query);
		header('Location: ' . $header);
		exit;
	
	}
	else{
		header('Location: home.php');
		exit;
	}

?>