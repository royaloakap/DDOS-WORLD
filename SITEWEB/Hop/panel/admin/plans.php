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

$currentPage = "admin_plans";
$pageon = "Plans";
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
						<i class="pe-7f-science"></i>
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
				if (isset($_POST['addBtn']))
				{
					$nameAdd = $_POST['nameAdd'];
					$descriptionAdd = $_POST['descriptionAdd'];
					$unitAdd = $_POST['unit'];
					$lengthAdd = $_POST['lengthAdd'];
					$mbtAdd = intval($_POST['mbt']);
					$attacks = intval($_POST['attacks']);
					$priceAdd = floatval($_POST['price']);
					
					if (empty($priceAdd) || empty($nameAdd) || empty($descriptionAdd) || empty($unitAdd) || empty($lengthAdd) ||empty($attacks) || empty($mbtAdd))
					{
						echo '<div class="g_12"><div class="alert alert-danger">Please fill in all fields</div></div>';
					}
					else
					{
						$methods = $_POST['methods'];
						$methods = implode(",", $methods);
					
						$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :description, :mbt, :attacks, :unit, :length, :price, :methods)");
						$SQLinsert -> execute(array(':name' => $nameAdd, ':description' => $descriptionAdd, ':mbt' => $mbtAdd, ':unit' => $unitAdd, ':length' => $lengthAdd, ':price' => $priceAdd, ":methods" => $methods, ":attacks"=>$attacks));
						echo '<div class="g_12"><div class="alert alert-success">Plan has been created</div></div>';
					}
				}
				?>
				<?php
		if (isset($_POST['deleteBtn']))
		{
			$deletes = $_POST['deleteCheck'];
			foreach($deletes as $delete)
			{
				$SQL = $odb -> prepare("DELETE FROM `plans` WHERE `ID` = :id LIMIT 1");
				$SQL -> execute(array(':id' => $delete));
			}
			echo '<div class="g_12"><div class="alert alert-success">Plan(s) have been removed</div></div>';
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
								  <input placeholder="Name" name="nameAdd" maxlength="50" type="text"/>
								<input placeholder="Description" name="descriptionAdd" maxlength="50" type="text"/>
								<input placeholder="Max Boot Time" name="mbt" type="text"/>
								<input placeholder="Length" name="lengthAdd" type="text"/>
								<input placeholder="Price" name="price" type="text"/>
								<input class="form-control" placeholder="Concurrant Attacks" name="attacks" type="text"/>
							
<?php

$fetchmethods = $odb->query("select * from boot_methods");
while ($row = $fetchmethods->fetch(PDO::FETCH_ASSOC)) {
		echo "<div style='float:left;padding:8px;height:80px; width:100px;'>
		 <input id='switch-". $row['method'] . "' class=\"form-control sw\" type=\"checkbox\" name=\"methods[]\" value=\"" . $row['method'] . "\">
		 <label class='switch2 blue' for='switch-". $row['friendly_name'] . "'></label>
		 <label for='switch-". $row['method'] . "' >" . $row['friendly_name']. "</label>
		 </div>";
}

?>
								<select name="unit" class="btn btn-block dropdown-toggle" style='color:#000'>
                                            
                                    
                                                                                <option selected="selected" value="Days">Days</option>
										<option value="Week">Week</option>
										<option value="Months">Months</option>
										<option value="Years">Years</option>

                                </select>
								<button type="submit" name="addBtn">Update</button>
								
								
						</div>
					</div>
					

					
						</article><!-- /widget -->
					</div>
					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%">
									<i class="pe-7s-menu"></i><h3>Current Plans</h3>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
									  	<thead>
									  		<tr>
													<th><center></th>
                                                    <th><center>Name</th>
                                                    <th><center>MBT</th>
													<th><center>Concurrents</th>
                                                    <th><center>Units</th>
													<th><center>Length</th>
													<th><center>Price</th>
													<th></th>
                                                </tr>
                                            </thead>
											<tbody>
<?php
				  $SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
				  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
				  {
					$planName = $show['name'];
					$mbtShow = $show['mbt'];
					$max_bootsShow = $show['max_boots'];
					$unitShow = $show['unit'];
					$lengthShow = $show['length'];
					$priceShow = $show['price'];
					$rowID = $show['ID'];
					echo '<tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td><center><a href="editplan.php?id='.$rowID.'">'.htmlentities($planName).'</a></center></td><td>'.$mbtShow.'</td><td>'.$max_bootsShow.'</td><td>'.$unitShow.'</td><td>'.$lengthShow.'</td><td>'.$priceShow.'</td><td><form method="POST"><input style="float:right;margin-top:6px;" type="submit" name="deleteBtn" value="Delete" class="btn btn--primary" /></form></td></tr>';
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