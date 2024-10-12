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


$currentPage = "admin_payments";
$pageon = "Payments";

$transPerPage = 15;	
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


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "../includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7f-wallet"></i>
						<span><?php echo $pageon ?></span>
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
<?php

	   if (isset($_POST['search']))
	   {
			if (isset($_POST['username'], $_POST['email'], $_POST['transaction']) &&
				!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['transaction']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `username` LIKE :user OR `email` LIKE :email OR `btc_addr` LIKE :email OR `tid` LIKE :tid");
				$query->execute(array(
					":user" => "%" . $_POST['username'] . "%",
					":email" =>"%" .  $_POST['email'] . "%",
					":tid" =>"%" .  $_POST['transaction'] . "%",
				));
			} else if (isset($_POST['username'], $_POST['email']) &&
				!empty($_POST['username']) && !empty($_POST['email']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `username` LIKE :user OR `email` LIKE :email OR `btc_addr` LIKE :email");
				$query->execute(array(
					":user" => "%" . $_POST['username'] . "%",
					":email" =>"%" .  $_POST['email'] . "%",
				));
			} else if (isset($_POST['username'], $_POST['transaction']) &&
				!empty($_POST['username']) && !empty($_POST['transaction']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `username` LIKE :user OR `tid` LIKE :tid");
				$query->execute(array(
					":user" => "%" . $_POST['username'] . "%",
					":tid" => "%" . $_POST['transaction'] . "%",
				));
			} else if (isset($_POST['email'], $_POST['transaction']) &&
				!empty($_POST['email']) && !empty($_POST['transaction']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `email` LIKE :email OR `btc_addr` LIKE :email OR `tid` LIKE :tid");
				$query->execute(array(
					":email" =>"%" .  $_POST['email'] . "%",
					":tid" => "%" . $_POST['transaction'] . "%",
				));
			} else if (isset($_POST['username']) &&
				!empty($_POST['username']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `username` LIKE :user");
				$query->execute(array(
					":user" => "%" . $_POST['username'] . "%",
				));
			} else if (isset($_POST['transaction']) && !empty($_POST['transaction']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `tid` LIKE :tid");
				$query->execute(array(
					":tid" => "%" . $_POST['transaction'] . "%",
				));
			} else if (isset($_POST['email']) && !empty($_POST['email']))
			{
				$query = $odb->prepare("SELECT *,`u`.`username`,`p`.`name` FROM `payments` LEFT JOIN `users` AS `u` ON `u`.`id`=`user` LEFT JOIN `plans` AS `p` ON `p`.`id` = `plan` WHERE `email` LIKE :email OR `btc_addr` LIKE :email ");
				$query->execute(array(
					":email" => "%" . $_POST['email'] . "%",
				));
			} else {
				echo "<div class='alert alert-error'>Please fill in a search criteria</div>";
			}
		
			if (isset($query)) {
				if ($query->rowCount() != 0) {
?>
<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3>Payment Logs</h3>
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
                                                <th>User</td>
                        <th>Plan</td>
                                                <th>Email / Address</td>
												<th>Payment Type</th>
                                                <th>Transaction ID</td>
                                                <th>Amount</td>
                                                <th>Date</td>
                    </tr>
                </thead>
                <tbody>
                                <?php
                                
                                while($getInfo = $query -> fetch(PDO::FETCH_ASSOC))
                                {
                                        $user = htmlentities($getInfo['username']);
                                        $plan = htmlentities($getInfo['name']);
                                        $email = htmlentities(($getInfo['type'] == "pp" || $getInfo['type'] == "stripe" ? $getInfo['email'] : ( $getInfo['type'] == "btc" ? $getInfo['btc_addr'] : "n/a")));
                                        $tid = htmlentities($getInfo['tid']);
                                        $amount = htmlentities($getInfo['paid']);
                                        $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
                                        echo '<tr class="spacer"></tr><tr><td><center>'.$user.'</center></td><td><center>'.$plan.'</center></td><td><center>'.$email.'</center></td><td>' . $getInfo['type'] . '</td><td><center>'.$tid.'</center></td><td><center>$'.$amount.'</center></td><td><center>'.$date.'</center></td></tr>';
                                }

                                ?>
                </tbody>
										</table>
										




							</div>
						</article><!-- /widget -->
					</div>

<?php
				} else {
					echo "<div class='alert alert-error'>No results found for given search criteria</div>";
				}
			}
		} else {

?>
				
 <form action="" method="POST">
					
					<div class="col-md-8 row">
					<div class="col-md-12">
					<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7f-graph3"></i><h3>Sales Over The Past 2 Weeks</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content filled">
								<p class="graph-number"><?php
								$fetchSales = $odb->query("
									SELECT
										COUNT(*) AS `payments`,
										SUM(`paid`) AS `paid`
									FROM `payments`
									WHERE `date` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 14 DAY))
								");
								$fetched = $fetchSales->fetch();
								echo "<span>" . $fetched['payments'] . "</span> payments <span>($" . round($fetched['paid'], 2) . ")</span>";
								?></p>
								<div id="sales2w" style="width: 100%; height: 362px;"></div>

							</div>
						</article><!-- /widget -->
					</div>
					<div class="col-md-12 row">
<?php
$transactions = 0;
$paid = 0;
$fetchTransactions = $odb->query("SELECT `type`, COUNT(*) AS `total`, SUM(`paid`) AS `paid` FROM `payments` GROUP BY `type`");
while ($row = $fetchTransactions->fetch(PDO::FETCH_ASSOC)) {
	$payment = ($row['type'] == "pp" ? "PayPal" : ($row['type'] == "btc" ? "Bitcoin" : ($row['type'] == "stripe" ? "Stripe" : "Other")));
?>
					<div class="col-md-3">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph2"></i><h3><?php echo $payment; ?></h3>
								</div> 
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
										<?php
												echo "
		<strong>Total Transactions:</strong> <span class='badge blue'>" . number_format($row['total']) . "</span><br />
		<strong>Total Income:</strong> <span class='badge blue'>$" . number_format($row['paid'], 2) ."</span>" ;
	$transactions += $row['total'];
	$paid += $row['paid'];
										?>
										
											
										</div>


									</div>

								</div>

							</div>
						</article>
						</div>
<?php


}
?><div class="col-md-3">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph"></i><h3>Total Sales</h3>
								</div> 
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
						
												<strong>Total Transactions:</strong> <span class='badge blue'><?php echo number_format($transactions); ?></span><br />
												<strong>Total Income:</strong> <span class='badge blue'>$<?php echo number_format($paid, 2); ?></span>
										
										
											
										</div>


									</div>

								</div>

							</div>
						</article>
						</div>
						<!-- /widget -->
							</div>
							</div>
					
			
					<div class="col-md-4 row">
					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3>Transactions Statistics</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div id="chartdiv3" style="width: 100%; height: 362px;"></div>

									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					<div class="col-md-12">
					<article class="widget widget__form">
									<header class="widget__header">
										<div class="widget__title" style="width:100%;">
											<i class="pe-7s-search"></i><h3>Transaction Search</h3>
										</div>
									</header>

										
											
											<label for="input-1" class="stacked-label"><i class="pe-7f-user"></i></label>
			<input type="text" name="username" class="stacked-input" id="input-1" placeholder="Username contains">
			<label for="input-2" class="stacked-label"><i class="pe-7s-star"></i></label>
			<input type="text"  name="email" class="stacked-input" id="input-2" placeholder="Email/BTC Address contains">
			<label for="input-2" class="stacked-label"><i class="pe-7s-note"></i></label>
			<input type="text"  name="transaction" class="stacked-input" id="input-2" placeholder="Transaction ID">
			<button type="submit" name="search">Search</button>


									
								</article><!-- /widget -->
					</div>
					</div>
			
					
					<div class="col-md-12">
						<article class="widget">

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
											<?php
												$pages = round($transactions/$transPerPage);
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
									<i class="pe-7s-mouse"></i><h3>Payment Logs</h3>
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
                                                <th>User</td>
                        <th>Plan</td>
                                                <th>Email / Address</td>
												<th>Payment Type</th>
                                                <th>Transaction ID</td>
                                                <th>Amount</td>
                                                <th>Date</td>
                    </tr>
                </thead>
                <tbody>
                                <?php
                                $SQLGetLogs = $odb -> prepare("SELECT `payments`.* , `plans`.`name` AS `planname`, `users`.`username` FROM `payments` LEFT JOIN `plans` ON `payments`.`plan` = `plans`.`ID` LEFT JOIN `users` ON `payments`.`user` = `users`.`ID` ORDER BY `ID` DESC LIMIT :start, :max");
								$SQLGetLogs->bindValue(":start", ($page-1)*$transPerPage, PDO::PARAM_INT);
								$SQLGetLogs->bindValue(":max", ($transPerPage), PDO::PARAM_INT);
								$SQLGetLogs->execute();
                                while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
                                {
                                        $user =  htmlentities($getInfo['username']);
                                        $plan =  htmlentities($getInfo['planname']);
                                        $email =  htmlentities(($getInfo['type'] == "pp" || $getInfo['type'] == "stripe" ? $getInfo['email'] : ( $getInfo['type'] == "btc" ? $getInfo['btc_addr'] : "n/a")));
                                        $tid = htmlentities( $getInfo['tid']);
                                        $amount = htmlentities( $getInfo['paid']);
                                        $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
                                        echo '<tr class="spacer"></tr><tr><td><center>'.$user.'</center></td><td><center>'.$plan.'</center></td><td><center>'.$email.'</center></td><td>' . $getInfo['type'] . '</td><td><center>'.$tid.'</center></td><td><center>$'.$amount.'</center></td><td><center>'.$date.'</center></td></tr>';
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
				 
				 </div>
					
			<?php } ?>

		</section> <!-- /content -->

	</div>


	 
		
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<script type="text/javascript" src="../js/amcharts/xy.js"></script>
	<script type="text/javascript" src="../js/amcharts/radar.js"></script>
											
										
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
		COUNT(*) as `payments`,
		`type`,
		SUM(`paid`) AS `paid`
	FROM `payments`
	WHERE `date` >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 14 DAY))
	GROUP BY DAY(FROM_UNIXTIME(date)), DAYNAME(FROM_UNIXTIME(date)), `type`
	ORDER BY `date` ASC
");
$rows = $fetchUsers->fetchAll(PDO::FETCH_ASSOC);

function fetchDayType(&$arr, $day, $type) {
	foreach ($arr as $k => $v) {
		if ($v['day'] === $day && $v['type'] == $type)
			return $v;
	}
}

for ($i = 14; $i >= 0; --$i) {
	$day = date("j", strtotime("-$i days"));
	$stripe = fetchDayType($rows, $day, "stripe");
	$pp = fetchDayType($rows, $day, "pp");
	$btc = fetchDayType($rows, $day, "btc");
	
	echo "{
		'date':'" . $day . "',
		'stripe':'" . ($stripe !== null ? round($stripe['paid'], 2) : 0) . "',
		'paypal':'" . ($pp !== null ? round($pp['paid'], 2) : 0) . "',
		'btc':'" . ($btc !== null ? round($btc['paid'], 2) : 0) . "'
	}" . ($i > 0 ? "," : "");
}

?>
			];
			chart.pathToImages = "http://www.amcharts.com/lib/3/images/";
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
			graph.valueField = "stripe";
			graph.lineColor = "#53d769";
			graph.lineThickness = 3;
			graph.bullet = "round";
			//graph.bulletColor = "rgba(0,0,0,0.3)";
			//graph.bulletBorderColor = "#53d769";
			graph.bulletBorderAlpha = 1;
			graph.bulletBorderThickness = 3;
			graph.bulletSize = 6;
			chart.addGraph(graph);
			var graph2 = new AmCharts.AmGraph();
			graph2.type = "smoothedLine";
			graph2.valueField = "paypal";
			graph2.lineColor = "#f00";
			graph2.lineThickness = 3;
			graph2.bullet = "round";
			//graph2.bulletColor = "rgba(0,0,0,0.3)";
			//graph2.bulletBorderColor = "#53d769";
			graph2.bulletBorderAlpha = 1;
			graph2.bulletBorderThickness = 3;
			graph2.bulletSize = 6;
			chart.addGraph(graph2);
			var graph3 = new AmCharts.AmGraph();
			graph3.type = "smoothedLine";
			graph3.valueField = "btc";
			graph3.lineColor = "#1c7dfa";
			graph3.bullet = "round";
			//graph3.bulletColor = "rgba(0,0,0,0.3)";
			//graph3.bulletBorderColor = "#53d769";
			graph3.bulletBorderAlpha = 1;
			graph3.bulletBorderThickness = 3;
			graph3.bulletSize = 6;
			chart.addGraph(graph3);
			var chartCursor = new AmCharts.ChartCursor();
			chart.addChartCursor(chartCursor);
			chartCursor.categoryBalloonAlpha = 0.2;
			chartCursor.cursorAlpha = 0.2;
			chartCursor.cursorColor = 'rgba(255,255,255,.8)';
			chartCursor.categoryBalloonEnabled = false;
			chart.write("sales2w");
		
			var chart3 = AmCharts.makeChart("chartdiv3", {
				"type": "pie",
				"theme": "none",
				"colors": ['#1c7dfa', '#53d769', '#f00'],
				"dataProvider": [
<?php
$i = 1;
$fetchTransactions = $odb->query("SELECT `type`, COUNT(*) AS `total`, SUM(`paid`) AS `paid` FROM `payments` GROUP BY `type`");
while ($row = $fetchTransactions->fetch(PDO::FETCH_ASSOC)) {
	$payment = ($row['type'] == "pp" ? "PayPal" : ($row['type'] == "btc" ? "Bitcoin" : ($row['type'] == "stripe" ? "Stripe" : "Other")));
	echo "{'title':'" . $payment . "','value':'" . $row['paid'] . "'}";
	if ($i < $fetchTransactions->rowCount()) echo ",";
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