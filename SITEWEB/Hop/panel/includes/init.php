<?php

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
$url = 'https://hopstresser.com';
$bootername = 'HOP Stresser | ';

// change this to change the max. slots for booting, must be an int
$maxBootSlots = 15;
$maxBootCooldown = 10; // in seconds

$coinbase['api'] = "-";
$coinbase['secret'] = "-";

$stripe['mode']           = "live"; // test -or- live

$stripe['test']['secret'] = "-";
$stripe['test']['public'] = "-";
$stripe['live']['secret'] = "-";
$stripe['live']['public'] = "-";


// indicate to use a random server or use all
$useRotations = true;

$booterTypes = array("layer7, layer4");

