<?php
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}

define('DIRECT', TRUE);
require 'functions.php';
$user = new user;
$stats = new stats;


$siteinfo = $odb -> query("SELECT * FROM `settings` LIMIT 1");
while ($show = $siteinfo -> fetch(PDO::FETCH_ASSOC))
{
$sitename = $show['sitename'];
$description = $show['description'];
$maintaince = $show['maintaince'];
$ethereum = $show['ethereum'];
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
$issuerId = $show['issuerId'];
$secretKey = $show['secretKey'];
$coinpayments = $show['coinpayments'];
$ipnSecret = $show['ipnSecret'];
}

?>
