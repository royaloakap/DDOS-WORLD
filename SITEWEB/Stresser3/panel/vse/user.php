<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Manage User";
	require_once 'header.php'; 
	
	$id = $_GET['id'];

	if(!is_numeric($id)){
		die(error('Invalid type of ID'));
	}

	$SQLGetInfo = $odb -> prepare("SELECT * FROM `users` WHERE `ID` = :id LIMIT 1");
	$SQLGetInfo -> execute(array(':id' => $_GET['id']));
	$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
	
	$username = $userInfo['username'];
	$rank = $userInfo['rank'];
	$membership = $userInfo['membership'];
	$status = $userInfo['status'];	
	$expire = $userInfo['expire'];	
	
	$update = false;

   if (isset($_POST['update'])){
	   
	   if ($user -> isAdmin($odb)){
		   
			if ($username!= $_POST['username']){
				if (ctype_alnum($_POST['username']) && strlen($_POST['username']) >= 4 && strlen($_POST['username']) <= 15){
					$SQL = $odb -> prepare("UPDATE `users` SET `username` = :username WHERE `ID` = :id");
					$SQL -> execute(array(':username' => $_POST['username'], ':id' => $id));
					$update = true;
					$username = $_POST['username'];
				}
				else{
					$error = 'Username has to be 4-15 characters in length and alphanumeric';
				}
			}
			
			if (!empty($_POST['password'])){
				$SQL = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `ID` = :id");
				$SQL -> execute(array(':password' => SHA1(md5($_POST['password'])), ':id' => $id));
				$update = true;
			}
			
			if ($rank != $_POST['rank']){
				$SQL = $odb -> prepare("UPDATE `users` SET `rank` = :rank WHERE `ID` = :id");
				$SQL -> execute(array(':rank' => $_POST['rank'], ':id' => $id));
				$update = true;
				$updateMsg = "User rank updated from {$rank} to {$_POST['rank']}";
				$rank = $_POST['rank'];
			}
			
			if ($expire != strtotime($_POST['expire'])){
				$SQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire WHERE `ID` = :id");
				$SQL -> execute(array(':expire' => strtotime($_POST['expire']), ':id' => $id));
				$update = true;
				$updateMsg = "Users expire updated from {$expire} to {$_POST['expire']}";
				$expire = strtotime($_POST['expire']);
			}
			
			if ($status != $_POST['status']){
				$SQL = $odb -> prepare("UPDATE `users` SET `status` = :status WHERE `ID` = :id");
				$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));
				$status = $_POST['status'];
				$reason = $_POST['reason'];
				$SQLinsert = $odb -> prepare('INSERT INTO `bans` VALUES(:username, :reason)');
				$SQLinsert -> execute(array(':username' => $username, ':reason' => $reason));
				$update = true;
			}
		}
		
		if ($membership != $_POST['plan']){
			
			if ($_POST['plan'] == 0){
				if ($user -> isAdmin($odb)){
					$SQL = $odb -> prepare("UPDATE `users` SET `expire` = '0', `membership` = '0' WHERE `ID` = :id");
					$SQL -> execute(array(':id' => $id));
					$update = true;
					$updateMsg = "User updated to plan: Non";
					$membership = $_POST['plan'];
				}
				else{
					$error = "You cannot remove packages";
				}
			}
			else{
				if ($_POST['plan'] != 85){
					if($user -> isAdmin($odb)){
						$getPlanInfo = $odb -> prepare("SELECT `unit`,`length`,`name` FROM `plans` WHERE `ID` = :plan");
						$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
						$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
						$unit = $plan['unit'];
						$length = $plan['length'];
						$name = $plan['name'];
						$newExpire = strtotime("+{$length} {$unit}");
						$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
						$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
						$update = true;
						$updateMsg = "User updated to plan: {$name}";
						$membership = $_POST['plan'];
					}
					else{
						$error = "You cannot give any other package apart from Vouch packages";
					}
				}
				else{
					$getPlanInfo = $odb -> prepare("SELECT `unit`,`length`,`name` FROM `plans` WHERE `ID` = :plan");
					$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
					$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
					$unit = $plan['unit'];
					$length = $plan['length'];
					$newExpire = strtotime("+{$length} {$unit}");
					$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");					
					$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
					$update = true;
					$updateMsg = "User updated to plan: Vouch";
					$membership = $_POST['plan'];
				}
			}
		}
		if ($update == true){
			$notify = success('User Has Been Updated');
			if(!empty($updateMsg)){
				$actionSQL = $odb->prepare("INSERT INTO `actions` VALUES (NULL,?,?,?,?)");
				$actionSQL->execute(array($_SESSION['username'],$username,$updateMsg,time()));
			}
		} else {
			$notify = error('Nothing has been updated');
		}
		
		if (!empty($error)){
			$notify = error($error);
		}
	}
	
	function selectedR($b, $a){
		if ($a == $b){
			return 'selected="selected"';
		}
	}
?>


<title><?php echo $sitename; ?> | User Panel</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">User Panel</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">User Panel</li>
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
		if(isset($done)){
			echo success($done);
		}
		?>
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
				
				<form class="form-horizontal push-10-t" method="post">
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<label for="name">Username</label>
											<input class="form-control" type="text" id="name" name="username" value="<?php echo htmlspecialchars($username); ?>">
											
										</div>
									</div>
								</div> 
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<label for="price">New Password</label>
											<input class="form-control" type="password" id="price" placeholder="Leave empty to keep the same" name="password">
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
										<label for="private">Rank</label>
											<select class="form-control" id="private" name="rank">
												<option value="0" <?php echo selectedR(0, $rank); ?> >User</option>
												<option value="1" <?php echo selectedR(1, $rank); ?> >Administrator</option>
												<option value="2" <?php echo selectedR(2, $rank); ?> >Supporter</option>
											</select>
											
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
										<label for="plan">Membership</label>
											<select class="form-control" id="plan" name="plan" >
												<option value="0">No Membership</option>	
												<?php 
												$SQLGetMembership = $odb -> query("SELECT * FROM `plans`");
												while($memberships = $SQLGetMembership -> fetch(PDO::FETCH_ASSOC)){
													$mi = $memberships['ID'];
													$mn = $memberships['name'];
													echo '<option value="'.$mi.'" '. selectedR($mi, $membership) .'>'.$mn.'</option>';
												}
												?>
											</select>
											
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
										<label for="status">Status</label>
											<select class="form-control" id="private" name="status">
												<option value="0" <?php echo selectedR(0, $status); ?> >Active</option>
												<option value="1" <?php echo selectedR(1, $status); ?> >Banned</option>
												<option value="2" <?php echo selectedR(2, $status); ?> >Warning</option>
											</select>

										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<label for="name">Reason</label>
											<input class="form-control" type="text" id="name" name="reason">
										
										</div>
									</div>
								</div> 
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
										<label for="expire">Expiration Date</label>
											<input class="form-control" type="text" id="expire" name="expire" value="<?php echo date("d-m-Y", $expire); ?>">
											
										</div>
									</div>
								</div> 
								<div class="form-group row">
									<div class="col-sm-9">
										<button name="update" value="do" class="btn btn-primary" type="submit">Submit</button> <a href="users.php" type="button" class="btn btn-primary">Go back</a>
									</div>
								</div>
							</form>

          </div>
        </div>
      </div>