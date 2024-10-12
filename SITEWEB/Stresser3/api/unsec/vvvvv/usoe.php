<?php

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

	if (($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}

	define('DIRECT', TRUE);
	require_once 'functions.php';
	$user = new user;
	$stats = new stats;
	
 
	$siteinfo = $odb -> query("SELECT * FROM `settings` LIMIT 1");
	while ($show = $siteinfo -> fetch(PDO::FETCH_ASSOC)){
		$sitename = $show['sitename'];
		$description = $show['description'];
		$maintaince = $show['maintaince'];
		$siteurl = $show['url'];
		$bitcoin = $show['bitcoin'];
		$paypal = $show['paypal'];
		$stripe = $show['stripe'];
		$stripeSecretKey = $show['stripeSecretKey'];
		$stripePubKey = $show['stripePubKey'];
		$rotation = $show['rotation'];
		$maxattacks = $show['maxattacks'];
		$key = $show['key'];
		$testboots = $show['testboots'];
		$cloudflare = $show['cloudflare'];
		$cbp = $show['cbp'];
		$secretKey = $show['secretKey'];
		$coinpayments = $show['coinpayments'];
		$ipnSecret = $show['ipnSecret'];
		$google_site = $show['google_site'];
		$google_secret = $show['google_secret'];
		$cooldown = $show['cooldown'];
		$cooldownTime = $show['cooldownTime'];
	}
	
	$smtpsettings = $odb -> query("SELECT * FROM `smtpsettings` LIMIT 1");
	while ($show = $smtpsettings -> fetch(PDO::FETCH_ASSOC)){
		$Shost = $show['host'];
		$SAuth = $show['auth'];
		$Susername = $show['username'];
		$Spassword = $show['password'];
		$Sport = $show['port'];
	}

	$twoauth = $odb -> query("SELECT * FROM `2authsettings` LIMIT 1");
	while ($show = $twoauth -> fetch(PDO::FETCH_ASSOC)){
		$AuthSecret = $show['secret'];
	}
	
?>
