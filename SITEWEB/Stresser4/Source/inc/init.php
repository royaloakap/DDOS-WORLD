<?php
	@session_start();
	if (($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}

	define('DIRECT', TRUE);
	require 'functions.php';		include('firewall.php');
	$user = new user;
	$stats = new stats;
	
 
	$siteinfo = $odb -> query("SELECT * FROM `settings` LIMIT 1");
	while ($show = $siteinfo -> fetch(PDO::FETCH_ASSOC)){
		$sitename = $show['sitename'];
		$description = $show['description'];
		$maintaince = $show['maintaince'];
		$paypal = $show['paypal'];
		$bitcoin = $show['bitcoin'];
		$tos = $show['tos'];
		$siteurl = $show['url'];
		$rotation = $show['rotation'];
		$system = $show['system'];
		$maxattacks = $show['maxattacks'];
		$key = $show['key'];
		$testboots = $show['testboots'];
		$cloudflare = $show['cloudflare'];
		$cbp = $show['cbp'];
		$skype = $show['skype'];
		$secretKey = $show['secretKey'];
		$coinpayments = $show['coinpayments'];
		$ipnSecret = $show['ipnSecret'];
		$btc_address = $show['btc_address'];
		$theme = $show['theme'];
		$logo = $show['logo'];				$hub_status = $show['hub_status'];		$hub_reason = $show['hub_reason'];				$hub_time = $show['hub_time'];				$hub_rtime = $show['hub_rtime'];
	}
	$tickets = $odb->query("SELECT COUNT(*) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Waiting for user response' ORDER BY `id` DESC")->fetchColumn(0);
	if ($hub_status == 0) {												$hub_x = $hub_rtime + $hub_time;																if($hub_x < time())						{							$SQLinsert = $odb -> prepare("UPDATE `settings` SET `hub_status` = '1', `hub_reason` = '', `hub_time` = '' WHERE 1");							$SQLinsert -> execute(array());											}	}
?>
