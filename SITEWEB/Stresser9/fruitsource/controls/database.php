<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fruitstr1_bombed');
define('DB_USERNAME', 'fruitstr1_bombed');
define('DB_PASSWORD', 'Maumau123');

$odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
date_default_timezone_set("America/New_York");

@ob_start();
@session_start();


error_reporting(0);
ini_set("display_errors", "Off");


define('DIRECT', TRUE);
function getRealIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
$_SERVER['REMOTE_ADDR'] = getRealIpAddr();
require 'functions.php';
$user = new user;
$stats = new stats;
$url = 'http://fruitstresser.net';

// change this to change the max. slots for booting, must be an int
$maxBootSlots = 4;
$stripe['mode']           = "live"; // test -or- live

$stripe['test']['secret'] = "sk_test_gpKX8YdujDyMw6GYHVKVUDQg ";
$stripe['test']['public'] = "pk_test_0YSyTKqGLkCLA9QaU2UzbvtM ";
$stripe['live']['secret'] = "sk_live_ErCWzqTfrEqsAfWOwp0mljjn ";
$stripe['live']['public'] = "pk_live_Oy5kb9zIMCulNgRnMUOpCrkO ";
?>