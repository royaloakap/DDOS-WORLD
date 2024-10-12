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
$page = "New Ticket";
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
	   if (isset($_POST['submitTicket']))
	  {
			$subject = htmlentities($_POST['subject']);
			$content = htmlentities($_POST['content']);
			$department = htmlentities($_POST['department']);
			$errors = array();

			if (empty($subject) || empty($content))
			{
				echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Fill in all fields!</div></div><meta http-equiv="refresh" content="3;url=support.php">';
				exit;
			}
			if (empty($errors))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `tickets` VALUES(NULL, :subject, :content, :status, :username, :department)");
				$SQLinsert -> execute(array(':subject' => $subject, ':content' => $content, ':status' => 'Waiting for Staff response.', ':username' => $_SESSION['username'], ':department' => $department));
				echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: Ticket has been created.</div></div>';
			}
			}

	   ?>
					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Open Ticket</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
							<div class="form-group">
								<div class="col-sm-12">
									<select class="form-control" name="department">
									  <optgroup>
										  <option value="0">Select Department</option>
										  <option value="Sales">Sales</option>
										  <option value="Technical Support">Technical Support</option>
										  <option value="Account">Account</option>
									  </optgroup>
									</select>
									</div>
								</div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" placeholder="Subject" name="subject">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
										<textarea class="form-control" rows="6" name="content" style="width: 100%; height: 120px;"></textarea>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
							<button type="submit" name="submitTicket" class="btn btn-sm btn-success"><i class="fa fa-ticket"></i> Submit</button>
						</div>	
						</form>
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