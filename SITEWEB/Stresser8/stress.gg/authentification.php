<?php
$p="auth";
require_once("require.php");

if(isset($_SESSION['id'])){
	header("Location: /hub");
	exit();
}

if(isset($_POST['username']) && !isset($_POST['action'])){
	$getUser=$pdo->prepare("SELECT `id` FROM `users` WHERE `username`=?");
	$getUser->execute(array(str_replace(" ", "", $_POST['username'])));
	if($getUser->rowCount() != 0){
		echo "yes";
	} else {
		echo "nope";
	}
} else if(isset($_POST['action']) && isset($_POST['username']) && isset($_POST['password'])){
	if($_POST['action'] == "login"){
		if(!empty(str_replace(" ", "", $_POST['username'])) && str_replace(" ", "", $_POST['username']) != "" && !empty($_POST['password'])){
			$getUser=$pdo->prepare("SELECT * FROM `users` WHERE `username`=?");
			$getUser->execute(array(str_replace(" ", "", $_POST['username'])));
			$userInfo=$getUser->fetch();
			if($getUser->rowCount() != 0 && password_verify($_POST['password'], $userInfo['password'])){
				if($userInfo['ban'] == "0") {
					$_SESSION['id']=$userInfo['id'];
					$_SESSION['lastlogin']=date("d-m-Y à H:i:s");
					$_SESSION['username']=str_replace(" ", "", $userInfo['username']);
					
					$updateUser=$pdo->prepare("UPDATE `users` SET `last-login`=?, `last-ip`=? WHERE `id`=?");
					$updateUser->execute(array(date("d-m-Y à H:i:s"), $ip, $userInfo['id']));
					
					$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
					$content=array("ip" => $ip);
					$content=json_encode($content);
					$addLogs->execute(array("login",$userInfo['id'],$userInfo['username'],date("d-m-Y à H:i:s"),$content));
					
					echo "success";
				} else {
					if($userInfo['ban'] == "1"){
						echo "suspend";
					} else {
						echo "ban";
					}
				}
			} else {
				echo "invalid";
			}
		} else {
			echo "nope";
		}
	} else if($_POST['action'] == "register"){
		$password=password_hash($_POST['password'], PASSWORD_DEFAULT);
		$dupli=$pdo->prepare("SELECT `last-ip` FROM `users` WHERE `last-ip`=? OR `reg-ip`=?");
		$dupli->execute(array($ip,$ip));
		if($dupli->rowCount() == 0){
			$dupliPseudo=$pdo->prepare("SELECT * FROM `users` WHERE `username`=?");
			$dupliPseudo->execute(array(str_replace(" ", "", $_POST['username'])));
			if($dupliPseudo->rowCount() == 0){
				$inscription=$pdo->prepare("INSERT INTO `users`(`username`, `password`, `last-login`, `last-ip`, `reg-date`, `reg-ip`) VALUES (?,?,?,?,?,?)");
				if($inscription->execute(array(str_replace(" ", "", $_POST['username']), $password, date("d-m-Y à H:i:s"), $ip, date("d-m-Y à H:i:s"), $ip))){
					$getInfo=$pdo->prepare("SELECT * FROM `users` WHERE `username`=?");
					$getInfo->execute(array(str_replace(" ", "", $_POST['username'])));
					$userInfo=$getInfo->fetch();
					if($getInfo->rowCount() != 0){
						$_SESSION['id']=$userInfo['id'];
						$_SESSION['username']=str_replace(" ", "", $userInfo['username']);
						
						$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
						$content=array("ip" => $ip);
						$content=json_encode($content);
						$addLogs->execute(array("register",$userInfo['id'],str_replace(" ", "", $userInfo['username']),date("d-m-Y à H:i:s"),$content));
						
						echo "success";
					}
				} else {
					echo "erreur";
				}
			} else {
				echo "pseudo";
			}
		} else {
			echo "dupli";
		}
	} else {
		echo "nope";
	}
} else {
	echo "err";
}


?>