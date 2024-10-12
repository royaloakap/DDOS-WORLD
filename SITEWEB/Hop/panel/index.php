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


$currentPage = "dashboard";
$pageon = "Dashboard";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);
?>



<?php
if (! isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off' ) {
    $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect_url");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include_once("analyticstracking.php") ?>
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	
        <meta name="description" content="Hop Stresser - Strongest Booter / IP Stresser">
        <meta name="keywords" content="Hop Stresser, Booter, DDoS, Layer7 Booter, Cheap DDoS Booter, Cheap Booter, Cheap Obama Attacks, Booter PayPal, Best Layer7 Booter , Best IP Stresser, best Booter ">
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
						<i class="pe-7f-home"></i>
						<span><?php echo $pageon ?></span>
					</h1>
					<ul class="main-header__breadcrumb">
						<li><a href="#" onclick="return false;"><?php include 'includes/name.php'; ?></a></li>
						<li><a href="#" onclick="return false;"><?php echo $pageon ?></a></li>
						
					</ul>
				</div>
				
				<div class="main-header__date">
					<input type="radio" id="radio_date_1" name="tab-radio" value="today" checked><!--
					--><input type="radio" id="radio_date_2" name="tab-radio" value="yesterday">
					<!--
					--><button>
						<i class="pe-7f-date"></i>
						<span>Expire: <?php echo date('d-m-Y' ,$userInfo['expire']); ?></span>
					</button>
				
					
				</div>
			</header> <!-- /main-header -->

			<div data-tab-radio="tab-radio" class="tab-radio-content row" id="today">
					<div class="main-stats__stat col-md-2 col-sm-2">
												<h3 class="main-stats__title">Welcome</h3>
						<p class="main-stats__resume">Welcome to <?php include 'includes/name.php'; ?>! We hope you enjoy your stay with us. </p>

						
					</div> <!-- /col -->
				  
				  <div class="main-stats__stat col-md-2 col-sm-2 col-xs-4">
						<div class="stat-circle">
							<h3><?php echo $stats -> totalBoots($odb); ?></h3>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92 92">
								<circle style="opacity:0.16;fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="46" cy="46" r="45"/>
								<path style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-miterlimit:10;" d="M84.839,68.718C88.749,62.049,91,54.289,91,46C91,21.147,70.853,1,46,1"/>
							</svg>
						</div> <!-- /stat-circle -->
						<h4 class="main-stats__subtitle">Total<br> Boots<br>
							
						</h4>
					</div> <!-- /col -->
					
					<div class="main-stats__stat col-md-2 col-sm-2 col-xs-4">
						<div class="stat-circle">
							<h3><?php echo $stats -> totalUsers($odb); ?></h3>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92 92">
							<circle style="opacity:0.16;fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="46" cy="46" r="45"/>
							<path style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-miterlimit:10;" d="M6.185,66.968C13.725,81.256,28.721,91,46,91c24.853,0,45-20.147,45-45C91,21.147,70.853,1,46,1"/>
							</svg>
						</div> <!-- /stat-circle -->
						<h4 class="main-stats__subtitle">New<br> members<br>
							
						</h4>

					</div> <!-- /col -->

					<div class="main-stats__stat col-md-2 col-sm-2 col-xs-4">
						<div class="stat-circle">
							<h3><?php echo $stats -> onlineServers($odb); ?> + <?php echo $stats -> onlineServers1($odb); ?> </h3>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92 92">
							<circle style="opacity:0.16;fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="46" cy="46" r="45"/>
							<path style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-miterlimit:10;" d="M6.185,66.968C13.725,81.256,28.721,91,46,91c24.853,0,45-20.147,45-45C91,21.147,70.853,1,46,1"/>
							</svg>
						</div> <!-- /stat-circle -->
						<h4 class="main-stats__subtitle">Online<br> servers<br>
							
						</h4>

					</div> <!-- /col -->
					
					

					<div class="main-stats__stat col-md-2 col-sm-2 col-xs-4">
						<div class="stat-circle">
							<h3><?php echo $stats -> attackMethhods($odb); ?></h3>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92 92">
								<circle style="opacity:0.16;fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="46" cy="46" r="45"/>
								<path style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-miterlimit:10;" d="M91,46C91,21.147,70.853,1,46,1"/>
							</svg>
						</div> <!-- /stat-circle -->
						<h4 class="main-stats__subtitle">Attack<br> Methods<br>
							
						</h4>
					</div> <!-- /col -->
					
						<div class="main-stats__stat col-md-2 col-sm-2 col-xs-4">
						<div class="stat-circle">
							<h3><?php echo $stats -> runningBoots($odb); ?>/<?php echo $maxBootSlots;?></h3>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 92 92">
								<circle style="opacity:0.16;fill:none;stroke:#000000;stroke-width:2;stroke-miterlimit:10;" cx="46" cy="46" r="45"/>
								<path style="fill:none;stroke:#FFFFFF;stroke-width:2;stroke-miterlimit:10;" d="M91,46C91,21.147,70.853,1,46,1"/>
							</svg>
						</div> <!-- /stat-circle -->
						<h4 class="main-stats__subtitle">Running<br> Attacks<br>
							
						</h4>
					</div> <!-- /col -->
					
					


				</div> <!-- row -->

				<div data-tab-radio="tab-radio" class="tab-radio-content row" id="yesterday">
					<div class="main-stats__stat col-md-3 col-sm-3">
						<h3 class="main-stats__title">Resume<br> Attacks from yesterday.</h3>
						<p class="main-stats__resume">Attacks From Yesterday!</p>
						
					</div> 

				</div> <!-- /row -->

				<div class="row">
					<div class="col-md-12">
					<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph1"></i><h3>Attacks In The Past 7 Days</h3>
								</div>
							</header>

							<div id="chartAttacks" style="width: 100%; height: 362px;"></div>
						</article><!-- /widget -->
					</div>
				
				
				
				
					<div class="col-md-7">
<?php
	$fetchNews = $odb->query("
		SELECT `n`.*, `u`.`username`
		FROM `news` AS `n`
		LEFT JOIN `users` AS `u`
		ON `u`.`id` = `n`.`author_id`
		ORDER BY `date` DESC
		LIMIT 3;
	");
	if ($fetchNews->rowCount() != 0)
	{
		while ($row = $fetchNews->fetch(PDO::FETCH_ASSOC))
		{
?>

						<div class="media message tabs">
							<figure class="pull-left rounded-image message__img">
								<img class="media-object" src="img/admin.png" alt="user"  height="48" width="48">
							</figure>
							<div class="media-body">
								<h4 class="media-heading message__heading"><?php echo htmlentities($row['username']); ?>
									<span><?php echo timeElapsedFromUNIX($row['date']); ?></span>
								</h4>
								<p class="message__msg"><?php echo nl2br(htmlentities($row['detail'])); ?></p>
							</div>
						</div>

<?php
		}
?>
	<br />
	<br />
	<br />
						<center>
							<a href="news.php" class="btn blue inverse">View More News &raquo;</a>
						</center>
<?php
	} else {
		echo '<div class="alert alert-error">There is no latest news!</div>';
	}
?>
					
						

					</div> 

					<div class="col-md-5">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7f-power"></i><h3>Server Status</h3>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
										
									  	<thead>
									  		<tr>
									  			<th>Name</th>
									  			<th>Power</th>
									  			<th>Status</th>
									  		</tr>
									  	</thead>
									  	<tbody>
<?php
	$fetchServers = $odb->query("SELECT * FROM servers WHERE `active`='1' ORDER BY `name`");
	if  ($fetchServers->rowCount() != 0)
	{
		while ($row = $fetchServers->fetch(PDO::FETCH_ASSOC))
		{
			echo '
										<tr class="spacer"></tr>
											<tr>
									  			<td class="text-left">
									  				' . htmlentities($row['name']) . '
									  			</td>
									  			<td>' . htmlentities($row['strength']) . '</td>
									  			<td style="font-size:19pt;color:' . ($row['status'] == "good" ? "lime" : ($row['status'] == "caution" ? "orange" : ($row['status'] == "gone" ? "red" : "black"))) .';">
									  				<i class="pe-7s-' . ($row['status'] == "good" ? "check" : ($row['status'] == "caution" ? "attention" : ($row['status'] == "gone" ? "close-circle" : "help1"))) . '"></i>
									  			</td>
									  		</tr>
											';
		}
	}
?>
<?php
	$fetchServers = $odb->query("SELECT * FROM servers_layer4 WHERE `active`='1' ORDER BY `name`");
	if  ($fetchServers->rowCount() != 0)
	{
		while ($row = $fetchServers->fetch(PDO::FETCH_ASSOC))
		{
			echo '
										<tr class="spacer"></tr>
											<tr>
									  			<td class="text-left">
									  				' . htmlentities($row['name']) . '
									  			</td>
									  			<td>' . htmlentities($row['strength']) . '</td>
									  			<td style="font-size:19pt;color:' . ($row['status'] == "good" ? "lime" : ($row['status'] == "caution" ? "orange" : ($row['status'] == "gone" ? "red" : "black"))) .';">
									  				<i class="pe-7s-' . ($row['status'] == "good" ? "check" : ($row['status'] == "caution" ? "attention" : ($row['status'] == "gone" ? "close-circle" : "help1"))) . '"></i>
									  			</td>
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
				

				</div> <!-- /row -->

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="js/main.js?v4"></script>
	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
<script type="text/javascript">
		jQuery(function() {
		AmCharts.ready(function () {
			var chart = new AmCharts.AmSerialChart();
			chart.dataProvider = [
<?php
// very complex query that looks scarrryyyyyy
$z = 1;
$fetchUsers = $odb->query("
	SELECT
		DAY(FROM_UNIXTIME(date)) as `day`, 
		DAYNAME(FROM_UNIXTIME(date)) as `dayname`, 
		COUNT(*) as `boots`
	FROM `logs`
	WHERE `date` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))
	GROUP BY DAY(FROM_UNIXTIME(date)), DAYNAME(FROM_UNIXTIME(date))
	ORDER BY `date` ASC
");
while ($row = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
	echo "{'date':'" . $row['dayname'] . " (" . $row['day'] . ")','boots':'" . $row['boots'] . "'}";
	if ($z <= 7) echo ",";
	$z++;
}
if ($fetchUsers->rowCount() <= 7) {
	for ($i = 0; $i < 7-$fetchUsers->rowCount(); $i++) {
		echo "{'date':'" . date("l (j)", strtotime("-" . $i . " days")) . "','boots':'0'}";
		if ($z <= 7) echo ",";
		$z++;
	}
}
?>
			];
			chart.pathToImages = "https://www.amcharts.com/lib/3/images/";
			chart.categoryField = "date";    
			var categoryAxis = chart.categoryAxis;
			categoryAxis.inside = false;
			categoryAxis.gridAlpha = 0;
			categoryAxis.tickLength = 0;
			categoryAxis.axisAlpha = 0.5;
			categoryAxis.fontSize = 9;
			categoryAxis.axisColor = "rgba(255,255,255,0.8)";
			categoryAxis.color = "rgba(255,255,255,0.8)";
			var valueAxis = new AmCharts.ValueAxis();
			valueAxis.dashLength = 2;
			valueAxis.gridColor = "rgba(255,255,255,0.8)";
			valueAxis.gridAlpha = 0.2;
			valueAxis.axisColor = "rgba(255,255,255,0.8)";
			valueAxis.color = "rgba(255,255,255,0.8)";
			valueAxis.axisAlpha = 0.5;
			valueAxis.fontSize = 9;
			chart.addValueAxis(valueAxis);
			var graph = new AmCharts.AmGraph();
			graph.type = "smoothedLine";
			graph.valueField = "boots";
			graph.lineColor = "#53d769";
			graph.lineThickness = 3;
			graph.bullet = "round";
			//graph.bulletColor = "rgba(0,0,0,0.3)";
			graph.bulletBorderColor = "#53d769";
			graph.bulletBorderAlpha = 1;
			graph.bulletBorderThickness = 3;
			graph.bulletSize = 6;
			chart.addGraph(graph);
			var chartCursor = new AmCharts.ChartCursor();
			chart.addChartCursor(chartCursor);
			chartCursor.categoryBalloonAlpha = 0.2;
			chartCursor.cursorAlpha = 0.2;
			chartCursor.cursorColor = 'rgba(255,255,255,.8)';
			chartCursor.categoryBalloonEnabled = false;
			chart.write("chartAttacks");
		});
		});
	</script>
</body>
</html>


