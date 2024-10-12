<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	// Methods
	if (isset($_POST['delete'])){
		$delete = $_POST['delete'];
		$SQL = $odb -> prepare("DELETE FROM `methods` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $delete));
		$notify = success('The method has been deleted');
	}
	
	if (isset($_POST['addmethod'])){
		if (empty($_POST['name']) || empty($_POST['fullname']) || empty($_POST['type'])){
			$notify = error('Please verify all fields');
		}
		else{
			$name = $_POST['name'];
			$fullname = $_POST['fullname'];
			$type = $_POST['type'];
			if ($system=='servers') {$command = $_POST['command'];} else {$command = '';}
			$SQLinsert = $odb -> prepare("INSERT INTO `methods` VALUES(NULL, :name, :fullname, :type, :command)");
			$SQLinsert -> execute(array(':name' => $name, ':fullname' => $fullname, ':type' => $type, ':command' => $command));
			$notify = success('Method has been added');
		}
	}	
	
	// API/Server 
	if (isset($_POST['deleteapi'])){
		$delete = $_POST['deleteapi'];
		$SQL = $odb -> prepare("DELETE FROM `api` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $delete));
		$notify = success('API has been removed');
	}
	
	if (isset($_POST['deleteserver'])){
		$delete = $_POST['deleteserver'];
		$SQL = $odb -> prepare("DELETE FROM `servers` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $delete));
		$notify = success('Server has been removed');
	}
	
	if (isset($_POST['addapi'])){
		
		if (empty($_POST['api']) || empty($_POST['name']) || empty($_POST['slots']) || empty($_POST['methods'])){
			$error = 'Please verify all fields';
		}
		
		$api = $_POST['api'];
		$name = $_POST['name'];
		$slots = $_POST['slots'];
		$network = $_POST['network'];
		$methods = implode(" ",$_POST['methods']);
		
		if (!(is_numeric($slots))){
			$error = 'Slots field has to be numeric';
		}
		
		$parameters = array("[host]", "[port]", "[time]", "[method]");
		foreach ($parameters as $parameter){
			if (strpos($api,$parameter) == false){
				$error = 'Could not find parameter "'.$parameter.'"';
			}
		}
		
		if (empty($error)){
			$SQLinsert = $odb -> prepare("INSERT INTO `api` VALUES(NULL, :name, :api, :slots, :methods, :type, 1)");
			$SQLinsert -> execute(array(':api' => $api, ':name' => $name, ':slots' => $slots, ':methods' => $methods, ':type' => $network));
			$notify = success('API has been added');
		}
		else{
			$notify = error($error);
		}
	}
	
	if (isset($_POST['addserver'])){
		
		if (empty($_POST['ip']) || empty($_POST['password']) || empty($_POST['name']) || empty($_POST['slots']) || empty($_POST['methods'])){
			$error = 'Please verify all fields';
		}
		
		$name = $_POST['name'];
		$ip = $_POST['ip'];
		$password = $_POST['password'];
		$slots = $_POST['slots'];
		$methods = implode(" ",$_POST['methods']);
		
		if (!(is_numeric($slots))){
			$error = 'Slots field has to be numeric';
		}
		
		if (!filter_var($ip, FILTER_VALIDATE_IP)){
			$error = 'IP is invalid';
		}
		
		if(!ctype_alnum(str_replace(' ','',$name)) || !ctype_alnum(str_replace(' ','',$methods))){
			$error = 'Invalid characters in the name or commands field';
		}
		
		if (empty($error)){
			$SQLinsert = $odb -> prepare("INSERT INTO `servers` VALUES(NULL, :name, :ip, :password, :slots, :methods)");
			$SQLinsert -> execute(array(':name' => $name, ':ip' => $ip, ':password' => $password, ':slots' => $slots, ':methods' => $methods));
			$notify = success('Server has been added');
		}
		else{
			$notify = error($error);
		}
	}
	
	// Blacklist
	if (isset($_POST['deleteblacklist'])){
		$delete = $_POST['deleteblacklist'];
		$SQL = $odb -> query("DELETE FROM `blacklist` WHERE `ID` = '$delete'");
		$notify = success('Blacklist has been removed');
	}
	
	if (isset($_POST['addblacklist'])){
	
		if (empty($_POST['value'])){
			$error = 'Please verify all fields';
		}

		$value = $_POST['value'];
		$type = $_POST['type'];

		if (empty($error)){	
			$SQLinsert = $odb -> prepare("INSERT INTO `blacklist` VALUES(NULL, :value, :type)");
			$SQLinsert -> execute(array(':value' => $value, ':type' => $type));
			$notify = success('Blacklist has been added');
		}
		else{
			$notify = error($error);
		}
	}
	
	if (isset($_POST['statushub'])){
	
		

		$hub_reason = $_POST['hub_reason'];
		$hub_status = $_POST['hub_status'];
		
		$hub_time = $_POST['hub_time'];
		
		$hub_x = time() + $hub_time;

		if (empty($error)){
			if($hub_time == ""){ $hub_time = "600"; }
			if($hub_status == 1){ $hub_reason = ""; }
			if($hub_status == 1){ $hub_time = ""; }
			$SQLinsert = $odb -> prepare("UPDATE `settings` SET `hub_status` = :hub_status, `hub_reason` = :hub_reason, `hub_time` = :hub_time, `hub_rtime` = UNIX_TIMESTAMP() WHERE 1");
			$SQLinsert -> execute(array(':hub_status' => $hub_status, ':hub_reason' => $hub_reason, ':hub_time' => $hub_time));
			$notify = success('Hub have been updated');
			if($hub_status == 0)
			{
				$hubb = 'Disabled';
				$hubbb = '-'.$hub_reason.' '.$hub_time.' seconds';
			} else {
				$hubb = 'Enable';
				$hubbb = '';
			}
			
			$username = $_SESSION['username'];
			$time = time();
		
			$action = ''.$hubb.' Hub '.$hubbb.'';
			$SQLLog = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, ?, 'Hub', ?, ?)");
			$SQLLog -> execute(array($username, $action, $time));
			
		}
		else{
			$notify = error($error);
		}
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Settings <small>Stresser settings</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Settings</li>
					<li><a class="link-effect" href="hub.php">Stresser</a></li>
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
			<div class="col-md-4">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Method Manager</h3>
					</div>
					<table class="table">
						<tr>
							<th style="font-size: 12px;">Name</th>
							<th style="font-size: 12px;">Tag</th>
							<th style="font-size: 12px;">Type</th>
							<?php
							if($system == 'servers'){
								echo '<th style="font-size: 12px;">command</th>';
							}
							?>
							<th style="font-size: 12px;">Delete</th>
						</tr>
						<tr>
							<form method="post">
								<?php
								$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['id'];
									$name = $getInfo['name'];
									$fullname = $getInfo['fullname'];
									$type = $getInfo['type'];
									if ($system == 'servers') {$command = '<td style="font-size: 12px;">'.$getInfo['command'].'</td>';} else {$command = '';}
									echo '<tr>
											<td style="font-size: 12px;">'.htmlspecialchars($name).'</td>
											<td style="font-size: 12px;">'.htmlspecialchars($fullname).'</td>
											<td style="font-size: 12px;">'.$type.'</td>
											'.$command.'
											<td style="font-size: 12px;"><button name="delete" value="'.$id.'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>
										</tr>';
								}
								if(empty($SQLGetMethods)){
									echo error('No methods');
								}
								?>
							</form>
						</tr>                                       
					</table>
				</div>
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Add New Method</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="name" name="name">
										<label for="name">Name</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="fname" name="fullname">
										<label for="fname">Tag Name</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="attacktype" name="type" size="1">
											<option value="udp">UDP Amplication</option>
											<option value="tcp">TCP Amplication</option>
											<option value="layer7">Layer 7</option>
										</select>
										<label for="attacktype">Layer Type</label>
									</div>
								</div>
							</div>
							<?php if($system == "server"){ ?>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="command" name="command">
										<label for="command">Command</label>
									</div>
								</div>
							</div>
							<?php } ?>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addmethod" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Blacklist Manager</h3>
					</div>
					<table class="table">
						<tr>
							<th style="font-size: 12px;">Value</th>
							<th style="font-size: 12px;">Type</th>
							<th style="font-size: 12px;">Delete</th>
						</tr>
						<tr>
							<form method="post">
								<?php
								$SQLGetMethods = $odb -> query("SELECT * FROM `blacklist`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['ID'];
									$value = $getInfo['data'];
									$type = $getInfo['type'];
									echo '<tr>
											<td style="font-size: 12px;">'.htmlspecialchars($value).'</td>
											<td style="font-size: 12px;">'.htmlspecialchars($type).'</td>
											<td style="font-size: 12px;"><button name="deleteblacklist" value="'.$id.'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>
										</tr>';
								}
								if(empty($SQLGetMethods)){
									echo error('No Blacklists');
								}
								?>
							</form>
						</tr>                                       
					</table>
				</div>
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Add Blacklist</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="name" name="value">
										<label for="name">Value</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="type" name="type" size="1">
											<option value="victim">Host</option>
											<option value="skype">Skype</option>
										</select>
										<label for="type">Type</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addblacklist" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">HUB Status</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
						<?php if($hub_status == 0){
							
						?>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" value="<?php echo $hub_reason; ?>" name="hub_reason">
										<label for="name">Reason</label>
									</div>
								</div>
							</div> 
							
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" value="<?php echo $hub_time; ?>" name="hub_time">
										<label for="name">Time (Seconds)</label>
									</div>
								</div>
							</div> 
						<?php } ?>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" name="hub_status" size="1">
											<option value="0" <?php if($hub_status == 0){ echo 'selected'; } ?>>Disabled</option>
											<option value="1" <?php if($hub_status == 1){ echo 'selected'; } ?>>Enabled</option>
										</select>
										<label for="type">Status</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="statushub" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title"><?php if ($system == 'api') { echo 'API'; } else { echo 'Server'; } ?> Manager</h3>
					</div>
					<table class="table">
						<tr>
						<?php if ($system == 'api') { ?>
							<th style="font-size: 12px;" width="15%">Name</th>
							<th style="font-size: 12px;" width="20%">API URL</th>
							<th style="font-size: 12px;">Slots</th>
							<th style="font-size: 12px;">Methods</th>
							<th style="font-size: 12px;">Delete</th>
						<?php } else { ?>
							<th style="font-size: 12px;">Name</th>
							<th style="font-size: 12px;">IP</th>
							<th style="font-size: 12px;">Slots</th>
							<th style="font-size: 12px;">Methods</th>
							<th style="font-size: 12px;">Delete</th>
						<?php } ?>
						</tr>
						<tr>
							<form method="post">
							<?php
							if ($system == 'api') {
								$SQLGetMethods = $odb -> query("SELECT * FROM `api` ORDER BY `name` ASC");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['id'];
									$api = $getInfo['api'];
									$name = $getInfo['name'];
									$slots = $getInfo['slots'];
									$methods = $getInfo['methods'];
									 
									echo '									<tr>
										<td style="font-size: 12px;"><a href="edit-api.php?id='.$id.'">'.htmlspecialchars($name).'</a></td>
										<td style="font-size: 12px;" width="20%">'.htmlspecialchars($api).'</td>
										<td style="font-size: 12px;">'.htmlspecialchars($slots).'</td>
										<td style="font-size: 12px;">'.htmlspecialchars($methods).'</td>
										<td style="font-size: 12px;"><button type="submit" title="Delete API" name="deleteapi" value="'.htmlspecialchars($id).'" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>
									</tr>';
								}
							} else {
								$SQLGetMethods = $odb -> query("SELECT * FROM `servers`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									 $name = $getInfo['name'];
									 $id = $getInfo['id']; 
									 $ip = $getInfo['ip'];
									 $slots = $getInfo['slots'];
									 $methods = $getInfo['methods'];
									echo '									<tr>
										<td style="font-size: 12px;">'.htmlspecialchars($name).'</td>
										<td style="font-size: 12px;">'.htmlspecialchars($ip).'</td>
										<td style="font-size: 12px;">'.htmlspecialchars($slots).'</td>
										<td style="font-size: 12px;">'.htmlspecialchars($methods).'</td>
										<td style="font-size: 12px;"><button type="submit" title="Delete Server" name="deleteserver" value="'.htmlspecialchars($id).'" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>
									</tr>';
								}
							}
							?>
							</form>
						</tr>                                       
					</table>
				</div>
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Add <?php if ($system == 'api') { echo 'API'; } else { echo 'Server'; } ?></h3>
					</div>
					<div class="block-content block-content-narrow">
						<?php
						if ($system == 'api') {
						?>
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="name" name="name">
										<label for="name">Name</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="api" name="api">
										<label for="api">API Link</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="network" name="network" size="1">
											<option value="0">Normal Network</option>
											<option value="1">VIP Network</option>
										</select>
										<label for="attacktype">Server Network</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="number" id="slots" name="slots">
										<label for="slots">Slots</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="methods" name="methods[]" size="4" multiple>
											<?php
											$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
											while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
												$name = $getInfo['name'];
												echo '<option value="'.$name.'">'.$name.'</option>';
											}
											?>
										</select>
										<label for="methods">Allowed Methods</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addapi" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
						<?php } else { ?>
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="name" name="name">
										<label for="name">Name</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="ip" name="ip">
										<label for="ip">IP</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="password" id="pass" name="password">
										<label for="pass">Password</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="api" name="api">
										<label for="api">API Link</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="number" id="slots" name="slots">
										<label for="slots">Slots</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="methods" name="methods[]" size="4" multiple>
											<?php
											$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
											while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
												$name = $getInfo['name'];
												echo '<option value="'.$name.'">'.$name.'</option>';
											}
											?>
										</select>
										<label for="methods">Allowed Methods</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addserver" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
						<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>     
	</div>
</main>
<?php

	include 'footer.php';

?>