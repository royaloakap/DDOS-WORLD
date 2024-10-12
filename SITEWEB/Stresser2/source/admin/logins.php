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


$currentPage = "admin_logins";
$pageon = "Login History";
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
						<i class="pe-7f-clock"></i>
						<span> Login History</span>
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
					
					

					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-clock"></i><h3><?php echo $pageon ?></h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
						
										
										<table class="table table-striped media-table">
                                          
												<tr>
													<th style="width:40%">IP Address</th>
													<th>Date of Login</th>
												</tr>
											
 <?php
				if (isset($_GET['user']) && !empty($_GET['user']) && is_String($_GET['user'])) {
					$fetchLogs = $odb->prepare("SELECT * FROM `login_history` WHERE `username`=:user ORDER BY `username` ASC, `date` DESC;");
					$fetchLogs->execute(array(":user"=>$_GET['user']));
				} else {
					$fetchLogs = $odb->query("SELECT * FROM `login_history` ORDER BY `username` ASC, `date` DESC;");
				}
				$previousUser = null;
				if ($fetchLogs->rowCount() != 0)
				{
					while ($row = $fetchLogs->fetch(PDO::FETCH_ASSOC))
					{
						if ($row['username'] !== $previousUser)
						{
							echo "<tr class='spacer'></tr><tr><th colspan='2'><strong>" . htmlentities($row['username']) . "</strong></th></tr>";
							$previousUser = $row['username'];
						}
						
						echo "<tr class='spacer'></tr><tr><td>" . $row['ip'] . "</td><td>" . date("r", $row['date']) . "</td></tr>";
					}
				}
				?>
                                          
										</table>
										


 

							</div>
						</article><!-- /widget -->
					</div>

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<script type="text/javascript" src="../js/chart.js"></script>
</body>
</html>