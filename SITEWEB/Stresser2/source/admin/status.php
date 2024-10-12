<?php
ob_start();
require_once '../includes/db.php';
require_once '../includes/init.php';
if (!($user -> LoggedIn()))
{
	header('location: ../login.php');
	die();
}
if (!($user->isAdmin($odb)))
{
	header('unset.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: ../login.php');
	die();
}

$currentPage = "admin_server";
$pageon = "Server Management";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);		
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?><?php echo $pageon ?></title>
	
	<link rel="icon" sizes="192x192" href="../img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="../img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="../img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="../img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="../img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="../img/web-hosting.ico" />

	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/main.min.css">
</head>
<body>


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "../includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7s-server"></i>
						<span> <?php echo $pageon ?></span>
						<span style='color:#f00;'> (Danger Zone)</span>
					</h1>
					<ul class="main-header__breadcrumb">
						<li><a href="#" onclick="return false;"><?php include '../includes/name.php'; ?></a></li>
						<li><a href="#" onclick="return false;"><?php echo $pageon ?></a></li>
						
					</ul>
				</div>
				
				<div class="main-header__date">
					<input type="radio" id="radio_date_1" name="tab-radio" value="today" checked><!--
					--><input type="radio" id="radio_date_2" name="tab-radio" value="yesterday"><!--
					--><button>
						<i class="pe-7f-date"></i>
						<span><?php echo date('m-d-Y' ,$userInfo['expire']); ?></span>
					</button>
				</div>
			</header>
<?php


if (isset($_POST['save'])) {

	$resetServers = $odb->query("UPDATE `servers` SET `active`='0'");
	$resetMethods = $odb->query("UPDATE `boot_methods` SET `active`='0'");
	

	if (isset($_POST['methods']) && !empty($_POST['methods']) && is_array($_POST['methods'])) {
		$updateMethod = str_repeat("?,", count($_POST['methods'])-1) . "?";
		$updateMethods = $odb->prepare("UPDATE `boot_methods` SET `active`='1' WHERE `method` IN (" . $updateMethod . ")");
		$updateMethods->execute($_POST['methods']);
	}
	

	if (isset($_POST['servers']) && !empty($_POST['servers']) && is_array($_POST['servers'])) {
		$updateServer = str_repeat("?,", count($_POST['servers'])-1) . "?";
		$updateServers = $odb->prepare("UPDATE `servers` SET `active`='1' WHERE `id` IN (" . $updateServer . ")");
		$updateServers->execute($_POST['servers']);
	}
}


