<?php

	include 'header.php';
	
	if(!$user->isAdmin($odb)){
		header('Location: home.php');
		exit;
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Users <small>Payment Logs</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>User Management</li>
					<li><a class="link-effect" href="payments.php">Payments</a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="content content-narrow">
		<?php
		if(isset($notify)){
			echo '<div class="row col-md-12">' . $notify . "</div>";
		}
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Payment Logs</h3>
					</div>
					<div class="block-content">
						<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Plan</th>
									<th style="font-size: 12px;">Email</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Transaction ID</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetLogs = $odb -> query("SELECT `payments`.* , `plans`.`name` AS `planname`, `users`.`username` FROM `payments` LEFT JOIN `plans` ON `payments`.`plan` = `plans`.`ID` LEFT JOIN `users` ON `payments`.`user` = `users`.`ID` ORDER BY `payments`.`date` DESC");
							while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
								$user = $getInfo['username'];
								$plan = $getInfo['planname'];
								$email = $getInfo['email'];
								$tid = $getInfo['tid'];
								$amount = $getInfo['paid'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								echo '<tr">
										<td>'.htmlspecialchars($user).'</td>
										<td>'.htmlspecialchars($plan).' ($'.$amount.')</td>
										<td>'.htmlspecialchars($email).'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($tid).'</td>
									  </tr>';
							}
							?>	
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>     
	</div>
</main>
<?php

	include 'footer.php';

?>