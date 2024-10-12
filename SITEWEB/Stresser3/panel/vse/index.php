<?php

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Access denied");
	ob_start();

	if (!($user -> hasMembership()) || !($user -> notBanned($odb))){
		header('location: ../dashboard.php');
		exit();
	}
	
?>