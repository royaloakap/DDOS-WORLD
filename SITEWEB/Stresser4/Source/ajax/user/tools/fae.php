<?php

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../../inc/config.php';	require_once '../../../inc/init.php';

	if (!empty($maintaince)){
		die();
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}

	if (!($user->hasMembership($odb))){
		die();
	}
	
	if($_GET['action'] == "add"){
		
		if (!(isset($_GET['id'])) || empty($_GET['method'])) {
			die(error('Please fill in the field'));
		}

		if (!filter_var($_GET['id'], FILTER_VALIDATE_IP)) {
			die(error('IP is invalid'));
		}
		$allowedTypes = array('f','e');
		if (!in_array($_GET['method'], $allowedTypes)) {
			die(error('Type is invalid'));
		}

		$SQLinsert = $odb->prepare("INSERT INTO `fe` VALUES(NULL, :userID, :type, :ip)");
		$SQLinsert->execute(array(':userID' => $_SESSION['ID'], ':type' => $_GET['method'], ':ip' => $_GET['id']));
		die(success('IP has been added'));
	}
	elseif($_GET['action'] == "remove"){
		
		if (!(isset($_GET['id']))){
			die(error('Please fill in the field'));
		}
		
		$SQL = $odb->prepare("DELETE FROM `fe` WHERE `ID` = :id AND `userID` = :uid LIMIT 1");
		$SQL->execute(array(
			':id' => $_GET['id'],
			':uid' => $_SESSION['ID']
		));
		echo success('IP has been deleted');
	}
	
?>