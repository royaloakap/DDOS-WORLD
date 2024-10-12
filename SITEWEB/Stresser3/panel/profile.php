<?php
     
header("X-XSS-Protection: 1; mode=block");
     
	$page = "Profile";
	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
	$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
	$plansql -> execute(array(":id" => $_SESSION['ID']));
	$row = $plansql -> fetch(); 
	$date = date("m-d-Y", $row['expire']);
	if (!$user->hasMembership($odb)){
		$row['mbt'] = 0;
		$row['concurrents'] = 0;
		$row['name'] = 'No membership';
		$date = 'No membership';
	}
	
	if(!empty($_POST['update'])){
		
		if(empty($_POST['old']) || empty($_POST['new'])){
			$error = error('New passwords do not match!');
		}

		$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password");
		$SQLCheckCurrent -> execute(array(':ID' => $_SESSION['ID'], ':password' => SHA1(md5($_POST['old']))));
		$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
	
		if ($countCurrent == 0){
			$error = error('Current password is incorrect!');
		}
		
		$notify = error($error);
	
		if(empty($error)){
			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
			$SQLUpdate -> execute(array(':password' => SHA1(md5($_POST['new'])),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
			$error = success('Password has been successfully changed!');
		}
	
	}
?>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Profile</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql active" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Profile</li>
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
					if(!empty($error)){
						echo ($error);
					}
				?>
                                                    <div class="card">
                                <div class="card-body">
                                    <div class="mt-2 activity">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label class="text-white" for="old">Current Password</label>
                                                        <input class="form-control" id="old" name="old" type="password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-white" for="new">New Password</label>
                                                        <input class="form-control" id="new" name="new" type="password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="text-white" for="rnew">Repeat New Password</label>
                                                        <input class="form-control" id="rnew" name="rnew" type="password">
                                                    </div>
                                                    <div class="form-group">
                                                        <button name="update" value="change" type="submit" class="btn btn-primary">Change Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>				
                </div>
            </div>
        </div>