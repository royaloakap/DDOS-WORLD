<?php
session_start();

require_once("../include/bdd.php");

//GET USER IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}

if(isset($_SESSION['id'])){
	if(isset($_POST['password'])){
		if(strlen($_POST['password']) >= 8){
			$password=password_hash($_POST['password'], PASSWORD_DEFAULT);
			$update=$pdo->prepare("UPDATE `users` SET `password`=? WHERE `id`=?");
			if($update->execute(array($password, $_SESSION['id']))){
				
				$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
				$content=json_encode(array("ip" => $ip));
				$addLogs->execute(array("changepwd", $_SESSION['id'], $_SESSION['username'], date("d-m-Y à H:i:s"), $content));
				
				session_destroy();
				echo "success";
			} else {
				echo "error";
			}
		} else {
			echo "too short";
		}
	} else {
		echo "invalid params";
	}
} else {
	echo "not login";
}
?>