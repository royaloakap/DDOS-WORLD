<?php

	include 'header.php';
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Users <small>Attack Logs</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>User Management</li>
					<li><a class="link-effect" href="attacks.php">Attacks</a></li>
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
						<h3 class="block-title">Attack Logs</h3>
					</div>
					<div class="block-content">
						<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Host</th>
									<th style="font-size: 12px;">Time</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Handler</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetLogs = $odb -> query("SELECT * FROM `logs` ORDER BY `date` DESC LIMIT 600");
							while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
								$user = $getInfo['user'];
								$host = $getInfo['ip'];
								if (filter_var($host, FILTER_VALIDATE_URL)) {$port='';} else {$port=':'.$getInfo['port'];}
								$time = $getInfo['time'];
								$method = $getInfo['method'];
								$handler = $getInfo['handler'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								echo '<tr>
										<td>'.htmlspecialchars($user).'</td>
										<td>'.htmlspecialchars($host).$port.' ('.htmlspecialchars($method).')<br></td>
										<td>'.$time.'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($handler).'</td>
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