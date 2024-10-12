<?php

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

    define('DB_HOST', 'localhost');
	define('DB_NAME', 'tsmepnorkjlyfdai_yJigpNaiPvgpjvHkiNYUcJyMadMllyGeTDs');
	define('DB_USERNAME', 'tsmepnorkjlyfdai_ovwem');
	define('DB_PASSWORD', '72mcXKC^2ma42@a@GeqHfW*!cq^Cqhs9lSV9yDIAT5pG!bO%vW');
	define('ERROR_MESSAGE', 'db error, pls dm owner bout it');

    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    
    header('Content-Type: application/json; charset=utf-8');
    

	try {

		$odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);

	} catch( PDOException $Exception ) {

		error_log('ERROR: '.$Exception->getMessage().' - '.$_SERVER['REQUEST_URI'].' at '.date('l jS \of F, Y, h:i:s A')."\n", 3, 'error.log');

		die(ERROR_MESSAGE);

	}

    function errorJson($message)
    {
        $contents = '{"Message":"'.$message.'","Status":"Error","ResponseStatus":200}';
        return $contents;
    }
    
    function successJson($message)
    {
        $contents = '{"Message":"'.$message.'","Status":"Success","ResponseStatus":200}';
        return $contents;
    }


	function error($string){  

		return '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>ERROR:</strong> '.$string.'</div>';

	}



	function success($string) {

		return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>SUCCESS:</strong> '.$string.'</div>';

	}
	
	 require('gomue.php');
$aWAF = new aWAF();

$aWAF->useCloudflare();
$aWAF->antiCookieSteal('username');

$aWAF->checkGET();
$aWAF->checkPOST();
$aWAF->checkCOOKIE();

$aWAF->start();

 
?>