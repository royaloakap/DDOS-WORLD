<?php

session_start();

require_once("../include/bdd.php");

if(isset($_SESSION['id'])){
	if(isset($_POST['name']) && !empty($_POST['name'])){
		$getPlan=$pdo->prepare("SELECT * FROM `plans` WHERE `name`=?");
		$getPlan->execute(array($_POST['name']));
		if($getPlan->rowCount() != 0){
			$plan=$getPlan->fetch();
			
			$getUser=$pdo->prepare("SELECT * FROM `users` WHERE `id`=?");
			$getUser->execute(array($_SESSION['id']));
			$userInfo=$getUser->fetch();
			
			if($plan['maxtime'] > $userInfo['secondes']){
				if($userInfo['points'] >= $plan['price']){
					$updateUser=$pdo->prepare("UPDATE `users` SET `plan`=?,`points`=`points`-?,`endplan`=?,`concurrents`=?,`secondes`=?,`network`=? WHERE `id`=?");
					$end=86400*$plan['duree'];
					$end=$end+time();
					if($updateUser->execute(array($plan['name'], $plan['price'], $end, $plan['concurents'], $plan['maxtime'], $plan['network'], $_SESSION['id']))){
						$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5])");
						echo "success";
					} else {
						echo "error";
					}
				} else {
					echo "no money";
				}
			} else {
				echo "not better";
			}
		} else {
			echo "invalid plan";
		}		
	} else {
		echo "invalid params";
	}	
} else {
	echo "not login";
}