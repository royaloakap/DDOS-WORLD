<?php

	include 'header.php';
	$page = "Profile";
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
			$error = 'You need to enter both passwords';
		}

		$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password");
		$SQLCheckCurrent -> execute(array(':ID' => $_SESSION['ID'], ':password' => SHA1(md5($_POST['old']))));
		$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
	
		if ($countCurrent == 0){
			$error = 'Current password is incorrect';
		}
		
		$notify = error($error);
	
		if(empty($error)){
			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
			$SQLUpdate -> execute(array(':password' => SHA1(md5($_POST['new'])),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
			$notify = success('Password has been successfully changed');
		}
	
	}
	
?>
			<main id="main-container" style="min-height: 404px;">
                <div class="content bg-image" style="background-image: url('assets/img/photos/photo8@2x.jpg');">
                    <div class="push-50-t push-15 clearfix">
                        <div class="push-15-r pull-left animated fadeIn">
                            <img class="img-avatar img-avatar-thumb" src="assets/img/avatars/anon.jpg" alt="">
                        </div>
                        <h1 class="h2 text-white push-5-t animated zoomIn"><?php echo htmlentities($_SESSION['username']); ?></h1>
                        <h2 class="h5 text-white-op animated zoomIn">
							<?php echo htmlentities($row['name']) . " | " . htmlentities($date); ?><br>
						</h2>
                    </div>
                </div>
                <div class="content bg-white border-b">
                    <div class="row items-push text-uppercase">
                        <div class="col-xs-6 col-sm-4">
                            <div class="font-w700 text-gray-darker animated fadeIn">My Purchases</div>
                            <a class="h2 font-w300 text-primary animated flipInX" href="#"><?php echo $stats -> purchases($odb); ?></a>
                        </div>
                        <div class="col-xs-6 col-sm-4">
                            <div class="font-w700 text-gray-darker animated fadeIn">My Running Attacks</div>
                            <a class="h2 font-w300 text-primary animated flipInX" href="#"><?php echo $stats -> countRunning($odb, $_SESSION['username']); ?></a>
                        </div>
                        <div class="col-xs-6 col-sm-4">
                            <div class="font-w700 text-gray-darker animated fadeIn">My Attacks</div>
                            <a class="h2 font-w300 text-primary animated flipInX" href="#"><?php echo $stats -> totalBootsForUser($odb, $_SESSION['username']); ?></a>
                        </div>
                    </div>
                </div>
                <div class="content content-boxed">
                    <div class="row">
						<?php if(!empty($notify)){
							echo '<div class="col-md-12">'.$notify.'</div>';
						}
						?>
						<div class="col-md-8">
                            <div class="block">
                                <ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
                                    <li class="active">
                                        <a href="#payments"><i class="fa fa-dollar"></i> Payments</a>
                                    </li> 
                                    <li>
                                        <a href="#logins"><i class="fa fa-calendar"></i> Logins</a>
                                    </li>
                                    <li>
                                        <a href="#attacks"><i class="fa fa-rocket"></i> Attacks</a>
                                    </li>
                                </ul>
                                <div class="block-content tab-content">
                                    <div class="tab-pane active" id="payments">
                                        <table class="table">
											<thead>
												<tr>
													<th style="font-size: 12px;">Transaction ID</th>
													<th style="font-size: 12px;">Amount (US Dollars)</th>
													<th style="font-size: 12px;">Email</th>
													<th style="font-size: 12px;">Date</th>
												</tr>
											</thead>
											<tbody style="font-size: 12px;">
											<?php
											$SQLGetLogs = $odb -> query("SELECT * FROM `payments` WHERE `user`='{$_SESSION['ID']}' ORDER BY `date` DESC LIMIT 0, 5");
											while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
												$tid = $getInfo['tid'];
												$paid = $getInfo['paid'];
												$email = $getInfo['email'];
												$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
												echo '<tr>
														<td>'.$tid.'</td>
														<td>$'.$paid.'</td>
														<td>'.$email.'</td>
														<td>'.$date.'</td>
													  </tr>';
											}
											?>
											</tbody>                                       
										</table>
                                    </div>
                                    <div class="tab-pane" id="logins">
                                        <table class="table">
											<thead>
												<tr>
													<th style="font-size: 12px;">IP Address</th>
													<th style="font-size: 12px;">Country</th>
													<th style="font-size: 12px;">Date</th>
												</tr>
											</thead>
											<tbody style="font-size: 12px;">
												<?php
												$SQLGetLogs = $odb -> query("SELECT * FROM `loginlogs` WHERE `username`='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 0, 5");
												while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
													$IP = $getInfo['ip'];
													$country = $getInfo['country'];
													$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
													echo '<tr>
															<td>'.htmlspecialchars($IP).'</td>
															<td>'.$country.'</td>
															<td>'.$date.'</td>
														  </tr>';
												}
												?>
											</tbody>                                     
										</table>
                                    </div>
                                    <div class="tab-pane" id="attacks">
                                        <table class="table">
											<thead>
												<tr>
													<th style="font-size: 12px;">Host</th>
													<th style="font-size: 12px;">Port</th>
													<th style="font-size: 12px;">Time</th>
													<th style="font-size: 12px;">Method</th>
													<th style="font-size: 12px;">Date</th>
												</tr>
											</thead>
											<tbody style="font-size: 12px;">
											<?php
											$SQLGetLogs = $odb -> query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 10");
											while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
												$IP = $getInfo['ip'];
												$port = $getInfo['port'];
												$time = $getInfo['time'];
												$method = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$getInfo['method']}'")->fetchColumn(0);
												$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
												echo '<tr><td>'.htmlspecialchars($IP).'</td><td>'.$port.'</td><td>'.$time.' seconds</td><td>'.htmlspecialchars($method).'</td><td>'.$date.'</td></tr>';
											}
											?>
											</tbody>                                       
										</table>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="col-lg-4">                          
                            <div class="block block-themed">
                                <div class="block-header bg-primary">                                 
                                    <h3 class="block-title">
										Change Password
										<i style="display: none;" id="image" class="fa fa-cog fa-spin"></i>
									</h3>
                                </div>
                                <div class="block-content">
                                    <form class="form-horizontal push-10-t push-10" method="post">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="password" id="old" name="old" placeholder="Enter your old password..">
                                                    <label for="old">Old Password</label>
                                                </div>
                                            </div>
                                            <div class="col-xs-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="password" id="new" name="new" placeholder="Enter your new password..">
                                                    <label for="new">New Password</label>
                                                </div>
                                            </div>
                                        </div>                         
                                        <div class="form-group">
                                            <div class="col-xs-12 text-center">                                             
												<button class="btn btn-sm btn-danger" name="update" value="change" type="submit">
													<i class="fa fa-plus push-5-r"></i> Change
												</button>
											</div>
                                        </div>
                                    </form>
                                </div>
							</div>
                        </div>
                    </div>
                </div>
            </main>
<?php

	include 'footer.php';

?>