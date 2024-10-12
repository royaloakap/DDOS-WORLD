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

$currentPage = "admin_utilities";
$pageon = "Utilities";
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
						<i class="pe-7s-tools"></i>
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
$result  = "";
$success = false;


if (isset($_POST['actionExpired'])) {
	$removePlans = $odb->query("
		UPDATE `users`
		SET `expire` = '0', `membership` = '0'
		WHERE `expire` < UNIX_TIMESTAMP(NOW()) AND `expire` > 0
	");
	
	$success = true;
	$result = "Removed " . $removePlans->rowCount() . " expired plans";
} else if (isset($_POST['actionDBFix'])) {

	$fetchTables = $odb->query("SHOW TABLES");
	$tables = $fetchTables->fetchAll(PDO::FETCH_COLUMN);
	array_walk($tables, function (&$value, &$key) {
		$value = "`" . $value . "`";
	});
	$query = "OPTIMIZE TABLE " . implode(",", $tables) . ";";
	

	$optimize = $odb->query($query);
	

	$success = true;
	$result = "Optimized " . $optimize->rowCount() . " tables";
} else if (isset($_POST['updatePlan'])) {
	if (isset($_POST['length'], $_POST['unit']) && !empty($_POST['length']) && !empty($_POST['unit'])) {
		$increment = ( strtotime("+" . $_POST['length'] . " " . $_POST['unit']) - time() );
		$updateUsers = $odb->prepare("
			UPDATE `users`
			SET `expire` = `expire` + :inc
			WHERE `expire` > 0
		");
		$updateUsers->bindValue(":inc", $increment, PDO::PARAM_INT);
		$updateUsers->execute();
		
		if ($updateUsers->rowCount() != 0) {
			$result = "Successfully increased all member's plan expiration time by " .
				$increment . " seconds (" . $_POST['length'] . " " . $_POST['unit'] . ")";
			$success = true;
		} else {
			$result = "Failed to increase expiration time to all users (something fucked up)";
		}
	} else {
		$result = "You piece of shit, don't even think about leaving shit out";
	}
}

if (!empty($result)) {
	echo "<div class='alert alert-" . ($success ? "success" : "danger") . "'>" . $result . "</div>";
}

?>
	
				<div class="row">
 <form action="" method="POST">
					
						<div class="col-md-4">
						
								<article class="widget">
									<header class="widget__header">
										<div class="widget__title" style="width:100%;">
											<i class="pe-7s-user"></i><h3>User Management</h3>
										</div>
									</header>
									<div class="widget__content filled pad20">
										<center>
											<button name="actionExpired" class="btn btn-lg red inverse">Remove Expired Plans</button>
										</center>
									</div>
								</article>
										
						</div>
					
						<div class="col-md-4">
						
								<article class="widget">
									<header class="widget__header">
										<div class="widget__title" style="width:100%;">
											<i class="pe-7s-server"></i><h3>Database Management</h3>
										</div>
									</header>
									<div class="widget__content filled pad20">
										<center>
											<button name="actionDBFix" class="btn btn-lg red inverse">Fix Database</button>
										</center>
									</div>
								</article>
										
						</div>
						
						<div class="col-md-4">
						
								<article class="widget">
									<header class="widget__header">
										<div class="widget__title" style="width:100%;">
											<i class="pe-7s-hammer"></i><h3>Plan Management</h3>
										</div>
									</header>
									<div class="widget__form">
										<input type="text" name="length" placeholder="Length of increment" />
										<select name="unit" class="btn btn-block gray dropdown-toggle" style='color:#000;'>
											<option value="Minutes">Minutes</option>
											<option value="Hours">Hours</option>
											<option value="Days">Days</option>
											<option value="Weeks">Weeks</option>
											<option value="Years">Years</option>
										</select>
										<button type="submit" name="updatePlan">Increase Expiration Date(To all users)</button>
									</div>
								</article>
								
						</div>
					
					</form>
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