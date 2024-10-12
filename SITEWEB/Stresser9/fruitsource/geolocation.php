<?php
ob_start();
include 'controls/database.php';
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
if (!($user->hasMembership($odb)))
{
	header('location: purchase.php');
	die();
}
$page = "Geolocation";
include ("head.php");
?>
<body>
		<?php include "header.php"; ?>
	
		<div class="container-fluid content">
		<div class="row">
				
			<?php include "side.php"; ?>
			<!-- end: Main Menu -->
						
			<!-- start: Content -->
			<div class="col-md-10 col-sm-11 main ">
			
			
			
			<div class="row">
			<div class="col-md-6">
				<?php 
				  $ip = '';
				  if(isset($_POST['lookupBtn']))
				  {
				   $ip = $_POST['ipAddress'];
				   $ip = (filter_var($ip, FILTER_VALIDATE_IP)) ? $ip : $_SERVER['REMOTE_ADDR'];
				  }
				  else 
				  {
				   $ip = $_SERVER['REMOTE_ADDR'];
				  }
				  $xml = simplexml_load_file('http://api.ipinfodb.com/v3/ip-city/?key=a69c934a779933e7a2427abceb2568d1f81b1181daa428e4c0dd5e32277f5fb8&format=xml&ip='.$ip);

				  $ip = $xml->ipAddress;
				  $status = $xml->statusCode;
				  $country = $xml->countryName;
				  $region = $xml->regionName;
				  $city = $xml->cityName;
				  $latitude = $xml->latitude;
				  $longitude = $xml->longitude;
				  $timezone = $xml->timeZone;
				?>
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Geolocation</strong> Resolver</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">IP Address</label>
				                    <div class="col-md-9">
				                     	<input class="form-control" name="ipAddress" type="text" placeholder="127.0.0.1"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
		                    <button type="submit" name="lookupBtn" class="btn btn-sm btn-success"><i class="fa fa-globe"></i> LookUp</button>
		                </div>
						</form>
			        </div>
					</div>
					<div class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-align-justify"></i><span class="break"></span>Geolocation Results</h2>
						</div>
						<div class="panel-body">
							<table class="table table-bordered">
								  <thead>
								<tr>
									<td>IP Address:</td>
									<td><?php echo $ip;?></td>
								</tr>
								<tr>
									<td>Status:</td>
									<td><?php echo $status;?></td>
								</tr>
								<tr>
									<td>Country:</td>
									<td><?php echo $country;?></td>
								</tr>
								<tr>
									<td>Region:</td>
									<td><?php echo $region;?></td>
								</tr><tr>
									<td>City:</td>
									<td><?php echo $city;?></td>
								</tr><tr>
									<td>Latitude:</td>
									<td><?php echo $latitude;?></td>
								</tr><tr>
									<td>Longitude:</td>
									<td><?php echo $longitude;?></td>
								</tr><tr>
									<td>Timezone:</td>
									<td><?php echo $timezone;?></td>
								</tr>
								  </tbody>
							 </table>  
						</div>
					</div>
				</div>
			</div><!--/row-->
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="assets/js/jquery-2.1.0.min.js"></script>

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
	<script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/jquery.sparkline.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="assets/js/jquery.flot.min.js"></script>
	<script src="assets/js/jquery.flot.pie.min.js"></script>
	<script src="assets/js/jquery.flot.stack.min.js"></script>
	<script src="assets/js/jquery.flot.resize.min.js"></script>
	<script src="assets/js/jquery.flot.time.min.js"></script>
	<script src="assets/js/jquery.flot.spline.min.js"></script>
	<script src="assets/js/jquery.autosize.min.js"></script>
	<script src="assets/js/jquery.placeholder.min.js"></script>
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/daterangepicker.min.js"></script>
	<script src="assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap.min.js"></script>
	<script src="assets/js/raphael.min.js"></script>
	<script src="assets/js/morris.min.js"></script>
	<script src="assets/js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="assets/js/uncompressed/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/js/uncompressed/gdp-data.js"></script>
	<script src="assets/js/gauge.min.js"></script>
	
	<!-- theme scripts -->
	<script src="assets/js/custom.min.js"></script>
	<script src="assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->
	
</body>
</html>