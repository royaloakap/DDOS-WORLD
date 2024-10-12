<?php

	include 'header.php';
	
	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Users <small>Login Logs</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>User Management</li>
					<li><a class="link-effect" href="logins.php">Logins</a></li>
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
						<h3 class="block-title">Login Logs</h3>
					</div>
					<div class="block-content">
						<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">IP</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Country</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `loginlogs` ORDER BY `date` DESC LIMIT 600");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$username = $getInfo['username'];
								$ip = $getInfo['ip'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								$country = $getInfo['country'];
								echo '<tr>
										<td></td>
										<td>'.htmlspecialchars($username).'</td>
										<td>'.htmlspecialchars($ip).'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($country).'</td>
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