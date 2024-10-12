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

$currentPage = "profile";
$pageon = "Profile";
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
						<i class="pe-7f-users"></i>
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
		if (isset($_POST['PassBtn']))
		{
			$cpassword = $_POST['cpassword'];
			$npassword = $_POST['npassword'];
			$rpassword = $_POST['rpassword'];
			if (!empty($cpassword) && !empty($npassword) && !empty($rpassword))
			{
				if ($npassword == $rpassword)
				{
					$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
					$SQLCheckCurrent -> execute(array(':username' => $_SESSION['username'], ':password' => SHA1($cpassword)));
					$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
					if ($countCurrent == 1)
					{
						$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
						$SQLUpdate -> execute(array(':password' => SHA1($npassword),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
						echo '<div class="alert alert-success"><p><strong>SUCCESS: </strong>Password Has Been Updated</p></div>';
					}
					else
					{
						echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>Current Password is incorrect.</p></div>';
					}
				}
				else
				{
					echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>New Passwords Did Not Match.</p></div>';
				}
			}
			else
			{
				echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>Please fill in all fields</p></div>';
			}
		}
		?>
		<?php 
		if (isset($_POST['EmailBtn']))
		{
			$cpassword = $_POST['cpassword'];
			$nemail = $_POST['nemail'];
			if (!empty($cpassword) && !empty($nemail))
			{
				if (filter_var($nemail, FILTER_VALIDATE_EMAIL))
				{
					$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
					$SQLCheckCurrent -> execute(array(':username' => $_SESSION['username'], ':password' => SHA1($cpassword)));
					$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
					if ($countCurrent == 1)
					{
						$SQLUpdate = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `username` = :username AND `ID` = :id");
						$SQLUpdate -> execute(array(':email' => $nemail,':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
						echo '<div class="alert alert-success"><p><strong>SUCCESS: </strong>Email Has Been Updated</p></div>';
					}
					else
					{
						echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>Current Password is Incorrect.</p></div>';
					}
				}
				else
				{
					echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>Email is not valid</p></div>';
				}
			}
			else
			{
				echo '<div class="alert alert-danger"><p><strong>FAILURE: </strong>Please fill in all fields</p></div>';
			}
		}
		?>

				<div class="row">
 <form action="" method="POST">
					<div class="col-md-6">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7f-users"></i><h3>Password Update</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
								<input type="password" name="cpassword" placeholder="Current Password" />
								<input type="password" name="npassword" value="" placeholder="New Password" />
								<input type="password" name="rpassword" value="" placeholder="Repeat Password" />
								<button type="submit" name="PassBtn">Update Password</button>
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
									<i class="pe-7f-users"></i><h3>Email Update</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
								<input type="password" name="cpassword" value="" placeholder="Current Password" />
								<input type="text" name="nemail" value="" placeholder="New Email" />
								<button type="submit" name="EmailBtn">Update Email</button>
						</div>
					</div>
						</article><!-- /widget --> 
					</div>

					</form>
		</section> <!-- /content -->

	</div>
	
	<script type="text/javascript" src="js/countdown/jquery.plugin.js"></script>
	<script type="text/javascript" src="js/countdown/jquery.countdown.js"></script>
</body>
</html>