else if (isset($_POST['stopBoots'])) {
	$SQLSelectRunningAttack = $odb -> query("
		SELECT *
		FROM `logs`
		WHERE
			`time` + `date` > UNIX_TIMESTAMP() AND
			`stopped` = 0
		ORDER BY `ID` DESC
	");
	while ($row = $SQLSelectRunningAttack -> fetch(PDO::FETCH_ASSOC))
	{
		$server = $row['server_used'];
	
		if (isset($row['stopped']) && $row['stopped'] == "No") {
			$servers = array();
			$name = "*";
			
			if ($server != "*") {
				$getServers = $odb->prepare("SELECT * FROM `servers` WHERE `name`=:name LIMIT 1;");
				$getServers->execute(array(":name" => $server));
				if ($getServers->rowCount() != 0) {
					$servers = $getServers->fetchAll(PDO::FETCH_ASSOC);
					$name = $servers[0]['name'];
				}
			}
			if ($server == "*" || empty($servers)) {
				$getServers = $odb->query("SELECT * FROM `servers` ORDER BY ABS(`last_used`) ASC;");
				if ($getServers->rowCount() != 0) $servers = $getServers->fetchAll(PDO::FETCH_ASSOC);										
			}
			
			$addr = "";
			
			foreach ($servers as $k => $server)
			{
				$addr = str_replace("%host%", $row['ip'], $server['addr'] . $server['resource']);
				$addr = str_replace("%time%", 1, $addr);
				$addr = str_replace("%port%", 1, $addr);
				$addr = str_replace("%method%", "stop", $addr);
				@file_get_contents($addr);
				
				$addr = null;

			}
		}

		$updateSQL = $odb -> prepare("UPDATE `logs` SET `stopped` = :stopped WHERE `user` = :username AND `time` + `date` > UNIX_TIMESTAMP() AND `ID` = :id");
		$updateSQL -> execute(array(':username' => $_SESSION['username'], ':id' => $row['ID'], ':stopped' => "Yes"));
		
		echo "<div class='alert alert-success'>All attacks (should) have been stopped?</div>";
	}
}

else if (isset($_POST['stopServers'])) {
	$resetServers = $odb->query("UPDATE `servers` SET `active`='0'");
}

else if (isset($_POST['runScript'])) {
	var_dump( exec("php /home/applejuice/server_crons.php") );
}

?>
	<form method="POST" action="">
				<div class="row">
					

					<div class="col-md-8">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style='width:100%;'>
									<i class="pe-7s-server"></i><h3>Server Uptime (Next run: <?php echo round( (strtotime("+3min")-time())/60, 2); ?> min)</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">

				
										<table class="table table-hover media-table">
									  	<thead>
									  		<tr>
									  			<th>Names</th>
									  			<th>Power</th>
									  			<th>Status</th>
									  			<th>Delay (MS)</th>
									  			<th>Active</th>
									  		</tr>
									  	</thead>
									  	<tbody>
<?php
	$fetchServers = $odb->query("SELECT * FROM `servers` ORDER BY `name`");
	if  ($fetchServers->rowCount() != 0)
	{
		while ($row = $fetchServers->fetch(PDO::FETCH_ASSOC))
		{
			echo '	<tr class="spacer"></tr>
											<tr>
									  			<td class="text-left">
									  				' . htmlentities($row['name']) . '
									  			</td>
									  			<td>' . htmlentities($row['strength']) . '</td>
									  			<td style="font-size:19pt;color:' . ($row['status'] == "good" ? "lime" : ($row['status'] == "caution" ? "orange" : ($row['status'] == "gone" ? "red" : "black"))) .';">
									  				<i class="pe-7s-' . ($row['status'] == "good" ? "check" : ($row['status'] == "caution" ? "attention" : ($row['status'] == "gone" ? "close-circle" : "help1"))) . '"></i>
									  			</td>
												<td>' . $row['delay'] . '</td>
												<td><input type="checkbox" name="servers[]" id="server-' . $row['id'] . '" class="sw" value="' . $row['id'] . '"' . ($row['active'] == "1" ? "checked='checked'" : "") . ' />
													<label class="switch green" for="server-' . $row['id'] . '"></label> </td>
									  		</tr>
											';
		}
	}
?>
									  	
									  	
									  	

									  	</tbody>
										</table>
								

							</div>
						</article><!-- /widget -->
					</div>
					
					<div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style='width:100%;'>
									<i class="pe-7s-mouse"></i><h3>Mass Utilities</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
						<center>
								<button name="stopBoots" class="btn btn-lg red inverse">Stop All Boots</button><br /><br />
								<button name="stopServers" class="btn btn-lg red inverse">Stop All Servers</button><br /><br />
								<button name="runScript" class="btn btn-lg blue inverse">Run Ping Check</button>
								</center>
										

							</div>
						</article><!-- /widget -->
					</div>
					
					
					<div class="col-md-8">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style='width:100%;'>
									<i class="pe-7s-config"></i><h3>Method Management</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">

				
										<div class="row">
											<?php
												$getMethods = $odb->query("SELECT * FROM `boot_methods`");
												if ($getMethods->rowCount() != 0) {
													while ($method = $getMethods->fetch(PDO::FETCH_ASSOC)) {
														echo '
													<div class="col-md-4 row" style="margin:0px 0px 2px 0px">
														<div class="col-md-6" style="padding:14px 0px;text-align:right;">' . $method['friendly_name'] . '</div>
														<div class="col-md-6">
															<input type="checkbox" name="methods[]" value="' . $method['method'] . '" id="mthd-' . $method['method'] . '" class="sw" ' . ($method['active'] == '1' ? ' checked="checked"' : "") . '/>
															<label class="switch green" for="mthd-' . $method['method'] . '"></label>
														</div>
													</div>';
													}
												}
											?>
										</div>
								

							</div>
						</article><!-- /widget -->
					</div>
					
						<div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style='width:100%;'>
									<i class="pe-7s-mouse"></i><h3>Actions</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
						<center>
								<button name="save" type="submit" class="btn btn-lg red inverse">Save Changes</button><br /><br /><br />
								<a href="index.php" class="btn btn-lg blue inverse">Discard Changes</a>
								</center>
										

							</div>
						</article><!-- /widget -->
					</div>
					
					
				</div>

				 </form>
					

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<script type="text/javascript" src="../js/chart.js"></script>
</body>
</html>