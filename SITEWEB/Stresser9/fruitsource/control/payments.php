<?php
ob_start();
include '../controls/database.php';
if (!($user -> LoggedIn()))
{
	header('location: ../login.php');
	die();
}
if (!($user -> isAdmin($odb)))
{
	die('You are not admin');
}
if (!($user -> notBanned($odb)))
{
	header('location: ../login.php');
	die();
}
$page = "Payments";
include ("head.php");
$transPerPage = 100000;	
$page = 1;
if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']))
	$page = $_GET['page'];
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
							<h2><i class="fa fa-money"></i><span class="break"></span>Payment Logs</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								  <thead>
									 <tr>
                                                <th>User</td>
												<th>Plan</td>
                                                <th>Email / Address</td>
												<th>Payment Type</th>
                                                <th>Transaction ID</td>
                                                <th>Amount</td>
                                                <th>Date</td>
											</tr>
										</thead>
									<tbody>
										<?php
										$SQLGetLogs = $odb -> prepare("SELECT `payments`.* , `plans`.`name` AS `planname`, `users`.`username` FROM `payments` LEFT JOIN `plans` ON `payments`.`plan` = `plans`.`ID` LEFT JOIN `users` ON `payments`.`user` = `users`.`ID` ORDER BY `ID` DESC LIMIT :start, :max");
										$SQLGetLogs->bindValue(":start", ($page-1)*$transPerPage, PDO::PARAM_INT);
										$SQLGetLogs->bindValue(":max", ($transPerPage), PDO::PARAM_INT);
										$SQLGetLogs->execute();
										while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
										{
												$user =  htmlentities($getInfo['username']);
												$plan =  htmlentities($getInfo['planname']);
												$email =  htmlentities(($getInfo['type'] == "pp" || $getInfo['type'] == "stripe" ? $getInfo['email'] : ( $getInfo['type'] == "btc" ? $getInfo['btc_addr'] : "n/a")));
												$tid = htmlentities( $getInfo['tid']);
												$amount = htmlentities( $getInfo['paid']);
												$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
												echo '<tr class="spacer"></tr><tr><td><center>'.$user.'</center></td><td><center>'.$plan.'</center></td><td><center>'.$email.'</center></td><td>' . $getInfo['type'] . '</td><td><center>'.$tid.'</center></td><td><center>$'.$amount.'</center></td><td><center>'.$date.'</center></td></tr>';
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
	
	<?php include "../footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->
	
</body>
</html>