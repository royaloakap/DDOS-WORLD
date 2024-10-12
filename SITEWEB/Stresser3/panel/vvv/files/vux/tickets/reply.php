<?php

header("X-XSS-Protection: 1; mode=block");

	ob_start();
	require_once '../../../avg/mycon.php';
	require_once '../../../avg/usv.php'; 
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb)) || empty($_GET['message']) || empty($_GET['id']) || !isset($_SERVER['HTTP_REFERER']) || !($user -> isStaff($odb))){
		die(error('There was an error with your session'));
	}

	$updatecontent = $_GET['message'];
	$id = $_GET['id'];
	
	if ($user -> safeString($updatecontent)){
		die(error('Unsafe characters were set'));
	}
	
	$SQLClosed = $odb -> query("SELECT `status` FROM `tickets` WHERE `id` = '$id'");
	if($SQLClosed->fetchColumn(0) == "Closed"){
		die(error('The ticket has been closed'));
	}
	
	$SQLGetMessages = $odb -> query("SELECT * FROM `messages` WHERE `ticketid` = '$id' ORDER BY `messageid` DESC LIMIT 1");
	
	$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender, UNIX_TIMESTAMP())");
	$SQLinsert -> execute(array(':sender' => 'Admin', ':content' => $updatecontent, ':ticketid' => $id));
	
	$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
	$SQLUpdate -> execute(array(':status' => 'Waiting for user response', ':id' => $id));
	die(success('Message has been sent'));
	
?>