<?php

//GET USER IP
if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ip = $_SERVER['REMOTE_ADDR'];
}

session_start();

require_once("../include/bdd.php");

if(isset($_SESSION['id'])){
	if(isset($_POST['object']) && isset($_POST['content'])){
		$object=str_replace(" ", "", $_POST['object']);
		$content=$_POST['content'];
		if(strlen($object) != 0){
			if(strlen($content) >= 10){
				
				$contentEncoded=array();
				$contentList=array(
					"author" => $_SESSION['id'],
					"time" => time(),
					"ip" => $ip,
					"content" => $content
				);
				
				array_push($contentEncoded, $contentList);
				$contentEncoded=json_encode($contentEncoded);
				
				$addTicket=$pdo->prepare("INSERT INTO `tickets`(`userid`, `username`, `objet`, `content`, `date`, `timestamp`) VALUES (?,?,?,?,?,?)");
				if($addTicket->execute(array($_SESSION['id'], $_SESSION['username'], $object, $contentEncoded, date("d-m-Y à H:i:s"), time()))){
					$addLogs=$pdo->prepare("INSERT INTO `logs`(`action`, `userid`, `username`, `date`) VALUES (?,?,?,?)");
					$addLogs->execute(array("createTicket", $_SESSION['id'], $_SESSION['username'], date("d-m-Y à H:i:s")));
					

						$curl = curl_init();
						
						$webhookContent = array(
							"username" => "Tickets",
							"avatar_url" => "https://dev.stressing.cc/favicon.ico",
							"content" => "[@here]",
							"embeds" => array(array(
								"title" => "Un nouveau ticket a été créé",
								"url" => "https://admin.stressing.cc",
								"color" => 15258703,
								"fields" => array(
									array(
										"name" => "Auteur",
										"value" => $_SESSION['username'],
										"inline" => true
									),
									array(
										"name" => "IP",
										"value" => $ip,
										"inline" => true
									),
									array(
										"name" => "Date",
										"value" => date("d/m/Y H\hi"),
										"inline" => true
									),
									array(
										"name" => "Objet du ticket",
										"value" => $object
									),
									array(
										"name" => "Contenu du ticket",
										"value" => $content
									)
								)
							))
						);

						curl_setopt_array($curl, array(
							CURLOPT_URL => "https://discordapp.com/api/webhooks/720618856405663854/7th8-4KmpPpR0lpKOLip786btBFyga83KV_IqesbFc_T0ccuzh7UahxD9wzmjS6xXbvj",
							CURLOPT_RETURNTRANSFER => false,
							CURLOPT_ENCODING => "",
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => "POST",
							CURLOPT_POSTFIELDS => json_encode($webhookContent),
							CURLOPT_HTTPHEADER => array(
								"Content-Type: application/json",
								"Cookie: __cfduid=d26c27d926c486afb632458a0411f785e1591879499; __cfruid=6796af6f4e9290203efadfc4c61b4e17794bf04f-1591879499"
							),
						));

						$response = curl_exec($curl);

						curl_close($curl);

					
					echo "success";
				} else {
					echo "error";
				}				
			} else {
				echo "invalid content";
			}
		} else {
			echo "invalid object";
		}
	} else {
		echo "invalid params";
	}	
} else {
	echo "not login";
}