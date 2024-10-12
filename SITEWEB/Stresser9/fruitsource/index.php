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
$page = "Dashboard";
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
				<?php
				  $SQLSelect = $odb -> query("SELECT * FROM `api` ORDER BY `ID` ASC");
				  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
				  {
					$rowID = $show['ID'];
				?>
				<?php
				  }
				  ?>
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="boxchart-overlay blue">
							<div class="boxchart">5,6,7,2,0,4,2,4,8,2,3,3,2</div>
						</div>
						<span class="value"><?php echo $rowID ?></span>	
						<span class="title">Total Servers Online</span>
							
					</div>
				</div><!--/col-->
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<i class="fa fa-laptop darkGreen"></i>
						<span class="value"><?php echo $stats -> totalUsers($odb); ?></span>	
						<span class="title">New Members</span>						
					</div>
				</div><!--/col-->

				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="linechart-overlay green">
							<div class="linechart">1,2,6,4,0,8,2,4,5,3,1,7,5</div>
						</div>
						<span class="value"><?php echo $stats -> totalBoots($odb); ?></span>	
						<span class="title">Total Stress</span>						
					</div>
				</div><!--/col-->
				
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="boxchart-overlay blue">
							<div class="boxchart">1,2,3,4,2,8,2,7,8,2,3,5,2</div>
						</div>
						<span class="value"><?php echo $stats -> runningBoots($odb); ?>/<?php echo $maxBootSlots;?></span>	
						<span class="title">Running Attacks</span>						
					</div>









				</div><!--/col-->

			</div><!--/row-->

			<div class="row">









				<div class="col-md-6">
				<?php
					$fetchNews = $odb->query("
						SELECT `n`.*, `u`.`username`
						FROM `news` AS `n`
						LEFT JOIN `users` AS `u`
						ON `u`.`id` = `n`.`author_id`
						ORDER BY `date` DESC
						LIMIT 3;
					");
					if ($fetchNews->rowCount() != 0)
					{
						while ($row = $fetchNews->fetch(PDO::FETCH_ASSOC))
						{
				?>

					<div class="panel panel-default">
						<div class="panel-body no-padding">
							<ul class="comments-list">
								<li>
									<a href="widgets.html#">
										<img class="avatar" alt="Lucas" src="assets/img/avatar.jpg">
									</a>
									<div>
										<strong><?php echo htmlentities($row['username']); ?></strong> - <?php echo timeElapsedFromUNIX($row['date']); ?>
									</div>
									<div class="date"><font size="2"><?php echo nl2br(htmlentities($row['detail'])); ?></font></div></br>
								</li>
							</ul>										
						</div>	
					</div>	

<?php
		}
?>

<?php
	} else {
		echo '<div class="alert alert-error">There is no latest news!</div>';
	}
?>
					
						

					</div> 
				
				<div class="col-md-6">
					<div class="panel panel-default">
						
						<div class="panel-body padding-horizontal">
							<h3><i class="fa fa-trophy"></i> Welcome to <?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['bootername'];
				}
			?></h3>
							<p><small><?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['wmsg'];
				}
			?></small></p>
							</br/>	
						</div>
					</div>	






				</div><!--/col-->	
				<?php
			if ($user -> isAdmin($odb))
			{
			?>
			<div class="col-md-6">
				<?php 
				if (isset($_POST['addBtn']))
				{
					$detailAdd = $_POST['detailAdd'];
					if (!empty($detailAdd))
					{
						$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL, :uid, :detail, UNIX_TIMESTAMP())");
						$SQLinsert -> execute(array(':detail' => $detailAdd, ":uid" => $_SESSION['ID']));
						echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>:You have added your news to the Dashboard! </div></div>';
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Please fill in all fields</div></div>';
					}
				}
				?>
				<?php
						if (isset($_POST['deleteBtn']))
						{
							$deletes = $_POST['deleteCheck'];
							foreach($deletes as $delete)
							{
								$SQL = $odb -> prepare("DELETE FROM `news` WHERE `ID` = :id LIMIT 1");
								$SQL -> execute(array(':id' => $delete));
							}
							echo '<div class="g_12"><div class="alert alert-success">New(s) Have been removed</div></div>';
						}
						?>
			<div class="panel panel-default">
			<form method="POST">
					  	<div class="panel-body">
					    	<textarea class="form-control" name="detailAdd" placeholder="Provide as much information to FruitStress Clients about what going on!"></textarea>
					  	</div>
						<div class="panel-footer">
							<div class="btn-group">
								<button type="button" class="btn btn-link"><i class="fa fa-news"></i></button>
							</div>
							
							<div class="pull-right">
								<button type="submit" name="addBtn" class="btn btn-success">submit</button>
							</div>	
							</form>
						</div>
					</div>
					<?php
					}
					?>
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