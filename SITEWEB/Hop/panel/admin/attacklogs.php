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
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);		

$currentPage = "admin_attacks";
$pageon = "Attack Logs";
$attacksPerPage = 250;
$page = 1;
if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']))
	$page = $_GET['page'];	
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
	<header class="top-bar">
		<?php include "../includes/template/header.php"; ?>
	</header> <!-- /top-bar -->


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "../includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7f-wallet"></i>
						<span> <?php echo $pageon ?></span>
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

				<!-- /main-header -->
<!-- /row -->
		
				<div class="row">
 <form action="" method="POST">
 
				
									
					<div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph1"></i><h3>Top 50 Ports Used</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div id="chartPorts" style="width: 100%; height: 362px;"></div>
										
	
									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					<div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph"></i><h3>Attack Methods Used</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div id="chartMethods" style="width: 100%; height: 362px;"></div>
										
	
									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					<div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph2"></i><h3>Attack Servers Used</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div id="chartServers" style="width: 100%; height: 362px;"></div>
										
	
									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					
					<div class="col-md-8">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph3"></i><h3>Attack Servers Used</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
							</div>
						</article><!-- /widget -->
					</div><div class="col-md-4">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph2"></i><h3>Attack Statistics</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-6">
											Total Attacks</div>
											<div class="col-md-6">
												<span class="badge blue">
													<?php
														$statistics = array();
													
													$fetchUsers = $odb->query("SELECT COUNT(*) FROM `logs`");
													$statistics['logs'] = $fetchUsers->fetchColumn(0);
													echo number_format($statistics['logs']);
												?>
												</span>
											</div>
											<div class="col-md-6">
												Average Time</div>
											<div class="col-md-6">
												<span class="badge blue">
													<?php
													
													$fetchUsers = $odb->query("SELECT AVG(`time`) FROM `logs`");
													$statistics['avg'] = $fetchUsers->fetchColumn(0);
													echo number_format($statistics['avg'], 3);
												?>
												</span>
											</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
				
					
					<div class="col-md-12">
						<article class="widget">

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
											<?php
												$pages = round($statistics['logs']/$attacksPerPage);
												$pagination = "<div class='btn-group block'>
													<a" . ($page > 1 ? " href='?page=" . ($page-1) . "'" : "") . " class='btn dark inverse'>&laquo; Previous</a>";
												
												for ($i = 1; $i <= $pages; $i++)
													$pagination .= "<a href=\"?page=" . $i . "\" class='btn " . ($i==$page?"red" : "blue") . " inverse'>" . $i . "</a>";
												
												
												$pagination .= "<a" . ($page < $pages ? " href='?page=" . ($page+1) . "'" : "") . " class='btn dark inverse'>Next &raquo;</a></div>";
												
												echo $pagination;
											?>
										
										
										</div>

									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>			


					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3><?php echo $pageon ?></h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
									  	<thead>
									  		<tr>
                                                    <th>User</th>
                                                    <th>Host</th>
                                                    <th>Port</th>
                                                    <th>Time</th>
													<th>Server</th>
													<th>Method</th>
													<th>Date</th>
													<th>Stopped</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
				$SQLGetLogs = $odb -> prepare("SELECT * FROM `logs` ORDER BY `date` DESC LIMIT :start,:max");
				$SQLGetLogs->bindValue(":start", ($page-1)*$attacksPerPage, PDO::PARAM_INT);
				$SQLGetLogs->bindValue(":max", $attacksPerPage, PDO::PARAM_INT);
				$SQLGetLogs->execute();
				while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
				{
					$user = htmlentities($getInfo['user']);
					$IP = htmlentities($getInfo['ip']);
					$port = htmlentities($getInfo['port']);
					$time = htmlentities($getInfo['time']);
					$method = htmlentities($getInfo['method']);
					$stopped = htmlentities($getInfo['stopped']);
					$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
					$server = (isset($getInfo['server_used']) && !empty($getInfo['server_used']) ? $getInfo['server_used'] : "-");
					echo '<tr class="spacer"></tr><tr><td><center>'.htmlentities($user).'</center></td><td><center>'.htmlentities($IP).'</center></td><td><center>'.htmlentities($port).'</center></td><td><center>'.htmlentities($time).'</center></td><td><center>'.htmlentities($server) . '</center></td><td><center>'.htmlentities($method).'</center></td><td><center>'.htmlentities($date).'</center></td><td><center>'.$stopped.'</center></td></tr>';
				}
					
				?>
                                            </tbody>
										</table>
										




							</div>
						</article><!-- /widget -->
					</div>

				 
					
					<div class="col-md-12">
						<article class="widget">

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
											<?php
											
												
												echo $pagination;
											?>
										
										
										</div>

									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>		

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<script type="text/javascript">
		jQuery(function() {
		AmCharts.ready(function () {
			
			
			var chart1 = AmCharts.makeChart("chartPorts", {
				"type": "pie",
				"theme": "none",
				"dataProvider": [
<?php
$i = 1;
$fetchUsers = $odb->query("
	SELECT `port`, COUNT(*) AS `total`
	FROM `logs`
	GROUP BY `port`
	ORDER BY (`total`) DESC
	LIMIT 50;
");
while ($row = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
	echo "{'title':'" . $row['port'] . "','value':'" . $row['total'] . "'}";
	if ($i < $fetchUsers->rowCount()) echo ",";
	$i++;
}
?>
				],
				"titleField": "title",
				"valueField": "value",
				"labelRadius": 5,
				"color": "rgba(255,255,255,0.5)",
				"radius": "42%",
				"innerRadius": "60%",
				"labelText": "[[title]]"
			});
			
			var chart2 = AmCharts.makeChart("chartMethods", {
				"type": "pie",
				"theme": "none",
				"dataProvider": [
<?php
$i = 1;
$fetchUsers = $odb->query("
	SELECT `method`, COUNT(*) AS `total`
	FROM `logs`
	GROUP BY `method`
");
while ($row = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
	echo "{'title':'" . $row['method'] . "','value':'" . $row['total'] . "'}";
	if ($i < $fetchUsers->rowCount()) echo ",";
	$i++;
}
?>
				],
				"titleField": "title",
				"valueField": "value",
				"labelRadius": 5,
				"color": "rgba(255,255,255,0.5)",
				"radius": "42%",
				"innerRadius": "60%",
				"labelText": "[[title]]"
			});
			
			var chart3 = AmCharts.makeChart("chartServers", {
				"type": "pie",
				"theme": "none",
				"dataProvider": [
<?php
$i = 1;
$fetchUsers = $odb->query("
	SELECT `server_used`, COUNT(*) AS `total`
	FROM `logs`
	GROUP BY `server_used`
");
while ($row = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
	echo "{'title':'" . $row['server_used'] . "','value':'" . $row['total'] . "'}";
	if ($i < $fetchUsers->rowCount()) echo ",";
	$i++;
}
?>
				],
				"titleField": "title",
				"valueField": "value",
				"labelRadius": 5,
				"color": "rgba(255,255,255,0.5)",
				"radius": "42%",
				"innerRadius": "60%",
				"labelText": "[[title]]"
			});
		});
		});
	</script>
</body>
</html>