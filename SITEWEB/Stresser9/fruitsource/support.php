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
$page = "Support Center";
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
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-align-justify"></i><span class="break"></span>Current Tickets</h2>
						</div>
						<div class="panel-body">
						<a href="newticket.php"><button type="submit" class="btn btn-success">Open Ticket</button></a>
							<table class="table table-striped">
								  <thead>
									 <tr>

										<th>Subject</th>
										
										<th>Department</th>

										<th>Status</th>

										<th>Actions</th>
										

									</tr>

								</thead>

								<tbody>

									<?php 
										$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = :username ORDER BY `id` DESC");
										$SQLGetTickets -> execute(array(':username' => $_SESSION['username']));
										while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
										{
											$id = $getInfo['id'];
											$user = $_SESSION['username'];
											$subject = $getInfo['subject'];
											$status = $getInfo['status'];
											$department = $getInfo['department'];

																	if ($status == "Closed") {
												$status1 = '<i class="fa fa-star-half-empty"></i>';
												} elseif ($status == "Waiting for Staff response.") {
												$status1 = '<i class="fa fa-star"></i>';
												} elseif ($status == "Waiting for member response.") {
												$status1 = '<i class="fa fa-star-o"></i>';
										
												
												
												} else {
												$membership = 'Undefined';
												}
												if ($status == "Closed")
												{
												echo '<tr><td>'.$subject.'</td><td>'.$department.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'"><button class="btn btn-warning">Full Ticket</button></a></td></tr>';
												
												}
												else{
												 echo '<tr><td>'.$subject.'</td><td>'.$department.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'"><button class="btn btn-warning">Full Ticket</button></a></td></tr>';
												 }
																				}
										?>
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
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/57b8ab1c0934485f5bf65f95/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>