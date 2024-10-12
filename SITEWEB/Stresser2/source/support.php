<?php

error_reporting(0);

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

$currentPage = "support";
$pageon = "Support Center";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);						
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
	<link rel="stylesheet" type="text/css" href="css/main.min.css?v1.3">
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
				if (isset($_POST['closeBtn']))
	   {
$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $_POST['closeID']));
echo '<div class="g_12"><div class="alert alert-success">SUCCESS: Ticket has been closed.</div></div>';
 	   }
	   ?>
	   
	   <?php 
	   if (isset($_POST['updateBtn']))
	  {
			$subject = ($_POST['subject']);
			$content = ($_POST['content']);
			$errors = array();

			if (empty($subject) || empty($content))
			{
				echo '<div class="g_12"><div class="alert alert-danger">ERROR: Fill in all fields!</div></div><meta http-equiv="refresh" content="3;url=support.php">';
				exit;
			}
			if (empty($errors))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `tickets` VALUES(NULL, :subject, :content, :status, :username, UNIX_TIMESTAMP(NOW()))");
				$SQLinsert -> execute(array(':subject' => $subject, ':content' => $content, ':status' => 'Waiting for Staff response.', ':username' => $_SESSION['username']));
				echo '<div class="g_12"><div class="alert alert-success">SUCCESS: Ticket has been created.</div></div>';
			}
			}

	   ?>
				<div class="row">
 <form action="" method="POST">
					<div class="col-md-6">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-menu"></i><h3>Create Ticket</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
							
								<input name="subject" maxlength="64" placeholder="Subject" type="text"/>
								<textarea name="content" id="field-ta" class="input" style="width:100%;height:300px;" placeholder="Enter text ..."></textarea>
								
								
								<button type="submit" name="updateBtn">Send</button>
								
						</div>
					</div>
					

					<div class="col-md-6">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3>Your Tickets</h3>
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

										<th>Subject</th>

										<th>Status</th>

										<th>Actions</th>
										

									</tr>

								</thead>

								<tbody>

									<?php 
			$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = :username ORDER BY `id` DESC");
			$SQLGetTickets -> execute(array(':username' => $_SESSION['username']));
			while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
			{
				$id = htmlentities($getInfo['id']);
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
					echo '<tr class="spacer"></tr><tr><td> '.$id.'</td><td>'.$subject.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a></td></tr>';
					
					}
					else{
                     echo '<tr class="spacer"></tr><tr><td> '.$id.'</td><td>'.$subject.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a><span class="right"><form method="post"><input type="submit" class="btn" name="closeBtn" value="Close""/><input type="hidden" name="closeID" value="'.$id.'" /></span></form></td></tr>';
					 }
                                                    }
			?>
</tbody>
										</table>
										



							</div>
						</article><!-- /widget -->
					</div>

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
</body>
</html>

