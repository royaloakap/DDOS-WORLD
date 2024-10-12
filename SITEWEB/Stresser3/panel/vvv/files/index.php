<?php 

header("X-XSS-Protection: 1; mode=block");

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("fuck off");
	ob_start();
	
	require_once 'vvv/avg/mycon.php';
	require_once 'vvv/avg/usv.php';
	
	if (!(empty($maintaince))) {
		header('Location: maintenance.php');
		exit;
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		header('location: login.php');
		die();
	}
	
?>

<!DOCTYPE html>
<html><head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
	<meta content="Coderthemes" name="author">
	<link rel="icon" type="png" href="favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- App favicon -->
	<link rel="manifest" href="/site.webmanifest">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<!-- third party css -->
	<link href="assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css">
	<!-- Plugins css-->
	<link href="assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
	<link href="assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css">
	<link href="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">
	<link href="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet">
	<!-- Sweet Alert-->
	<link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
	<!-- App css -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/app.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/remixicon.css" rel="stylesheet" type="text/css">
	<link href="assets/css/animate.css" rel="stylesheet" type="text/css">
	<link href="assets/css/sidebar-menu.css" rel="stylesheet" type="text/css">
	<script src="https://shoppy.gg/api/embed.js"></script><style 
type="text/css">.shoppy-wrapper {
    overflow-y: hidden;
    position: fixed;
    z-index: 999999;
    background: rgba(0, 0, 0, 0.63);
    width: 100%;
    height: 100%;
    top: 0;
    margin-left: auto;
    margin-right: auto;
}

.shoppy-wrapper > .shoppy-container {
    position: relative;
    width: 100%;
    height: 0;
    padding-bottom: 56.25%;
}

.shoppy-wrapper > .shoppy-container > iframe {
    background: transparent !important;
    border: none;
    height: 100vh;
    width: 90vw;
    margin-right: auto;
    left: 50%;
    position: absolute;
    margin-left: -45vw;
}

.shoppy-close {
    z-index: 9999999999;
    height: 40px;
    position: fixed;
    right: 20px;
    top: 20px;
    width: 40px;
    margin: 0;
    border-radius: 50%;
    cursor: pointer
}

.shoppy-close:hover {
    background: rgba(0, 0, 0, .4)
}

.shoppy-close:after, .shoppy-close:before {
    background: #fff;
    content: '';
    display: block;
    left: 50%;
    position: absolute;
    top: 50%;
    height: 70%;
    width: 2px
}

.shoppy-close:before {
    -webkit-transform: translateX(-50%) translateY(-50%) rotate(45deg);
    -webkit-transform-origin: center center;
    transform: translateX(-50%) translateY(-50%) rotate(45deg);
    transform-origin: center center
}

