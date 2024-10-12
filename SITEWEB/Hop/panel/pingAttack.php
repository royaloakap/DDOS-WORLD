<?php

header('Content-Type: application/json');

ob_start();
require_once 'includes/db.php';
require_once 'includes/init.php';


ini_set("display_errors", "On");
error_reporting(E_ALL);

function fetchToken($token)
{
	global $odb;
	$fetchToken = $odb->prepare("
		SELECT
			`t`.*,
			`l`.`time` AS `boot_time`,
			`l`.`date` AS `boot_date`,
			`l`.`stopped`,
			`l`.`ip`,
			`p`.`mbt` AS `plan_mbt`
		FROM `ping_tokens` AS `t`

		LEFT JOIN `logs` AS `l`
		ON `l`.`ID` = `t`.`attack_id`

		LEFT JOIN `users` AS `u`
		ON `u`.`ID` = `t`.`user_id`

		LEFT JOIN `plans` AS `p`
		ON `p`.`ID` = `u`.`membership`

		WHERE `t`.`token` = :token
	");
	$fetchToken->execute(array(
		":token" => $token,
	));
	if ($fetchToken->rowCount() != 0) {
		return $fetchToken->fetch(PDO::FETCH_ASSOC);
	}
}

function removeToken($token)
{
	global $odb;
	$remove = $odb->prepare("DELETE FROM `ping_tokens` WHERE `token` = :token");
	$remove->execute(array(
		":token" => $token,
	));
}

function updateToken($token)
{
	global $odb;
	$updateToken = $odb->prepare("UPDATE `ping_tokens` SET `runs`=`runs`+1 WHERE `token` = :token");
	$updateToken->execute(array(
		":token" => $token,
	));
}

$response = array(
	"success" => false,
	"message" => "An invalid token was specified",
	"code"    => 1,
);

if ($user -> LoggedIn() && $user->hasMembership($odb) && $user -> notBanned($odb)) {
	if (isset($_GET['token']) && !empty($_GET['token']) && is_string($_GET['token'])) {
		if ( ($token = fetchtoken($_GET['token'])) !== null) {
			if ($token['user_id'] === $_SESSION['ID']) {
				// Make sure that:
				//	- attack has not stopped
				//	- boot date and time is greater than current time (in future) to expire
				//	- the token date and user's mbt is greater than current time
				//	- the token hasn't run more than 25 times
				
				if ($token['stopped'] == "No" && $token['boot_time'] + $token['boot_date'] > time()
					&& $token['date']+$token['plan_mbt'] > time() && $token['runs'] < 26) {
					if (filter_var($token['ip'], FILTER_VALIDATE_IP) || filter_var($token['ip'], FILTER_VALIDATE_URL)) {
						$ip = preg_replace("/(https?:\/\/)/is", "", $token['ip']);
						$ip = trim($ip);
						$ip = trim($ip, "/");
						
						// execute command (scarryyyy stuff)
						curl_exec("ping -c 1 -i 1 -t 80 " . escapeshellarg($ip), $results);
						updateToken($_GET['token']); // update token's runs
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
	}
} else {
	$response['message'] = "You must be logged in to send pings";
	$response['code'] = 2;
}

echo json_encode($response, true);


