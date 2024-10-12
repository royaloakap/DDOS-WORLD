<?php

require_once("../include/bdd.php");

session_start();

if(isset($_SESSION['id'])){
	$getAlive=$pdo->prepare("SELECT * FROM `users` WHERE `id`=?");
	$getAlive->execute(array($_SESSION['id']));
	
	if($getAlive->rowCount() != 0){
		$getOnline=$pdo->prepare("SELECT * FROM `users_online` WHERE `userid`=?");
		$getOnline->execute(array($_SESSION['id']));
		if($getOnline->rowCount() != 0){
			$upRow=$pdo->prepare("UPDATE `users_online` SET `username`=?,`time`=? WHERE `userid`=?");
			$upRow->execute(array($_SESSION['username'], time(), $_SESSION['id']));
		} else {
			$addRow=$pdo->prepare("INSERT INTO `users_online`(`userid`, `username`, `time`) VALUES (?,?,?)");
			$addRow->execute(array($_SESSION['id'], $_SESSION['username'], time()));
		}
		echo "added";
	} else {
		echo "session expire";
	}
} else {
	echo "not login";
}


?>