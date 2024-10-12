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
					Tickets
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Settings</li>
					<li><a class="link-effect" href="support.php">Tickets</a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="content content-narrow">
		<div class="row">
			<div class="col-md-12">
				<div class="block">
					<div class="block-header bg-primary">
						<ul class="block-options">
							<li>
							</li>
						</ul>
						<h3 class="block-title">Tickets</h3>
					</div>
					<div class="block-content">
						<table class="table js-dataTable-full">
							<thead>
								<tr>									<th style="font-size: 12px;">ID</th>										
									<th style="font-size: 12px;">Username</th>
									<th style="font-size: 12px;">Subject</th>
									<th style="font-size: 12px;">Status</th>
									<th style="font-size: 12px;">Join</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` ORDER BY `id` DESC");							while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC)){									$id = $getInfo['id'];																		$username = $getInfo['username'];									$subject = $getInfo['subject'];									$status = $getInfo['status'];									$original = $getInfo['content'];								
								echo '<tr>																				<td>'.$id.'</td>										
										<td>'.$username.'</td>																				<td>'.$subject.'</td>																				<td>'.$status.'</td>
										<td><a class="link-effect" href="ticket.php?id='.$id.'">Click me <3</a></td>
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