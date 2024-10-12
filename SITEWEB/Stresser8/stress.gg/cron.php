<?php

require_once("include/bdd.php");


//Mettre fin à un plan
$getUsers=$pdo->prepare("SELECT * FROM `users` WHERE `plan`!='Free' AND `endplan`!=0 AND `endplan`<?");
$getUsers->execute(array(time()));
while($user=$getUsers->fetch()){
	$update=$pdo->prepare("UPDATE `users` SET `plan`='free',`endplan`=0 WHERE `id`=?");
	$update->execute(array($user['id']));
	
	$getPlan=$pdo->prepare("SELECT * FROM `plans` WHERE `name`='Free'");
	$getPlan->execute();
	$plan=$getPlan->fetch();
	
	$addLog=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
	$content=json_encode(array("oldplan" => $user['plan']));
	$addLog->execute(array("expireplan",$user['id'],$user['username'],date("d-m-Y à H:i:s"),$content));
	
	//var_dump($plan);
	
	$updateUser=$pdo->prepare("UPDATE `users` SET `plan`=?,`endplan`=0,`concurrents`=?,`secondes`=?,`network`=? WHERE `id`=?");
	$updateUser->execute(array("Free",$plan['concurents'],$plan['maxtime'],"free",$user['id']));
}
//End


//Gestion des paiements BTC
$getPayments=$pdo->prepare("SELECT * FROM `payments` WHERE `croncheck`=0");
$getPayments->execute();

while($pay=$getPayments->fetch()){
	$statut=$pay['status'];
	if($statut == "error"){
		$update=$pdo->prepare("UPDATE `payments` SET `croncheck`=1 WHERE `id`=?");
		$update->execute(array($pay['id']));
	} else if($statut == "success"){
		$update=$pdo->prepare("UPDATE `payments` SET `croncheck`=1 WHERE `id`=?");
		$update->execute(array($pay['id']));
		
		$updateUser=$pdo->prepare("UPDATE `users` SET `points`=`points`+? WHERE `id`=?");
		if($updateUser->execute(array($pay['entered_amount'], $pay['userid']))){
			
			$curl = curl_init();
						
			$webhookContent = array(
				"username" => "Paiement",
				"avatar_url" => "https://dev.stressing.cc/favicon.ico",
				"content" => "[@here]",
				"embeds" => array(array(
					"title" => "Un nouveau paiement a été reçu.",
					"url" => "https://admin.stressing.cc",
					"color" => 15258703,
					"fields" => array(
						array(
							"name" => "ID Client",
							"value" => $pay['userid'],
							"inline" => true
						),
						array(
							"name" => "Date",
							"value" => date("d/m/Y H\hi"),
							"inline" => true
						),
						array(
							"name" => "Montant en €",
							"value" => $pay['entered_amount']
						),
						array(
							"name" => "Montant en ".$pay['to_currency'],
							"value" => $pay['amount']
						)
					)
				))
			);

			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://discordapp.com/api/webhooks/720690812824059915/WeUK9NV5-4kuFODrK-Jea-YN1TQbVZvdDVwku4ws477y-mvtdYje87wwLuq6oFNGQ2K3",
				CURLOPT_RETURNTRANSFER => false,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($webhookContent),
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			
			
			$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`, `content`) VALUES (?,?,?,?,?)");
			$encodedContent=json_encode(array(
				"id" => $pay['id'],
				"amount" => $pay['entered_amount'],
				"curency" => $pay["to_currency"]
			));
			$addLogs->execute(array("btcPayment", $pay['userid'], $pay['id'], date("d-m-Y à H:i:s"), $encodedContent));
		}
	}
}
//End


?>