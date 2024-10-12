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
$page = "Customer Accounts";
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
							<h2><i class="fa fa-group"></i><span class="break"></span>Customer Accounts</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								  <thead>
									 <tr>
									<th>ID</th>
									<th>Username</th>
									<th>Email</th>
									<th>Rank</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
								while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['ID'];
									$user = $getInfo['username'];
									$email = $getInfo['email'];
									$rank = ($getInfo['rank'] == 1) ? 'Admin' : 'Member';
									echo '<tr><td>'.$id.'</td><td><a href="edituser.php?id='.$id.'">'.$user.'</a></td><td>'.$email.'</td><td>'.$rank.'</td></tr>';
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