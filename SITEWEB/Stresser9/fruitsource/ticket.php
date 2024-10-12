<?php
ob_start();
require_once 'controls/database.php';
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
$id = ($_GET['id']);
if(is_numeric($id) == false) {
echo "lol";
exit;
}
$page = "Reply Ticket";
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
				<?php
				   $SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = $id");
				   while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
				   {
					$username = $getInfo['username'];
					$subject = $getInfo['subject'];
					$status = $getInfo['status'];
					$original = $getInfo['content'];
					}
					if ($username != $_SESSION['username']) {
					echo "Invalid ticket ID!";
					exit;
					}
					if ($status == "Closed") {
					echo "Ticket is closed!";
					exit;
					}
						   if (isset($_POST['closeBtn']))
						   {
					$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
					$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
					echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Ticket has been closed.  Redirecting....</div></div><meta http-equiv="refresh" content="3;url=support.php">';
						   }
				?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-info"></i><span class="break"></span>Ticket Info</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Department</th>
									
									  	<th>Subject</th>

										<th>Details</th>
										
										<th>Action</th>

									</tr>

								</thead>

								<tbody>
                    <?php
					   $SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = $id");
					   while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
					   {
						$username = $getInfo['username'];
						$subject = $getInfo['subject'];
						$department = $getInfo['department'];
						
						$original = $getInfo['content'];

						if($status == "Closed")
						{
						echo '<tr><td>'.$department.'</td><td>'.$subject.'</td><td>'.htmlentities($original).'</div></td><td>Closed</td></tr>';
						}else{
								echo '<tr><td>'.$department.'</td><td>'.$subject.'</td><td>'.htmlentities($original).'</div></td><td><form method="POST"><input type="submit" name="closeBtn" value="Close" class="btn btn-danger" /></form></td></tr>';
								}
						}

					?>
							</td>
							</tbody>
						</table>
					</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-info"></i><span class="break"></span>Ticket Info</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								<thead>
									<tr>
									  		<th>Chat</th>
									</tr>
									  	</thead>
									  	<tbody>
								<?php 
			$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");
			$SQLGetMessages -> execute(array(':ticketid' => $id));
			while ($getInfo = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC))
			{
				$sender = $getInfo['sender'];
				$content = $getInfo['content'];
				if ($sender != "Admin") { $li = "in"; } else { $li = "out"; }
                                  echo  '<tr><td><span class="left">
                                                '.$sender.' | </span>
                                           <span class="right">
                                           '.htmlspecialchars($content).'</span></td></tr>
                                       ';
                                       }
			?>
								</tbody>
						</table>
					</div>
					</div>
				</div>
				<div class="col-md-6">
				<?php 
				if (isset($_POST['updateBtn']))
					   {
						$updatecontent = $_POST['content'];

							$errors = array();
							if (empty($updatecontent))
							{
								$errors[] = 'Fill in all fields';
							}
							if (empty($errors))
							{
								$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender)");
								$SQLinsert -> execute(array(':sender' => 'Client', ':content' => $updatecontent, ':ticketid' => $id));
							{
								$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
								$SQLUpdate -> execute(array(':status' => 'Waiting for admin response', ':id' => $id));
								echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Ticket has been updated.  Redirecting....</div></div><meta http-equiv="refresh" content="3;url=support.php">';
							}
							}
							else
							{
								echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: ';
								foreach($errors as $error)
								{
									echo '-'.$error.'<br />';
								}
								echo '</div></div>';
							}
						}
				?>						
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Reply</strong> Ticket</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
				                <div class="form-group">
				                    <div class="col-md-12">
				                        <textarea row="6" style="width: 100%; height: 150px;" class="form-control" name="content"></textarea>
				                    </div>
				                </div>
						</div>
						<div class="panel-footer">
		                    <button type="submit" name="updateBtn" class="btn btn-sm btn-success"><i class="fa fa-ticket"></i> Submit Reply</button>
		                </div>
						</form>
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