<?php

 

function gen_domain($domain, $port) {
    return $domain . (isset($port) && $port != 80 ? ":" . $port : "");
}

function is_valid_domain($domain) {
	return (
		filter_var($domain, FILTER_VALIDATE_URL) ||
		filter_var("http://" . $domain, FILTER_VALIDATE_URL) ||
		filter_var("http://" . $domain, FILTER_VALIDATE_IP) ||
		filter_var($domain, FILTER_VALIDATE_IP)
	);
}

function sendHTTPHead($domain, $headers) {
	$ch = curl_init($domain);
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER     => array(
		),
		CURLOPT_TIMEOUT        => 15,
		CURLOPT_CUSTOMREQUEST  => "HEAD",
		CURLOPT_REFERER        => "http://vexsolutions.pw/",
		CURLOPT_USERAGENT      => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :
									"Linux Solutions/1.1"),
	));
	curl_exec($ch);
	return $ch;
}

function get_response($domain, $port) {    
    if ( is_valid_domain($domain) ) {
		$x = sendHTTPHead("http://" . $domain, array());
        $data = array
        (
            "code" => curl_getinfo($x, CURLINFO_HTTP_CODE),
            "time" => curl_getinfo($x, CURLINFO_CONNECT_TIME),
            "valid" => true
        );
    }
    else
    {
        $data = array
        (
            "code" => null,
            "time" => 0,
            "valid" => false
        );
    }
    
    return $data;
}

function fetchID($data) {
    $goodHTTPCodes = array(200, 301, 302, 303, 304, 307, 400, 401, 403, 405);
    return (
		($data['valid'] === false ? 3 : (
						is_numeric($data['code']) && in_array($data['code'], $goodHTTPCodes) ? 1 : 2
			)
		)
	);
}

function gen_html($id, $domain, $port, $time, $code) {
    $units = ($time == 1 ? "second" : ($time > 1 ? "seconds" : "ms"));
	
	$time = ($time < 1 ? $time*1000 : round($time, 2));
    if ($time <= 0 )
		$time = "< 1";

    if ( $id == 1 )
    {
        $html  = "<div class='alert alert-success'>
				Request was sent to <a href=\"http://" . gen_domain($domain, $port) . "\">" . $domain . "</a> and it took " . $time . " " . $units . " to receive a
				" . $code . " response code to " . $domain . (filter_var("http://" . $domain, FILTER_VALIDATE_URL) ? " (" . gethostbyname($domain) . ")" : "") . "</div>";
    }
    else if ( $id == 2 )
    {
        $html = "<div class='alert alert-warning'>
			A request was sent to <a href=\"http://" . gen_domain($domain, $port) . "\">" . $domain . "</a>" .
			(!empty($code) && is_numeric($code) ? " and we received a " . $code . " response code," : " and") . " it appears to be down.</div>";
    }
    else if ( $id == 3 )
    {
        $html  = "<div class='alert alert-danger'>You specified an invalid domain or ip address</div>";
    }
	
    return $html;
}

function filter_domain($domain)
{
	$domain = preg_replace("/[^A-Za-z0-9-\/\.\:]/", "", trim($domain));
	// Split the variable into two, $domain & $port.
	$result = explode(":", $domain);
	// If the port is not numeric or not set we use port 80.
	if (!isset($result[1]) || !is_numeric($result[1]))
	{
	$result[1] = 80;
	}
	return $result;
}

ob_start();
require_once 'includes/db.php';
require_once 'includes/init.php';


if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user->hasMembership($odb)))
{
	header('location: purchase.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}

$currentPage = "tools";
$pageon = "Status Checker";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);

if (isset($_POST['checkStatus'], $_POST['address']) && !empty($_POST['address'])) {
	$addr = preg_replace("/^https?:\/\//is", "", $_POST['address']);
	$addr = rtrim($addr, "/");
	
	list($domain, $port) = filter_domain($addr);
	
	$data = get_response($domain, $port);
	
	$time = round($data["time"], 3);
	$id = fetchID($data);
	$html = gen_html($id, $domain, $port, $time, $data["code"]);
}						
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?><?php echo $pageon ?></title>
	
	<link rel="icon" sizes="192x192" href="img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="img/web-hosting.ico" />

	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.min.css">
	
	<script type="text/javascript" src="js/main.js"></script>
</head>
<body>

	<header class="top-bar">
			<?php include "includes/template/header.php"; ?>
			
	</header> <!-- /top-bar -->


	<div class="wrapper">

		<aside class="sidebar">
	<?php include "includes/template/sidebar.php"; ?>
			
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7f-gleam"></i>
						<span><?php echo $pageon ?></span>
					</h1>
					<ul class="main-header__breadcrumb">
						<li><a href="#" onclick="return false;"><?php include 'includes/name.php'; ?></a></li>
						<li><a href="#" onclick="return false;"><?php echo $pageon ?></a></li>
						
					</ul>
				</div>
				
				<div class="main-header__date">
					<input type="radio" id="radio_date_1" name="tab-radio" value="today" checked><!--
					--><input type="radio" id="radio_date_2" name="tab-radio" value="yesterday"><!--
					--><button>
						<i class="pe-7f-date"></i>
						<span>Expire: <?php echo date('d-m-Y' ,$userInfo['expire']); ?></span>
					</button>
				</div>
			</header> 

<?php
	echo (isset($html) && !empty($html) ? $html : "");
?>
			
					<div class="col-md-6 col-md-offset-3">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-server"></i><h3>Is It Up?</h3>
								</div>
							</header>

							<div class="widget__content">
							<form action="" method="POST">
								<input name="address" type="text" placeholder="http://example.com/"/>
								<button type="submit" name="checkStatus">Is it up?</button>
							</form>
						</div>
					</div>

		</section> <!-- /content -->

	</div>


	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
	
	<script type="text/javascript" src="js/countdown/jquery.plugin.js"></script>
	<script type="text/javascript" src="js/countdown/jquery.countdown.js"></script>
</body>
</html>