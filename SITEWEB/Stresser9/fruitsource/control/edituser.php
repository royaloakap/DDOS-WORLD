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
if (!isset($_GET['id']))
{
	die('No ID Selected');
}
$id = $_GET['id'];
$SQLGetInfo = $odb -> prepare("SELECT * FROM `users` WHERE `ID` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $_GET['id']));
$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$username = $userInfo['username'];
$password = $userInfo['password'];
$email = $userInfo['email'];
$rank = $userInfo['rank'];
$membership = $userInfo['membership'];
$status = $userInfo['status'];
$page = "Customer";
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
				<div class="col-md-4">
					<?php 
				   if (isset($_POST['rBtn']))
				   {
					$sql = $odb -> prepare("DELETE FROM `users` WHERE `ID` = :id");
					$sql -> execute(array(':id' => $id));
					header('location: ca.php');
				   }
				   if (isset($_POST['updateBtn']))
				   {
					$update = false;
					if ($username!= $_POST['username'])
					{
						if (ctype_alnum($_POST['username']) && strlen($_POST['username']) >= 4 && strlen($_POST['username']) <= 15)
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `username` = :username WHERE `ID` = :id");
							$SQL -> execute(array(':username' => $_POST['username'], ':id' => $id));
							$update = true;
							$username = $_POST['username'];
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Username has to be 4-15 characters and alphanumeric</div></div>';
						}
					}
					if (!empty($_POST['password']))
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `ID` = :id");
						$SQL -> execute(array(':password' => SHA1($_POST['password']), ':id' => $id));
						$update = true;
						$password = SHA1($_POST['password']);
					}
					if ($email != $_POST['email'])
					{
						if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `ID` = :id");
							$SQL -> execute(array(':email' => $_POST['email'], ':id' => $id));
							$update = true;
							$email = $_POST['email'];
						}
						else
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Email is invalid</div></div>';
						}
					}
					if ($rank != $_POST['rank'])
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `rank` = :rank WHERE `ID` = :id");
						$SQL -> execute(array(':rank' => $_POST['rank'], ':id' => $id));
						$update = true;
						$rank = $_POST['rank'];
					}
					if ($membership != $_POST['plan'])
					{
						if ($_POST['plan'] == 0)
						{
							$SQL = $odb -> prepare("UPDATE `users` SET `expire` = '0', `membership` = '0' WHERE `ID` = :id");
							$SQL -> execute(array(':id' => $id));
							$update = true;
							$membership = $_POST['plan'];
						}
						else
						{
							$getPlanInfo = $odb -> prepare("SELECT `unit`,`length` FROM `plans` WHERE `ID` = :plan");
							$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
							$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
							$unit = $plan['unit'];
							$length = $plan['length'];
							$newExpire = strtotime("+{$length} {$unit}");
							$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
							$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
							$update = true;
							$membership = $_POST['plan'];
						}
					}
					
					if ($status != $_POST['status'])
					{
						$SQL = $odb -> prepare("UPDATE `users` SET `status` = :status WHERE `ID` = :id");
						$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));
						$update = true;
						$status = $_POST['status'];
					}
					if ($update == true)
					{
						echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: User has been updated</div></div>';
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Nothing was updated</div></div>';
					}
					}
				   ?>
					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Customer</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="username" maxlength="15" value="<?php echo $username;?>" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="password" placeholder="Password" type="password"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="email" type="text" value="<?php echo htmlentities($email);?>"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                     <select class="form-control" name="rank">
								<?php
								function selectedR($check, $rank)
								{
									if ($check == $rank)
									{
										return 'selected="selected"';
									}
								}
								?>
                                <option value="0" <?php echo selectedR(0, $rank); ?> >User</option>
                                <option value="1" <?php echo selectedR(1, $rank); ?> >Admin</option>
								</select>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                     <select class="form-control" name="plan">
								<option value="0">No Membership</option>
								<?php 
									$SQLGetMembership = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
									while($memberships = $SQLGetMembership -> fetch(PDO::FETCH_ASSOC))
									{
										$mi = $memberships['ID'];
										$mn = $memberships['name'];
										$selectedM = ($mi == $membership) ? 'selected="selected"' : '';
										echo '<option value="'.$mi.'" '.$selectedM.'>'.$mn.'</option>';
									}
								?>
								</select>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                     <select class="form-control" name="status">
								<?php
								function selectedS($check, $rank)
								{
									if ($check == $rank)
									{
										return 'selected="selected"';
									}
								}
								?>
                                <option value="0" <?php echo selectedS(0, $status); ?>>Active</option>
                                <option value="1" <?php echo selectedS(1, $status); ?>>Banned</option>
								</select>
				                    </div>
				                </div>
						</div>
						<div class="panel-footer">
							<button type="submit" name="updateBtn" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Update</button>
							<button type="submit" name="rBtn" class="btn btn-sm btn-danger"><i class="fa fa-ban"></i> Remove User</button>
						</div>	
						</form>
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