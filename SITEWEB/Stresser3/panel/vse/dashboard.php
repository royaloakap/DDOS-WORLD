<?php

header("X-XSS-Protection: 1; mode=block");

    require_once 'header.php'; 

	$TotalUsers = $odb->query("SELECT COUNT(*) FROM `users`")->fetchColumn(0);
	$TotalAdmins = $odb->query("SELECT COUNT(*) FROM `users` WHERE `rank`")->fetchColumn(0);
	$TotalPaidUsers = $odb->query("SELECT COUNT(*) FROM `users` WHERE `membership`")->fetchColumn(0);
	$TodayAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()")->fetchColumn(0);
	$MonthAttack = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()  - INTERVAL 30 DAY")->fetchColumn(0);
	$TotalAttacks = $odb->query("SELECT COUNT(*) FROM `logs`")->fetchColumn(0);
	$TotalPools = $odb->query("SELECT COUNT(*) FROM `api`")->fetchColumn(0);
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);

?>
<title><?php echo $sitename; ?> | Dashboard</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Dashboard</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
			<div class="container-fluid">
				<div class="card-group">
					<div class="card border-right">
						<div class="card-body">
							<div class="d-flex d-lg-flex d-md-block align-items-center">
								<div>
									<div class="d-inline-flex align-items-center">
										<h2 class="text-white mb-1 font-weight-medium"><?php echo $TotalUsers; ?></h2>
									</div>
									<h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate mb-2">Total Users</h6>
								</div>
								<div class="ml-auto mt-md-3 mt-lg-0">
									<span class="opacity-7 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg></span>
								</div>
							</div>
							<div class="progress progress-md">
								<div class="progress-bar bg-purple" role="progressbar" style="width: <?php echo $TotalUsers; ?>%"></div>
							</div>
						</div>
					</div>
					<div class="card border-right">
						<div class="card-body">
							<div class="d-flex d-lg-flex d-md-block align-items-center">
								<div>
									<div class="d-inline-flex align-items-center">
										<h2 class="text-white mb-1 font-weight-medium"><?php echo $TotalAdmins; ?></h2>
									</div>
									<h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate mb-2">Total Admins</h6>
								</div>
								<div class="ml-auto mt-md-3 mt-lg-0">
									<span class="opacity-7 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-globe"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg></span>
								</div>
							</div>
							<div class="progress progress-md">
								<div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $$TotalAdmins+100; ?>%"></div>
							</div>
						</div>
					</div>
					<div class="card border-right">
						<div class="card-body">
							<div class="d-flex d-lg-flex d-md-block align-items-center">
								<div>
									<div class="d-inline-flex align-items-center">
										<h2 class="text-white mb-1 font-weight-medium"><?php echo $RunningAttacks; ?></h2>
									</div>
									<h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate mb-2">Running Attacks</h6>
								</div>
								<div class="ml-auto mt-md-3 mt-lg-0">
									<span class="opacity-7 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg></span>
								</div>
							</div>
							<div class="progress progress-md">
								<div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $RunningAttacks; ?>%" aria-valuenow="1" aria-valuemin="0" aria-valuemax="40"></div>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="d-flex d-lg-flex d-md-block align-items-center">
								<div>
									<h2 class="text-white mb-1 font-weight-medium"><?php echo $TotalPaidUsers; ?></h2>
									<h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate mb-2">Active Users</h6>
								</div>
								<div class="ml-auto mt-md-3 mt-lg-0">
									<span class="opacity-7 text-muted"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg></span>
								</div>
							</div>
							<div class="progress progress-md">
								<div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $TotalPaidUsers; ?>%"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-7">
						<div class="card">
							<div class="card-body">
								<h4 class="card-title">News</h4>
								<div class="mt-4 activity">
                                            <div class="col-lg-12">
										
                                                  			<?php
						                                                        $newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 5");
					                                                        	while($row = $newssql ->fetch()){
						                                                     	$ID = $row['ID'];
							                                                    $title = $row['title'];
						                                                    	$content = $row['content'];
						                                                    	$date = $row['date'];
						                                                    echo 
							'
							<div class="d-flex align-items-start border-left-line pb-3">
													<div>
														<a href="javascript:void(0)" class="btn btn-purple btn-circle mb-2 btn-item">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
														</a>
													</div>
													<div class="ml-3 mt-2">
														<h5 class="text-white font-weight-medium mb-2">'.$title.'</h5>
														<p class="font-14 mb-2 text-muted">'.$content.'</p>
														<span class="font-weight-light font-14 text-muted">15/12/2021</span>
													</div>
												</div> ' ;
						}
                                                           ?>
                                                       </div></div></div></div></div>
					<?php
	$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
	$plansql -> execute(array(":id" => $_SESSION['ID']));
	$row = $plansql -> fetch(); 
	$date = date("d M Y", $row['expire']);
	if (!$user->hasMembership($odb)){
		$row['mbt'] = 'No membership';
		$row['concurrents'] = 'No membership';
		$row['name'] = 'No membership';
		$date = 'No membership';
	}
?>
					<div class="col-md-6 col-lg-5">
						<div class="card">
							<div class="card-body">
								<h4 class="card-title">Account Information</h4>
								<div class="mt-4 activity">
									<div class="table-responsive">
										<table class="table">
											<tbody>
												<tr>
													<td>ID</td>
													<td><?php echo $_SESSION['ID']; ?></td>
												</tr>
												<tr>
													<td>Username</td>
													<td><?php echo $_SESSION['username']; ?></td>
												</tr>
												<tr>
													<td>Attack Time (seconds)</td>
													<td><?php echo $row['mbt']; ?></td>
												</tr>
												<tr>
													<td>Concurrent Attacks</td>
													<td><?php echo $row['concurrents']; ?></td>
												</tr>
												<tr>
													<td>Expiration Date</td>
													<td><?php echo $date; ?></td>
												</tr>
											</tbody>
										</table>	
					
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/.row -->
	  <script>

		alerts();

		function alerts() {
			document.getElementById("alertsdiv").style.display = "none";
			document.getElementById("alerts").style.display = "inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			}
			else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("alertsdiv").innerHTML = xmlhttp.responseText;
					document.getElementById("alerts").style.display = "none";
					document.getElementById("alertsdiv").style.display = "inline-block";
					document.getElementById("alertsdiv").style.width = "100%";
					eval(document.getElementById("ajax").innerHTML);
				}
			}
			xmlhttp.open("GET","vvv/files/user/alerts.php",true);
			xmlhttp.send();
		}
		</script>