.shoppy-close:after {
    -webkit-transform: translateX(50%) translateY(50%) rotate(-45deg);
    -webkit-transform-origin: center center;
    transform: translateX(-50%) translateY(-50%) rotate(-45deg);
    transform-origin: center center
}</style>
<style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;box-sizing: content-box;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style><style type="text/css">.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;box-sizing: content-box;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style></head>
<body class="center-menu">
	<!-- Navigation Bar-->
	<nav class="navbar-expand-lg navbar-dark active" id="topnav">
		<div class="container-fluid in">
			<div id="navigation" class="active">
				<!-- Navigation Menu-->
				<ul class="navigation-menu in">
					<li class="has-submenu">
						<a href="home.php" class="nav-link">
							<i class="ri-home-gear-line"></i>Home <div class="arrow-down"></div></a>
						</li>
						<li class="has-submenu">
							<a href="hub.php" class="nav-link">
								<i class="fe-wifi-off"></i>Attack Hub <div class="arrow-down"></div></a>
							
						</li>					
						<li class="has-submenu">
							<a class="nav-link" href="plan.php">
								<i class="fe-shopping-cart"></i>Purchase <div class="arrow-down"></div></a>
							
						</li>
						<li class="has-submenu">
							<a class="nav-link" href="activate.php">
								<i class="fe-gift"></i>Activate Code <div class="arrow-down"></div></a>
							
						</li>
						<li class="has-submenu">
							<a class="nav-link" href="support.php">
								<i class="fe-mail"></i>Support <div class="arrow-down"></div></a>
							
						</li>
						<li class="has-submenu last-elements">
							<a class="nav-link" href="https://t.me/ScapyClub">
								<i class="ri-send-plane-fill"></i>Telegram <div class="arrow-down"></div></a>
							
						</li>
						<?php
				                     if ($user -> isAdmin($odb)){ 
	                                 ?>
						<li class="has-submenu last-elements">
							<a class="nav-link" href="admin/home.php">
								<i class="fe-settings"></i>Admin <div class="arrow-down"></div></a>
							
						</li><?php  }?>
						<li class="has-submenu last-elements">
							<a class="nav-link" href="logout.php">
								<i class="fe-log-out"></i>Exit <div class="arrow-down"></div></a>
							
						</li>
											</ul>
					<!-- End navigation menu -->

					<div class="clearfix"></div>
				</div>
				<!-- end #navigation -->
			</div>
			<!-- end container -->
		
		<!-- end navbar-custom -->
	</nav>
	<!-- End Navigation Bar-->
	<!-- ============================================================== -->
	<!-- Start Page Content here -->
	<!-- ============================================================== -->

	<!-- base js -->
	<script src="assets/js/vendor.min.js"></script>
	<script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
	<script src="assets/libs/peity/jquery.peity.min.js"></script>
	<!-- Sparkline charts -->
	<script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
	<!-- init js -->
	<script src="assets/js/pages/dashboard-1.init.js"></script>
	<!-- App js -->
	<script src="assets/js/app.min.js"></script>
	<script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
	<script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
	<script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
	<script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
	<script src="assets/libs/datatables/dataTables.buttons.min.js"></script>
	<script src="assets/libs/datatables/buttons.bootstrap4.min.js"></script>
	<script src="assets/libs/datatables/buttons.html5.min.js"></script>
	<script src="assets/libs/datatables/buttons.flash.min.js"></script>
	<script src="assets/libs/datatables/buttons.print.min.js"></script>
	<script src="assets/libs/datatables/dataTables.keyTable.min.js"></script>
	<script src="assets/libs/datatables/dataTables.select.min.js"></script>
	<script src="assets/libs/pdfmake/pdfmake.min.js"></script>
	<script src="assets/libs/pdfmake/vfs_fonts.js"></script>
	<!-- third party js ends -->
	<script src="assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
	<script src="assets/libs/switchery/switchery.min.js"></script>
	<script src="assets/libs/multiselect/jquery.multi-select.js"></script>
	<script src="assets/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
	<script src="assets/libs/select2/select2.min.js"></script>
	<script src="assets/libs/bootstrap-select/bootstrap-select.min.js"></script>
	<script src="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
	<script src="assets/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
	<!-- Sweet Alerts js -->
	<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

	<!-- Sweet alert init js-->
	<script src="assets/js/pages/sweet-alerts.init.js"></script>
	<!-- Datatables init -->
	<script src="assets/js/pages/datatables.init.js"></script>

	<!-- App js -->
	<script src="assets/js/app.min.js"></script>

	<div class="container-fluid">
<div class="row">
</div> <!-- end col -->
</div>
	<script src="assets/js/vendor.min.js"></script>
	<script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
	<script src="assets/libs/peity/jquery.peity.min.js"></script>
	<!-- Sparkline charts -->
	<script src="assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
	<!-- init js -->
	<script src="assets/js/pages/dashboard-1.init.js"></script>
	<!-- App js -->
	<script src="assets/js/app.min.js"></script>
</body></html>