<?php

error_reporting(0);

ob_start();
require_once '../includes/db.php';
require_once '../includes/init.php';

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
if (!($user -> isAdmin($odb)))
{
	die('You are not admin');
}
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);	


$currentPage = "admin_support";
$pageon = "Support";
$ticketsPerPage = 30;
$page = 1;
if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']))
	$page = $_GET['page'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?>Support</title>
	
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

										<th>
ID
										</th>
										
										<th>Username</th>

										<th>Subject</th>

										<th>Status</th>

										<th>Actions</th>

									</tr>

								</thead>

								<tbody>

									<?php 
					
			$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` ORDER BY `id` DESC LIMIT :start, :max");
			$SQLGetTickets->bindValue(":start", ($page-1)*$ticketsPerPage, PDO::PARAM_INT);
			$SQLGetTickets->bindValue(":max", $ticketsPerPage, PDO::PARAM_INT);
			$SQLGetTickets->execute();
			
			while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
			{
				$id = htmlentities($getInfo['id']);
                                $user = htmlentities($getInfo['username']);
				$subject = htmlentities($getInfo['subject']);
				$status = htmlentities($getInfo['status']);

                                      	if ($status == "Closed") {
					$status1 = '<i class="fa fa-star-half-empty"></i>';
					} elseif ($status == "Waiting for Staff response.") {
					$status1 = '<i class="fa fa-star"></i>';
					} elseif ($status == "Waiting for member response.") {
					$status1 = '<i class="fa fa-star-o"></i>';
			
					
					
					} else {
					$membership = 'Undefined';
					}
					if ($status == "Closed")
					{
					echo '<tr class="spacer"></tr><tr><td> '.$id.'</td><td>'.$user.'</td><td>'.$subject.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a></td></tr>';
					
					}
					else{
                     echo '<tr class="spacer"></tr><tr><td> '.$id.'</td><td>'.$user.'</td><td>'.$subject.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a><span class="right"><form method="post"><input type="submit" class="btn" name="closeBtn" value="Close""/><input type="hidden" name="closeID" value="'.$id.'" /></span></form></td></tr>';
					 }
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