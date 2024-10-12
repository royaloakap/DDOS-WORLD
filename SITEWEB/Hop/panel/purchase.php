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

$currentPage = "purchase";
$pageon = "Purchase Plan";

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
						<i class="pe-7f-network"></i>
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
		
				<div class="row">
 <form action="" method="POST">
 
 
		
                                                 <?php
					$newssql = $odb -> query("SELECT * FROM `plans` ORDER BY ABS(`price`) ASC, `name` ASC;");
                        while($row = $newssql ->fetch())
                        {
?>
<div class="col-md-4">
<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7f-cart"></i><h3><?php echo htmlentities($row['name']); ?></h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">

										<?php
											
											echo "" . $row['mbt'] . " seconds for attacks ". "</strong>";
										?>
										
								<hr width="">
									
											<?php
											
											echo "" . $row['max_boots'] . " concurrents". "</strong>";
										?>
										
										<hr width="">
											<?php
											
											echo "" . $row['length'] . " " . $row['unit'] . "</strong>";
										?>
										
									<hr width="">
											<?php
											
											echo "This type of stresser is only for web server (website)" . "</strong>";
										?>
									
											<hr width=""> 
											<?php
											
											echo "Available methods:  " . $row['methods'] . " " . "</strong>";
										?>
	<hr width="">
										
										
											<?php
											
											echo "Price:  " . $row['price'] . " " . "€ </strong>";
										?>
									
										<br>
										<br>
											<?php
											
											echo "Available payments";
										?>
										
							
										
<center>
									<a href="order.php?id=<?php echo $row['ID']; ?>&method=paypal" title="" class="btn blue inverse">Paypal</a>
									</div>

								</div>

										
							</div>
						</article><!-- /widget --> 
						</div>
					
<?php
						}
						?>


					</div>

				 
					<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">
						<i class="pe-7s-close"></i>
					</span>
					<span class="sr-only">
						Close
					</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Payment</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn blue inverse" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>

		</section> <!-- /content -->

	</div>


	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
	
	<script type="text/javascript" src="js/countdown/jquery.plugin.js"></script>
	<script type="text/javascript" src="js/countdown/jquery.countdown.js"></script>
	
	<script type="text/javascript">
		jQuery(function(){
		$('a.iframePopup').on('click', function(ev) {
			$("#modal-body").html("");
			$('#myModal').modal('show');
			
			$(".modal-body").html("");

			$.ajax({
				url : "/" + $(this).attr('href')
			}).success(function(data) {
				$(".modal-body").html(data);
			}).error(function(xhr, status, error) {
				$(".modal-body").html("<div class='alert alert-error'>Error: An unexpected error occurred and could not load payment gateway.<br />Error message: " + xhr.responseText + "</div>");
			});
			
			//alert($(this).attr("modal-type"));
			//if ($(this).attr("modal-type") == "btc") {
			//	$(".modal-body").css({ "height" : "600px"});
			//} else {
			//	$(".modal-body").css({ "height" : "auto"});
			//}
			
			//$(".modal-body").html("<iframe style='width:100%;height:100%;' src='/" + $(this).attr('href') + "'></iframe>");

			//$("#results").text(data);

			ev.preventDefault();
		});
		});
	</script>
</body>
</html>