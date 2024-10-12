<?php

ob_start(); 
require_once 'includes/app/config.php';
require_once 'includes/app/init.php'; 
header('Content-Type: application/json');


function removeToken()
{
	die();
}

$response = array(
	"success" => false,
	"message" => "An invalid token was specified",
	"code"    => 1,
);

if ($user -> LoggedIn() && $user->hasMembership($odb) && $user -> notBanned($odb)) {
	if (isset($_GET['token']) && !empty($_GET['token']) && is_string($_GET['token'])) {
		$SQLSelect = $odb->query("SELECT * FROM `ping_sessions` WHERE ping_key='{$_GET['token']}' ORDER BY `ID` DESC LIMIT 1");
		while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
			
		$userid = $show['user_id'];
		$key = $show['ping_key'];
        $ip = $show['ping_ip'];
        $pingport = $show['ping_port'];
		}
		if ($userid == $_SESSION['ID']) {
			if ($key == $_GET['token']) {
				// Make sure that:
				//	- attack has not stopped
				//	- boot date and time is greater than current time (in future) to expire
				//	- the token date and user's mbt is greater than current time
				//	- the token hasn't run more than 25 times
			
				
					
						$ip = preg_replace("/(https?:\/\/)/is", "", $ip);
						$ip = trim($ip);
						$ip = trim($ip, "/");
						
						// execute command (scarryyyy stuff)
						//$data = exec("ping -c 1 -i 1 -t 80 " . escapeshellarg($ip), $results);


							$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/includes/ajax/probe.php?action=ping&token=BossStresser&target=" . $ip);
							curl_setopt_array($ch, array(
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_HTTPHEADER     => array(
								),
								CURLOPT_TIMEOUT        => 15,
							));
							$data = json_decode(curl_exec($ch), true); 
							var_dump($data);
							if (!empty($data) && is_array($data)) {
								if (isset($data['success']) && $data['success'] === true) {
									$response['message'] = $data['message'];
									$response['code'] = 0;
									updateToken($_GET['token']); // update token's runs
								}
								else {
									$response['message'] = $data['message'];
									$response['code'] = 5;
								}
							}
							else {
								//throw new Exception("Something fucked up " . curl_error($ch) . " " . print_r($data, true));

								$response['message'] = "Internal server error";
								$response['code'] = 5;
							}



						if (isset($results[0])) {
							$response['success'] = true;
							$response['message'] = $results[0] . (count($results) > 2 ? $results[1] : "");
							$response['code'] = 0;
						} else {
							
							$response['message'] = "An invalid host was used";
							$response['code'] = 4;
						}
					
				} else {
					$response['message'] = "Token has expired";
					$response['code'] = 3;
					removeToken($_GET['token']);
				}
		}
	}
} else {
	$response['message'] = "You must be logged in to send pings";
	$response['code'] = 2;
}

echo json_encode($response, true);
