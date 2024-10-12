<?php

	include 'header.php';
	
	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	if(isset($_GET['add'])){
		
		if($_GET['add'] == "day"){
			$time = (60 * 60 * 24);
			$log = "1 Days";
		}
		
		$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
		while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
			$newTime = $getInfo['expire'] + $time;
			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `expire` = ? WHERE `ID` = ?");
			$SQLUpdate -> execute(array($newTime, $getInfo['ID']));
		}
		
		$username = $_SESSION['username'];
		$action = 'Updated everyone for ' . $log;
		$time = time();
		
		$SQLLog = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, ?, 'Everyone', ?, ?)");
		$SQLLog -> execute(array($username, $action, $time));
		$notify = success($log .' has been added on everyones account');
		
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Users <small>Manage Users</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>User Management</li>
					<li><a class="link-effect" href="users.php">Users</a></li>
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
						<ul class="block-options">
							<li>
								<button style="color: white;" type="button"><i class="fa fa-plus"></i> Add <a href="users.php?add=day" style="color: white;">1 Day</a> or <a href="users.php?add=3day" style="color: white;">3 Days</a> or <a href="users.php?add=week" style="color: white;">1 Week</a> On Everyone's Plan</button>
							</li>
						</ul>
						<h3 class="block-title">Manage Users</h3>
					</div>
					<div class="block-content">
						<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Email</th>
									<th style="font-size: 12px;">Rank</th>
									<th style="font-size: 12px;">Membership</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$id = $getInfo['ID'];
								$user = $getInfo['username'];
								$email = $getInfo['email'];
								if ($getInfo['expire']>time()) {$plan = $odb -> query("SELECT `plans`.`name` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = '$id'") -> fetchColumn(0);} else {$plan='No membership';}
								$rank = $getInfo['rank'];
								$membership = $getInfo['membership'];
								$status = $getInfo['status'];	
								$expire = $getInfo['expire'];
									if ($rank == 1)
									{
										$rank = 'Administrator';
									}
									elseif ($rank == 2)
									{
										$rank = 'Supporter';
									}
									else
									{
										$rank = 'Member';
									}
								echo '<tr>
										<td></td>
										<td><a class="link-effect" href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td>
										<td>'.htmlspecialchars($email).'</td>
										<td>'.$rank.'</td>
										<td>'.htmlspecialchars($plan).'</td>
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