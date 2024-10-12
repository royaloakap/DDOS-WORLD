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
							<h2><i class="fa fa-ticket"></i><span class="break"></span>Support Center</h2>
						</div>
						<div class="panel-body">
							<table class="table table-striped">
								  <thead>
									 <tr>

										<th>
ID
										</th>
										
										<th>Username</th>

										<th>Subject</th>
										
										<th>Department</th>

										<th>Status</th>

										<th>Actions</th>

									</tr>

								</thead>

								<tbody>

									<?php 
										$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` ORDER BY `id` DESC");
										
										while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
										{
											$id = $getInfo['id'];
															$user = $getInfo['username'];
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
												echo '<tr><td> '.$id.'</td><td>'.$user.'</td><td>'.$subject.'</td><td>'.$department.'</td><td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a></td></tr>';
												
												}
												else{
												 echo '<tr><td> '.$id.'</td><td>'.$user.'</td><td>'.$subject.'</td><td>'.$department.'<td>'.$status.'</td><td><a href="ticket.php?id='.$id.'">Full Ticket</a></td></tr>';
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
	
	<?php include "../footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->
	
</body>
</html>