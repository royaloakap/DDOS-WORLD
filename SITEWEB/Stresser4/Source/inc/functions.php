<?php

	function get_tiny_url($url){
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	
	class user {
		
		function realIP(){
			switch(true){
			  case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
			  case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
			  case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
			  default : return $_SERVER['REMOTE_ADDR'];
			}
		}
	 
		function isAdmin($odb){
			$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			$rank = $SQL -> fetchColumn(0);
			if ($rank == 1){
				return true;
			} else{
				return false;
			}
		}
		
		function isSupporter($odb){
			$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			$rank = $SQL -> fetchColumn(0);
			if ($rank > 0){
				return true;
			} else{
				return false;
			}
		}
		
		function availableuser($odb, $user){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = ?");
			$SQL -> execute(array($user));
			$count = $SQL -> fetchColumn(0);
			if ($count == 1){
				return true;
			} else{
				return false;
			}
		}
		
		function LoggedIn(){
			@session_start();
			if (isset($_SESSION['username'], $_SESSION['ID'])){
				return true;
			} else {
				return false;
			
			}
		} 
		
		function captcha($response, $secret) {
			$url = 'https://www.google.com/recaptcha/api/siteverify';
			$data = ['secret'   => $secret,
					 'response' => $response,
					 'remoteip' => $_SERVER['REMOTE_ADDR']];

			$options = [
				'http' => [
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data) 
				]
			];

			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
			return json_decode($result)->success;
		}
		
		function hasMembership($odb){
			$SQL = $odb -> prepare("SELECT `expire` FROM `users` WHERE `ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			$expire = $SQL -> fetchColumn(0);
			if (time() < $expire){
				return true; 
			} else{
				$SQLupdate = $odb -> prepare("UPDATE `users` SET `membership` = 0 WHERE `ID` = ?");
				$SQLupdate -> execute(array($_SESSION['ID']));
				return false;
			}
		}
		
		function notBanned($odb){
			$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			$result = $SQL -> fetchColumn(0);
			if ($result == 0){
				return true;
			} else{
				session_destroy();
				return false;
			}
		}
		
		function safeString($string){
			$upper_string = strtoupper($string);
			$parameters = array("<SCRIPT", "ALERT(", "<IFRAMW", ".CCS", ".JS", "<META", "<FRAME", "<EMBED", "<XML", "<IMG");
			foreach ($parameters as $parameter){
				if (strpos($upper_string,$parameter) !== false){
					return true;
				}
			}
		}	
	}

	class stats {
		
		function totalUsers($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `users`");
			return $SQL->fetchColumn(0);
		}
		
		function activeUsers($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `users` WHERE `expire` > UNIX_TIMESTAMP()");
			return $SQL->fetchColumn(0);
		}
		
		function referrals($odb, $user){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `referral` = ?");
			$SQL -> execute(array($user));
			return $SQL->fetchColumn(0);
		}
		
		function referralbalance($odb, $user){
			$SQL = $odb -> prepare("SELECT `referralbalance` FROM `users` WHERE `username` = ?");
			$SQL -> execute(array($user));
			return $SQL->fetchColumn(0);
		}
		
		function totalBoots($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `logs`");
			return $SQL->fetchColumn(0);
		}
		
		function runningBoots($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
			return $SQL->fetchColumn(0);
		}
		
		function concurrents($odb){
			$SQL = $odb -> prepare("SELECT `plans`.`concurrents` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			return $SQL->fetchColumn(0);
		}
		
		function mbt($odb){
			$SQL = $odb -> prepare("SELECT `plans`.`mbt` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			return $SQL->fetchColumn(0);
		}
		
		function countRunning($odb, $user){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = ?  AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
			$SQL -> execute(array($user));
			return $SQL->fetchColumn(0);
		}
		
		function totalServers($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `api`");
			return $SQL->fetchColumn(0);
		}
		
		function totalBootsForUser($odb, $user){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = ?");
			$SQL -> execute(array($user));
			return $SQL->fetchColumn(0);
		}
		
		function purchases($odb){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `payments` WHERE `user` = ?");
			$SQL -> execute(array($_SESSION['ID']));
			return $SQL->fetchColumn(0);
		}
		
		function serversonline($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `api`");
			return $SQL->fetchColumn(0);
		}
		
		function tickets($odb){
			$SQL = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = ? AND `status` = 'Waiting for user response' ORDER BY `id` DESC");
			$SQL -> execute(array($_SESSION['username']));
			return $SQL->fetchColumn(0);
		}
		
		function admintickets($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'Waiting for admin response'");
			return $SQL->fetchColumn(0);
		}
		
		function usersforplan($odb, $plan)
		{
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `membership` = ?");
			$SQL -> execute(array($plan));
			return $SQL->fetchColumn(0);
		}
	}
	
?>
