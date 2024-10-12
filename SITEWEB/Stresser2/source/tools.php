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
}



if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}

$currentPage = "tools";
$pageon = "Tools";
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
						<i class="pe-7f-map-marker"></i>
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
				$resolvedDomain = '';
				if (isset($_POST['domain']) && !empty($_POST['domain']))
				{
					$domain = str_replace(array("http://","https://"), "", trim($_POST['domain']));
					$domain = ltrim($domain, "/");
					$resolvedDomain = gethostbyname($domain);
				}
				$resolvedSkype = '';
				if (isset($_POST['skype']) && !empty($_POST['skype']))
				{
					$skype = $_POST['skype'];
					$resolvedSkype = @file_get_contents('http://webresolver.nl/api.php?key=4E9S1-ZKSNI-ZTH0K-0DFS1&action=resolve&string='.$skype);
				}
				?>

				<div class="row">
 <form action="" method="POST">
 <?php
	if (isset($resolvedDomain) && !empty($resolvedDomain)) {
		echo "<div class='alert'>" . $resolvedDomain . "</div>";
	} else 
	if (isset($resolvedSkype) && !empty($resolvedSkype)) {
		echo "<div class='alert'>" . $resolvedSkype . "</div>";
	}
 ?>
					<div class="col-md-6">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-menu"></i><h3>Domain Resolver</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
								<input name="domain" value="" type="text" placeholder="Website"/>
								<button type="submit" name="resolveBtn">Resolve</button>
								<!--<input type="submit" name="attackBtn" value="Stress" />-->
						</div>
					</div>
					</form>
					<div class="row">
 <form action="" method="POST">
					<div class="col-md-6">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-menu"></i><h3>Skype Resolver</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
								<input name="skype" value="" type="text" placeholder="Skype"/>
								
								<button type="submit" name="resolveBtn">Resolve</button>
								<!--<input type="submit" name="attackBtn" value="Stress" />-->
						</div>
					</div>
					
					</form>
						</article><!-- /widget --> 
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