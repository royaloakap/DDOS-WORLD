<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Hub Settings";
	require_once 'header.php'; 
	
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
		$vip = $_POST['vip'];
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
			$SQLinsert = $odb -> prepare("INSERT INTO `api` VALUES(NULL, :name, :api, :slots, :methods, :vip)");
			$SQLinsert -> execute(array(':api' => $api, ':name' => $name, ':slots' => $slots, ':methods' => $methods, ':vip' => $vip));
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
	
?>


<title><?php echo $sitename; ?> | Api Settings</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Api Settings</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Api Settings</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>


  <!-- Page Content -->
                 <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 col-lg-12">
	  <?php
		if(isset($notify)){
			echo $notify;
		}
		?>
	     <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Method Manager</h3>
       
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
											<td style="font-size: 12px;"><button name="delete" value="'.$id.'" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></td>
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
        </div>
		<div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">API Manager</h3>
			<table class="table">
						<tr>
							<th style="font-size: 12px;" width="15%">Name</th>
							<th style="font-size: 12px;" width="20%">API URL</th>
							<th style="font-size: 12px;">Slots</th>
							<th style="font-size: 12px;">VIP</th>
							<th style="font-size: 12px;">Methods</th>
							<th style="font-size: 12px;">Delete</th>

						</tr>
						<tr>
							<form method="post">
							<?php
								$SQLGetMethods = $odb -> query("SELECT * FROM `api`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									 $id = $getInfo['id'];
									 $api = $getInfo['api'];
									 $name = $getInfo['name'];
									 $slots = $getInfo['slots'];
									 $methods = $getInfo['methods'];
									 $vip = $getInfo['vip'];
									
									if($vip == "0")
									{
										$vip = '<button type="button" class="btn btn-outline btn-danger btn-circle"><i class="fa fa-times"></i> </button>';
									}
									
									if($vip == "1")
									{
										$vip = '<button type="button" class="btn btn-outline btn-success btn-circle"><i class="fa fa-check"></i> </button>';
									}
									 echo '<tr>
												<td style="font-size: 12px;">'.htmlspecialchars($name).'</td>
												<td style="font-size: 12px;" width="20%">'.htmlspecialchars($api).'</td>
												<td style="font-size: 12px;">'.htmlspecialchars($slots).'</td>
												<td style="font-size: 12px;">'.($vip).'</td>
												<td style="font-size: 12px;">'.htmlspecialchars($methods).'</td>
												<td style="font-size: 12px;"><button type="submit" title="Delete API" name="deleteapi" value="'.htmlspecialchars($id).'" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
											</tr>';
								}
							?>
							</form>
						</tr>                                       
					</table>
          </div>
        </div>
      </div>
	  
	  <div class="row">
	     <div class="col-md-5 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add New Method</h3>
       
					<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="name">Name</label>
										<input class="form-control" type="text" id="name" name="name">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="fname">Tag Name</label>
										<input class="form-control" type="text" id="fname" name="fullname">
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="attacktype">Layer Type</label>
										<select class="form-control" id="attacktype" name="type" size="1">
											<option value="layer4">Layer 4</option>
											<option value="layer7">Layer 7</option>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addmethod" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>


          </div>
        </div>
		<?php
			$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
			$plansql -> execute(array(":id" => $_SESSION['ID']));
			$row = $plansql -> fetch(); 
			$date = date("m-d-Y, h:i:s a", $row['expire']);
			if (!$user->hasMembership($odb)){
				$row['mbt'] = 0;
				$row['concurrents'] = 0;
				$row['name'] = 'No membership';
				$date = '0-0-0';
			}
		?>
		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add New Server</h3>
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="name">Name</label>
										<input class="form-control" type="text" id="name" name="name">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="api">API Link</label>
										<input class="form-control" type="text" id="api" name="api" placeholder="http://link.com/api.php?key=keyhere&target=[host]&port=[port]&time=[time]&method=[method]">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="slots">Slots</label>
										<input class="form-control" type="number" id="slots" name="slots">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="methods">Allowed Methods</label>
										<select class="form-control" id="methods" name="methods[]" size="4" multiple>
											<?php
											$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
											while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
												$name = $getInfo['name'];
												echo '<option value="'.$name.'">'.$name.'</option>';
											}
											?>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="vip">VIP</label>
										<select class="form-control" id="vip" name="vip">
											
												<option value="0">No</option>
												<option value="1">Yes</option>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addapi" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
          </div>
        </div>
      </div>
      <!--/.row -->