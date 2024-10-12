<?php

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';

	if (!empty($maintaince)){
		die();
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}

	$id = $_GET['id'];

	if(empty($id)){
		echo(error('Ticket ID is empty'));
	}
	
	$SQLFind = $odb -> prepare("SELECT `status` FROM `tickets` WHERE `id` = :id");
	$SQLFind -> execute(array(':id' => $id));
	
	if($SQLFind->fetchColumn(0) == "Closed"){
		die(error('Ticket is already closed'));
	}
	
	$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
	$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
	die(success('Ticket has been closed successfuly'));
	
?>