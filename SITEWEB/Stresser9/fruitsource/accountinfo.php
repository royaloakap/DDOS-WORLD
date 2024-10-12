<?php 
include "controls/database.php";
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
$page = "Account Information";
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
				if (isset($_POST['updatePassBtn']))
				{
					$cpassword = $_POST['cpassword'];
					$npassword = $_POST['npassword'];
					$rpassword = $_POST['rpassword'];
					if (!empty($cpassword) && !empty($npassword) && !empty($rpassword))
					{
						if ($npassword == $rpassword)
						{
							$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
							$SQLCheckCurrent -> execute(array(':username' => $_SESSION['username'], ':password' => SHA1($cpassword)));
							$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
							if ($countCurrent == 1)
							{
								$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
								$SQLUpdate -> execute(array(':password' => SHA1($npassword),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
								echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Password has been updated</div></div>';
							}
							else
							{
								echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Current Password is incorrect</div></div>';
							}
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: New passwords did not match</div></div>';
						}
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Please fill in all fields</div></div>';
					}
				}
				if (isset($_POST['updateEmailBtn']))
				{
					$cpassword = $_POST['cpassword'];
					$nemail = $_POST['nemail'];
					if (!empty($cpassword) && !empty($nemail))
					{
						if (filter_var($nemail, FILTER_VALIDATE_EMAIL))
						{
							$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
							$SQLCheckCurrent -> execute(array(':username' => $_SESSION['username'], ':password' => SHA1($cpassword)));
							$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
							if ($countCurrent == 1)
							{
								$SQLUpdate = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `username` = :username AND `ID` = :id");
								$SQLUpdate -> execute(array(':email' => $nemail,':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
								echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Email has been updated</div></div>';
							}
							else
							{
								echo '<div class="g_12"><div class="alert alert-danger">ERROR: Current password is incorrect</div></div>';
							}
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Invalid email</div></div>';
						}
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Please fill in all fields</div></div>';
					}
				}
				?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-lock"></i><span class="break"></span>Update Information</h2>
							<ul class="nav tab-menu nav-tabs" id="myTab">
								<li class=""><a href="ui-elements.html#info">Password</a></li>
								<li class=""><a href="ui-elements.html#custom">Email</a></li>
							</ul>
						</div>
						<div class="panel-body">
							
							<div id="myTabContent" class="tab-content">
								<div class="tab-pane" id="info">
							<div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Current Password</label>
				                    <div class="col-md-9">
				                        <input type="password" id="hf-password" name="cpassword" class="form-control" placeholder="Current Password">
				                        <span class="help-block">Please enter your password</span>
				                    </div>
				                </div>
								 <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">New Password</label>
				                    <div class="col-md-9">
				                        <input type="password" id="hf-password" name="npassword" class="form-control" placeholder="New Password">
				                        <span class="help-block">Please enter your password</span>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-password">Repeat Password</label>
				                    <div class="col-md-9">
				                        <input type="password" id="hf-password" name="rpassword" class="form-control" placeholder="Repeat Password">
				                        <span class="help-block">Please enter your password</span>
				                    </div>
				                </div>
						</div>
						<div class="panel-footer">
		                    <button type="submit" name="updatePassBtn" class="btn btn-sm btn-success"><i class="fa fa-lock"></i> Submit</button>
							</form>
		                </div>

								</div>
								<div class="tab-pane" id="custom">
									<div class="panel-body">
							<form action="" method="POST" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">New Email</label>
				                    <div class="col-md-9">
				                        <input type="email" id="hf-email" name="email" class="form-control" placeholder="Enter Email..">
				                        <span class="help-block">Please enter your email</span>
				                    </div>
				                </div>
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-password">Current Password</label>
				                    <div class="col-md-9">
				                        <input type="password" id="hf-password" name="cpassword" class="form-control" placeholder="Enter Password..">
				                        <span class="help-block">Please enter your password</span>
				                    </div>
				                </div>
						</div>
						<div class="panel-footer">
		                    <button type="submit" name="updateEmailBtn" class="btn btn-sm btn-success"><i class="fa fa-envelope"></i> Update</button>
		                </div>
							</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
				<?php 
					$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`, `plans`.`con` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
					$plansql -> execute(array(":id" => $_SESSION['ID']));
					$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);
				?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-align-justify"></i><span class="break"></span>Memebership Information</h2>
							<div class="panel-actions">
								<a href="table.html#" class="btn-setting"><i class="fa fa-wrench"></i></a>
								<a href="table.html#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
								<a href="table.html#" class="btn-close"><i class="fa fa-times"></i></a>
							</div>
						</div>
						<div class="panel-body">
							<table class="table table-bordered">
									<tr>
									<td>Username:</td>
									<td><?php echo $userInfo['username']; ?></td>
								</tr>
								<tr>
									<td>Email:</td>
									<td><?php echo $userInfo['email']; ?></td>
								</tr>
								<tr>
									<td>Membership:</td>
									<td><?php echo $userInfo['name']; ?></td>
								</tr>
								<tr>
									<td>Max Boot:</td>
									<td><?php echo $userInfo['mbt']; ?></td>
								</tr>
								<tr>
									<td>Concurrents:</td>
									<td><?php echo $userInfo['con']; ?></td>
								</tr>
								<tr>
									<td>Expire:</td>
									<td><?php echo date('m-d-Y' ,$userInfo['expire']); ?></td>
								</tr>                                   
								  </tbody>
							 </table>  
						</div>
					</div>
				</div>
				</div><!--/col-->	
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