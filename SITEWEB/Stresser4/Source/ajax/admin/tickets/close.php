<?php
	
	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb)) || empty($_GET['id']) || !isset($_SERVER['HTTP_REFERER']) || !($user -> isSupporter($odb))){
		die(error('There was an error with your session'));
	}

	$id = $_GET['id'];
	
	$SQLFind = $odb -> prepare("SELECT `status` FROM `tickets` WHERE `id` = :id");
	$SQLFind -> execute(array(':id' => $id));
	
	if($SQLFind->fetchColumn(0) == "Closed"){
		die(error('Ticket is already closed'));
	}
	
	$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
	$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
	die(success('Ticket has been closed successfuly'));
	
?>