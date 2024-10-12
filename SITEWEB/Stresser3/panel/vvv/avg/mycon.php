<?php

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

	define('DB_HOST', 'localhost');
	define('DB_NAME', 'tsmepnorkjlyfdai_yJigpNaiPvgpjvHkiNYUcJyMadMllyGeTDs');
	define('DB_USERNAME', 'tsmepnorkjlyfdai_ovwem');
	define('DB_PASSWORD', '72mcXKC^2ma42@a@GeqHfW*!cq^Cqhs9lSV9yDIAT5pG!bO%vW');
	define('ERROR_MESSAGE', 'db error, pls dm owner bout it');

	try {
		$odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
	} catch( PDOException $Exception ) {
		error_log('ERROR: '.$Exception->getMessage().' - '.$_SERVER['REQUEST_URI'].' at '.date('l jS \of F, Y, h:i:s A')."\n", 3, 'error.log');
		die(ERROR_MESSAGE);
	}

	function error($string){  
		return '<div class="alert alert-danger alert-dismissible bg-danger text-sql border-0 fade show"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>ERROR:</strong> '.$string.'</div>';
	}

	function success($string) {
		return '<div class="alert alert-success alert-dismissible bg-success text-sql border-0 fade show"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>SUCCESS:</strong> '.$string.'</div>';
	}
	
require('cattyie.php');
$aWAF = new aWAF();

$aWAF->useCloudflare();
$aWAF->antiCookieSteal('username');

$aWAF->checkGET();
$aWAF->checkPOST();
$aWAF->checkCOOKIE();

$aWAF->start();
	
?>
