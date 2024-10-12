<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="description" content="CleverAdmin - Bootstrap Admin Template">
		<meta name="author" content="Lukasz Holeczek">
		<meta name="keyword" content="CleverAdmin, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
	    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="../assets/ico/favicon.png">

	    <title><?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['bootername'];
				}
			?> - <?php echo $page ?></title>

	    <!-- Bootstrap core CSS -->
	    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- page css files -->
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet">
		<link href="../assets/css/jquery-ui.min.css" rel="stylesheet">
		<link href="../assets/css/fullcalendar.css" rel="stylesheet">
		<link href="../assets/css/morris.css" rel="stylesheet">
		<link href="../assets/css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
		<link href="../assets/css/climacons-font.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="../assets/css/style.min.css" rel="stylesheet">
	</head>
</head>

			<script src="../assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="../assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="../assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="../assets/js/jquery-ui.min.js"></script>
	<script src="../assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="../assets/js/jquery.sparkline.min.js"></script>
	<script src="../assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="../assets/js/jquery.flot.min.js"></script>
	<script src="../assets/js/jquery.flot.pie.min.js"></script>
	<script src="../assets/js/jquery.flot.stack.min.js"></script>
	<script src="../assets/js/jquery.flot.resize.min.js"></script>
	<script src="../assets/js/jquery.flot.time.min.js"></script>
	<script src="../assets/js/jquery.flot.spline.min.js"></script>
	<script src="../assets/js/jquery.autosize.min.js"></script>
	<script src="../assets/js/jquery.placeholder.min.js"></script>
	<script src="../assets/js/moment.min.js"></script>
	<script src="../assets/js/daterangepicker.min.js"></script>
	<script src="../assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="../assets/js/jquery.dataTables.min.js"></script>
	<script src="../assets/js/dataTables.bootstrap.min.js"></script>
	<script src="../assets/js/raphael.min.js"></script>
	<script src="../assets/js/morris.min.js"></script>
	<script src="../assets/js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="../assets/js/uncompressed/jquery-jvectormap-world-mill-en.js"></script>
	<script src="../assets/js/uncompressed/gdp-data.js"></script>
	<script src="../assets/js/gauge.min.js"></script>
	
	<!-- theme scripts -->
	<script src="../assets/js/custom.min.js"></script>
	<script src="../assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="../assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->