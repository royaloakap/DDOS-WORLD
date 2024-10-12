<?php

	ob_start(); 
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php'; 

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$subject = $_GET['subject'];
	$content = $_GET['content'];
	
	if (empty($subject) || empty($content)){
		die(error('Fill in all fields'));
	}
	
	if ($user -> safeString($content) || $user -> safeString($subject)){
		die(error('Unsafe characters were set'));
	}
	
	$SQLCount = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Waiting for admin response'")->fetchColumn(0);
	if ($SQLCount > 2){
		die(error('You have too many opened tickets. Please wait them to be responded before you open a new one'));
	}
	
	$SQLinsert = $odb -> prepare("INSERT INTO `tickets` VALUES(NULL, :subject, :content, :status, :username, UNIX_TIMESTAMP())");
	$SQLinsert -> execute(array(':subject' => $subject, ':content' => $content, ':status' => 'Waiting for admin response', ':username' => $_SESSION['username']));
	echo success('Ticket has been created');
	
?>