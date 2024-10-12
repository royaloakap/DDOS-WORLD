<?php
ob_start();
require_once '../includes/db.php';
require_once '../includes/init.php';
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user -> isAdmin($odb)))
{
	die('You are not admin');
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}
if (!isset($_GET['id']))
{
	die('No ID Selected');
}

$currentPage = "admin_user";
$pageon = "Edit User";

$id = $_GET['id'];
$SQLGetInfo = $odb -> prepare("SELECT * FROM `users` WHERE `ID` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $_GET['id']));
$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$username = htmlentities($userInfo['username']);
$password = htmlentities($userInfo['password']);
$email = htmlentities($userInfo['email']);
$rank = htmlentities($userInfo['rank']);
$membership = htmlentities($userInfo['membership']);
$status = htmlentities($userInfo['status']);
$layer4 = htmlentities($userInfo['layer4']);
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
		<?php 
				   if (isset($_POST['rBtn']))
				   {
					$sql = $odb -> prepare("DELETE FROM `users` WHERE `ID` = :id");
					$sql -> execute(array(':id' => $id));
					header('location: users.php');
				   }
				   if (isset($_POST['updateBtn']))
				   {
					$update = false;
					if ($username!= $_POST['username'])
					{
						if (ctype_alnum($_POST['username']) && strlen($_POST['username']) >= 4 && strlen($_POST['username']) <= 26)
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `username` = :username WHERE `ID` = :id");
							$SQL -> execute(array(':username' => $_POST['username'], ':id' => $id));
							$update = true;
							$username = $_POST['username'];
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger">Username has to be 4-26 characters and alphanumeric</div></div>';
						}
					}
					if (!empty($_POST['password']))
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `ID` = :id");
						$SQL -> execute(array(':password' => SHA1($_POST['password']), ':id' => $id));
						$update = true;
						$password = SHA1($_POST['password']);
					}
					if ($email != $_POST['email'])
					{
						if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `ID` = :id");
							$SQL -> execute(array(':email' => $_POST['email'], ':id' => $id));
							$update = true;
							$email = $_POST['email'];
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger">Email is invalid</div></div>';
						}
					}
					if ($rank != $_POST['rank'])
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `rank` = :rank WHERE `ID` = :id");
						$SQL -> execute(array(':rank' => $_POST['rank'], ':id' => $id));
						$update = true;
						$rank = $_POST['rank'];
					}
					if ($membership != $_POST['plan'])
					{
						if ($_POST['plan'] == 0)
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `expire` = '0', `membership` = '0' WHERE `ID` = :id");
							$SQL -> execute(array(':id' => $id));
							$update = true;
							$membership = $_POST['plan'];
						}
						else
						{
							$getPlanInfo = $odb -> prepare("SELECT `unit`,`length` FROM `plans` WHERE `ID` = :plan");
							$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
							$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
							$unit = $plan['unit'];
							$length = $plan['length'];
							$newExpire = strtotime("+{$length} {$unit}");
							$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
							$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
							$update = true;
							$membership = $_POST['plan'];
						}
					}
					
					if ($status != $_POST['status'])
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `status` = :status WHERE `ID` = :id");
						$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));
						$update = true;
						$status = $_POST['status'];
					}
					if ($update == true)
					{
						echo '<div class="g_12"><div class="alert alert-success">User has been updated</div></div>';
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-success">Nothing was updated</div></div>';
					}
					}
				   ?>
				   
				   
				   
				   
				<div class="row">
 <form action="" method="POST">
					<div class="col-md-12">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-menu"></i><h3><?php echo $pageon ?></h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content">
								 <input name="username" maxlength="15" value="<?php echo $username;?>" type="text"/>
								<input placeholder="Password" name="password" type="text"/>
								<input name="email" type="text" value="<?php echo htmlentities($email);?>"/>

								<select name="plan" class="btn btn-block gray dropdown-toggle">
											<option value="0">No Membership</option>
<?php 
									$SQLGetMembership = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
									while($memberships = $SQLGetMembership -> fetch(PDO::FETCH_ASSOC))
									{
										$mi = $memberships['ID'];
										$mn = $memberships['name'];
										$selectedM = ($mi == $membership) ? 'selected="selected"' : '';
										echo '<option value="'.$mi.'" '.$selectedM.'>'.$mn.'</option>';
									}
								?>


                                </select>
						<select class="btn btn-block gray dropdown-toggle" name="status">
								<?php
								function selectedS($check, $rank)
								{
									if ($check == $rank)
									{
										return 'selected="selected"';
									}
								}
								?>
                                <option value="0" <?php echo selectedS(0, $status); ?>>Active</option>
                                <option value="1" <?php echo selectedS(1, $status); ?>>Banned</option>
								</select>
								
								
               
								
								
		
								
								
								<button type="submit" name="updateBtn">Update</button>
								<button type="submit" name="rBtn">Remove</button>
								
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
	<script type="text/javascript" src="../js/chart.js"></script>
</body>
</html>