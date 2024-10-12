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
$page = "Friends & Enemies";
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
				if (isset($_POST['addBtn']))
				{
					$ipAdd = $_POST['ipAdd'];
					$noteAdd = $_POST['noteAdd'];
					$type = $_POST['type'];
					if (empty($ipAdd) || empty($type))
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Please fill in all fields</div></div>';
					}
					else
					{
						if (!filter_var($ipAdd, FILTER_VALIDATE_IP))
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: IP is invalid</div></div>';
						}
						else
						{
							$SQLinsert = $odb -> prepare("INSERT INTO `fe` VALUES(NULL, :userID, :type, :ip, :note)");
							$SQLinsert -> execute(array(':userID' => $_SESSION['ID'], ':type' => $type, ':ip' => $ipAdd, ':note' => $noteAdd));
							echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: IP has been added</div></div>';
						}
					}
				}
				?>
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Friends</strong> & Enemies</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Host</label>
				                    <div class="col-md-9">
				                     	<input class="form-control" name="ipAdd" maxlength="15" placeholder="127.0.0.1" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Notes</label>
				                    <div class="col-md-9">
				                     	<textarea class="form-control" name="noteAdd" style="resize:none;"></textarea>
				                    </div>
				                </div>
								<div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Host</label>
				                    <div class="col-md-9">
				                     	<select class="form-control" name="type">
										<option value="f" selected="selected" />Friend
										<option value="e" />Enemy
									</select>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
		                    <button type="submit" name="addBtn" class="btn btn-sm btn-success"><i class="fa fa-user"></i> Add</button>
		                </div>
						</form>
			        </div>
					</div>
					<div class="col-lg-6">
					<?php
						if (isset($_POST['deleteBtn']))
						{
							$deletes = $_POST['deleteCheck'];
							if (!empty($deletes))
							{
								foreach($deletes as $delete)
								{
									$SQL = $odb -> prepare("DELETE FROM `fe` WHERE `ID` = :id AND `userID` = :uid LIMIT 1");
									$SQL -> execute(array(':id' => $delete, ':uid' => $_SESSION['ID']));
								}
								echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: IP(s) Have been removed</div></div>';
							}
						}
						?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-align-justify"></i><span class="break"></span>Friends/Enemies</h2>
						</div>
						<div class="panel-body">
						<form method="POST">
						<input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger" />
							<table class="table table-bordered">
							<thead>
								 <tr>
										<th>
											<input type="checkbox" class="simple_form tMainC" />
										</th>
										<th>IP</th>
										<th>Type</th>
										<th>Note</th>
									</tr>
								</thead>
								<tbody>
									<?php
									  $SQLSelect = $odb -> prepare("SELECT * FROM `fe` WHERE `userID` = :user ORDER BY `ID` DESC");
									  $SQLSelect -> execute(array(':user' => $_SESSION['ID']));
									  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
									  {
										$ipShow = $show['ip'];
										$noteShow = $show['note'];
										$rowID = $show['ID'];
										$type = ($show['type'] == 'f') ? 'Friend' : 'Enemy';
										echo '<tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td>'.$ipShow.'</td><td>'.$type.'</td><td>'.htmlentities($noteShow).'</td></tr>';
									  }
									  ?>
								</tbody>
							 </table>  
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