<?php
session_start();

require_once("../include/bdd.php");


if(isset($_SESSION['id'])){
	if(isset($_GET['id'])){
		$checkAttack=$pdo->prepare("SELECT * FROM `attaques` WHERE `userid`=? AND `id`=?");
		$checkAttack->execute(array($_SESSION['id'], $_GET['id']));
		if($checkAttack->rowCount() != 0){
			$att=$checkAttack->fetch();
			if($att['statut'] == 0){
				$pid=json_decode($att['content']);
				if(is_object($pid)){
					$pid=get_object_vars($pid)['process'];
					
					$cmd="kill -9 ".$pid;
				
					$getServ=$pdo->prepare("SELECT * FROM `servers` WHERE `id`=?");
					$getServ->execute(array($att['server']));
					$serv=$getServ->fetch();
					
					$ssh=get_object_vars(json_decode($serv['ssh']));
					
					$connection = ssh2_connect($ssh['ip'], $ssh['port']);
					ssh2_auth_password($connection, $ssh['user'], $ssh['pass']);

					$stream = ssh2_exec($connection, $cmd);
					
					stream_set_blocking($stream, true);
					$output = stream_get_contents($stream);
					
					fclose($stream);
					
					$updateServer=$pdo->prepare("UPDATE `servers` SET `active_concurent`=`active_concurent`-1 WHERE `id`=?");
					$updateServer->execute(array($serv['id']));
					
					$updateAttack=$pdo->prepare("UPDATE `attaques` SET `statut`=2 WHERE `id`=?");
					$updateAttack->execute(array($att['id']));
					
					echo "success";
				} else {
					echo "not started";
				}
			} else {
				echo "already stop";
			}
		} else {
			echo "not you";
		}
	} else {
		echo "invalid params";
	}
} else {
	echo "not login";
}
?>