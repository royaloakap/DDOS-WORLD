<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	$id = $_GET['id'];

	if(!is_numeric($id)){
		die(error('Invalid type of ID'));
	}

	$SQLGetInfo = $odb -> prepare("SELECT * FROM `api` WHERE `ID` = :id LIMIT 1");
	$SQLGetInfo -> execute(array(':id' => $_GET['id']));
	$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
	
	$name = $userInfo['name'];
	$api = $userInfo['api'];
	$slots = $userInfo['slots'];
	$type = $userInfo['type'];	
	$status = $userInfo['status'];			$network = $userInfo['network'];	
	$methods = $userInfo['methods'];
	$update = false;

   if (isset($_POST['update'])){
	   
	   if ($user -> isAdmin($odb)){			$methodsX = implode(" ",$_POST['methodsXD']);
		   
			if ($name!= $_POST['name'])			{
					$SQL = $odb -> prepare("UPDATE `api` SET `name` = :name WHERE `ID` = :id");			    	$SQL -> execute(array(':name' => $_POST['name'], ':id' => $id));
					$update = true;
					$name = $_POST['name'];
			}
			if ($api != $_POST['api'])			{				$SQL = $odb -> prepare("UPDATE `api` SET `api` = :api WHERE `ID` = :id");				$SQL -> execute(array(':api' => $_POST['api'], ':id' => $id));				$api = $_POST['api'];				$update = true;			}						if ($slots != $_POST['slots'])			{				$SQL = $odb -> prepare("UPDATE `api` SET `slots` = :slots WHERE `ID` = :id");				$SQL -> execute(array(':slots' => $_POST['slots'], ':id' => $id));				$update = true;				$slots = $_POST['slots'];			}					if ($methods != $methodsX)			{				$SQL = $odb -> prepare("UPDATE `api` SET `methods` = :methods WHERE `ID` = :id");				$SQL -> execute(array(':methods' => $methodsX, ':id' => $id));				$update = true;				$methods = $methodsX;			} else {				die(error('Please, put all methods..'));			}			if ($type != $_POST['type'])			{				$SQL = $odb -> prepare("UPDATE `api` SET `type` = :type WHERE `ID` = :id");				$SQL -> execute(array(':type' => $_POST['type'], ':id' => $id));				$update = true;				$type = $_POST['type'];			}						if ($status != $_POST['status'])			{				$SQL = $odb -> prepare("UPDATE `api` SET `status` = :status WHERE `ID` = :id");				$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));				$update = true;				$type = $_POST['type'];			}
			
		
		if ($update == true){						$updateMsg = 'Edit API - '.$name.'';
			$notify = success('API Has Been Updated');
			if(!empty($updateMsg)){
				$actionSQL = $odb->prepare("INSERT INTO `actions` VALUES (NULL,?,?,?,?)");
				$actionSQL->execute(array($_SESSION['username'],$_SESSION['username'],$updateMsg,time()));
			}
		} else {
			$notify = error('Nothing has been updated');
		}
		
		if (!empty($error)){
			$notify = error($error);
		}
	}   }
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					API <small>API</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>HUB Management</li>
					<li><a class="link-effect" href="hub.php">API</a></li>
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
						<h3 class="block-title">Edit API</h3>
					</div>
					<div class="content">
						<div class="row block-content block block-content">
							<form class="form-horizontal push-10-t" method="post">
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<input class="form-control" type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
											<label for="name">Name</label>
										</div>
									</div>
								</div> 
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<input class="form-control" type="text" id="api" name="api" value="<?php echo $api; ?>">
											<label for="email">API Link</label>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">																			<div class="form-material">																					<input class="form-control" type="number" id="slots" name="slots" value="<?php echo $slots; ?>">																						<label for="slots">Slots</label>																					</div>									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-12">
										<div class="form-material">
											<select class="form-control" id="private" name="type" size="1">											<?php											function selectedR($check, $typexd)											{												if ($check == $typexd)												{													return 'selected="selected"';												}											}											?>
												<option value="0" <?php echo selectedR(0, $network); ?> >Normal</option>
												<option value="1" <?php echo selectedR(1, $network); ?> >VIP</option>
											</select>
											<label for="private">Network</label>
										</div>
									</div>
								</div>
								<div class="form-group row">									<div class="col-sm-12">										<div class="form-material">											<select class="form-control" id="private" name="status" size="1">											<?php											function status($check, $type)											{												if ($check == $type)												{													return 'selected="selected"';												}											}											?>												<option value="0" <?php echo status(0, $status); ?> >Disabled</option>												<option value="1" <?php echo status(1, $status); ?> >Enabled</option>											</select>											<label for="private">Status</label>										</div>									</div>								</div>																<div class="row-form">                                        <div class="col-md-4"><strong>Allowed Methods</strong><br><small> (If you edit the API, you need check all methods)</small></div>                                        <div class="col-md-8">                                            <select class="form-control" name="methodsXD[]" multiple="multiple"><?php$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){ $name = $getInfo['name']; echo '<option value="'.$name.'">'.$name.'</option>'; }?>                                            </select>										</div>                                    </div>
								<div class="form-group row">
									<div class="col-sm-9">
										<button name="update" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>     
	</div>
</main>
<?php

	include 'footer.php';

?>