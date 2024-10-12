<?php
session_start();

//GET USER IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}


include("include/bdd.php");

if(!isset($_SESSION['id']) && $p!="auth"){
	header("Location: authentification");
	exit();
}

if(isset($_SESSION['id']) && $p=="auth"){
	header("Location: hub");
	exit();
}

if(isset($_SESSION['id']) && $p!="auth"){
	$getAlive=$pdo->prepare("SELECT * FROM `users` WHERE `id`=?");
	$getAlive->execute(array($_SESSION['id']));
	if($getAlive->rowCount() == 0){
		session_destroy();
		header("Location: authentification");
		exit();
	} else {
		$userInfo=$getAlive->fetch();
		
		if($userInfo['ban'] != 0){
			session_destroy();
			header("Location: authentification");
			exit();
		} else if($userInfo['last-login'] != $_SESSION['lastlogin']){
			session_destroy();
			header("Location: authentification");
			exit();
		}
	}
}
?>