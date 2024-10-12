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

$currentPage = "admin_index";
$pageon = "Dashboard";
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
						<span> News</span>
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
				if (isset($_POST['addBtn']))
				{
					$titleAdd = $_POST['titleAdd'];
					$detailAdd = $_POST['detailAdd'];
					if (!empty($titleAdd) && !empty($detailAdd))
					{
						$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL, :title, :uid, :detail, UNIX_TIMESTAMP())");
						$SQLinsert -> execute(array(':title' => $titleAdd, ':detail' => $detailAdd, ":uid" => $_SESSION['ID']));
						echo '<div class="g_12"><div class="alert alert-success">News has been posted</div></div>';
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger">Please fill in all fields</div></div>';
					}
				}
				?>
				<?php
						if (isset($_POST['deleteBtn']))
						{
							$deletes = $_POST['deleteCheck'];
							foreach($deletes as $delete)
							{
								$SQL = $odb -> prepare("DELETE FROM `news` WHERE `ID` = :id LIMIT 1");
								$SQL -> execute(array(':id' => $delete));
							}
							echo '<div class="g_12"><div class="alert alert-success">New(s) Have been removed</div></div>';
						}
						?>
				<div class="row">
 <form action="" method="POST">
					<div class="col-md-4">
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
								<input placeholder="Subject" name="titleAdd" maxlength="25" type="text"/>
								<textarea name="detailAdd" style="width:100%;height:300px;" class="input" placeholder="Enter text ..."></textarea>
								
								
								<button type="submit" name="addBtn">Post News</button>
								
						</div>
					</div>
					

					<div class="col-md-8">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3>Current News</h3>
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
									  			<th>Check Box</th>
									  			<th>Title</th>
									  			<th>Details</th>
									  			<th>Delete</th>
									  			
									  		</tr>
									  	</thead>
									  	<tbody>

									  	
									  		
									  		<?php
								  $SQLSelect = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC");
								  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
								  {
									$titleShow = $show['title'];
									$detailShow = $show['detail'];
									$rowID = $show['ID'];
									echo '<tr class="spacer"></tr><tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td>'.htmlentities($titleShow).'</td><td>'.htmlentities($detailShow).'</td><td><input type="submit" name="deleteBtn" class="btn" value="Delete"></td></tr>';
								  }
								  ?>
									  		
									  	

									  	</tbody>
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