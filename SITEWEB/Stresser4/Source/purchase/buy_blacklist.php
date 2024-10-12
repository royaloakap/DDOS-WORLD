<?php

	ob_start();
	session_start();
	require_once("../inc/config.php");
	require_once("../inc/init.php");
	
	if($_GET['type'] == "month" || $_GET['type'] == "lifetime")
	{
		if($_GET['type'] && $_GET['host'] && $user -> LoggedIn()){
		
			if($type == "month")
			{
				$money = 20;
				$bitch = "month";
			}
			
			if($type == "lifetime")
			{
				$money = 200;
				$bitch = "lifetime";
			}

			$host = $_GET['host'];
			
			
			$query = array(
				"cmd" => "_pay",
				"reset" => "1",
				"ipn_url" => "http://". $_SERVER['SERVER_NAME'] ."/panel/gateway/" . $bitch ."_ipn.php",
				"merchant" => $coinpayments,
				"item_name" => 'Game: ' . rand(5994, 19963), 
				"currency" => "USD",
				"amountf" => $money,
				"quantity" => "1",
				"custom" => $host . "_" . $_SESSION['ID'],
				"allow_quantity" => "0",
				"want_shipping" => "0",
				"allow_extra" => "0" 
			);

			$header = "https://www.coinpayments.net/index.php?". http_build_query($query);
			header('Location: ' . $header);
			exit;
		
		}
	} else{
		header('Location: ../home.php');
		exit;
	}

?>