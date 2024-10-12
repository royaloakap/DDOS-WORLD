<?php

/**
 * StressBoss Server Probe
 */

header("Content-Type: application/json");
header("X-XSS-Protection: 1; mode=block");

function safetyFilter($raw) {
	$str = str_replace(array($_SERVER['REMOTE_ADDR'], $_SERVER['SERVER_ADDR']), "255.255.255.255", $raw);
	$str = str_replace(array(gethostbyaddr($_SERVER['REMOTE_ADDR']), gethostbyaddr($_SERVER['SERVER_ADDR'])), "255.255.255.255", $raw);
	return $str;
}

// default response
$response = array(
	"success" => false,
	"message" => "Invalid request given",
);

// valid tokens
$tokens = array(
	"BossStresser",
	"apikey",
	"ApiKey",
	"stresser",
);

$actions = array(

	"ping",      // for ping tool; pings the server
	"portcheck", // does a port check on the server
	"websiteup", // checks if a website is online

	"subdomain", // todo; finds subdomains
	"dnsscan",   // to do some day

);

// check shit
if (isset($_GET['action'], $_GET['token'], $_GET['target'])
	&& !empty($_GET['action']) && !empty($_GET['token']) && !empty($_GET['target'])
) {

	// valid token
	if (!in_array($_GET['token'], $tokens)) {
		$response['message'] = "Invalid token of gratitude specified";
		goto fail;
	}

	// action check
	if (!in_array($_GET['action'], $actions)) {
		$response['message'] = "Invalid action given";
		goto fail;
	}

	$_GET['target'] = preg_replace("/^https?:\/\//is", "", trim($_GET['target']));

	// i might have had a bit too many driknks for the below shit code l0l
	if (!filter_var($_GET['target'], FILTER_VALIDATE_IP) &&
		!filter_var("http://" . $_GET['target'], FILTER_VALIDATE_URL)) {
		$response['message'] = "Invalid target must be an IP address or a domain";
		goto fail;
	}

	if (filter_var($_GET['target'], FILTER_VALIDATE_URL)) {
		if (($url = parse_url($_GET['target'])) !== null) {
			$_GET['target']      = $url['host'];
		} else {
			$response['message'] = "Invalid URL"; 
			goto fail;
		}
	}

	$target = escapeshellarg(escapeshellcmd(trim($_GET['target'])));
	$response['success'] = true;

	switch ($_GET['action']) {

		case "ping":
			$cmd = "ping -c 1 -i 1 -t 100 " . $target;
			exec($cmd, $results);
	
			$response['raw']     = array("results" => $results, "cmd" => $cmd, "target" => $target);
			$response['message'] = safetyFilter($results[0] . (count($results)>2 ? $results[1] : ""));
		break;

		case "portcheck":
			$cmd = "nmap -T3 --host-timeout 4s --min-rate 1000 -PN -p U:1194,T:21,22,25,53,80,110,111,123,143,443,465,993,995,3306,8443,553,554,1080,1900,3128,5000,6515,6588,8000,8008,8080,8081,8088,8090,8118,8880,8909,1723,7080,51840,27000-27030,1500,3005,3101,28960,25565,6881-6889 " . $target . " 2>&1";
			exec($cmd, $results);

			// trim some shit
			
			foreach ($results as $k => $v) {
				if (preg_match("/^(Warning\:|Nmap scan report)/is", $v)) {
					unset($results[$k]);
				}
			}

			if (preg_match("/Nmap done\:/is", implode(PHP_EOL, $results))) {
				array_shift($results);
				array_shift($results);
				array_pop($results);
			}

			$response['raw']     = array("results" => $results, "cmd" => $cmd, "target" => $target);
			$response['message'] = $results;
		break;

		case "websiteup":

			$ch = curl_init($_GET['target']);
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER     => array(
				),
				CURLOPT_TIMEOUT        => 15,
				CURLOPT_CUSTOMREQUEST  => "HEAD",
				CURLOPT_REFERER        => $_GET['target'],
				CURLOPT_USERAGENT      => "Mozilla/5.0 ;Windows NT 6.1; WOW64; AppleWebKit/537.36 ;KHTML, like Gecko; Chrome/39.0.2171.95 Safari/537.36",
			));
			curl_exec($ch);

			$response['message'] = array(
				"code" => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    	        "time" => curl_getinfo($ch, CURLINFO_CONNECT_TIME),
			);

		break;

	}

} else {
	$response['message'] = "A probe action, and a token of gratitude is required!";
}

// i hate these things, but fuck it! :D
fail:
echo json_encode($response);
