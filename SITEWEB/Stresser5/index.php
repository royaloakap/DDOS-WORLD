<?php

$paginaname = 'Dashboard';

?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php include("@/header.php"); ?>
	
                    <div id="page-content">
                     
                        <div class="row">
                      
                            <div class="col-sm-6 col-lg-3">
                                <a href="javascript:void(0)" class="widget">
                                    <div class="widget-content widget-content-mini text-right clearfix">
                                        <div class="widget-icon pull-left themed-background-danger">
                                            <i class="fa fa-signal text-light-op"></i>
                                        </div>
                                        <h2 class="widget-heading h3 text-danger">
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> totalBoots($odb); ?>"></span></strong>
                                        </h2>
                                        <span class="text-muted">Total Attacks</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="javascript:void(0)" class="widget">
                                    <div class="widget-content widget-content-mini text-right clearfix">
                                        <div class="widget-icon pull-left themed-background-info">
                                            <i class="fa fa-fire text-light-op"></i>
                                        </div>
                                        <h2 class="widget-heading h3 text-info">
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> runningBoots($odb); ?>"></span></strong>
                                        </h2>
                                        <span class="text-muted">Running Attacks</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="javascript:void(0)" class="widget">
                                    <div class="widget-content widget-content-mini text-right clearfix">
                                        <div class="widget-icon pull-left themed-background">
                                            <i class="fa fa-hdd-o text-light-op"></i>
                                        </div>
                                        <h2 class="widget-heading h3">
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> serversonline($odb); ?>"></span></strong>
                                        </h2>
                                        <span class="text-muted">Servers</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="javascript:void(0)" class="widget">
                                    <div class="widget-content widget-content-mini text-right clearfix">
                                        <div class="widget-icon pull-left themed-background-danger">
                                            <i class="fa fa-heart text-light-op"></i>
                                        </div>
                                        <h2 class="widget-heading h3 text-danger">
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> totalUsers($odb); ?>"></span></strong>
                                        </h2>
                                        <span class="text-muted">Total Users</span>
                                    </div>
                                </a>
                            </div>
                            
                        </div>
            
                        <div class="row">
                          
						<div class="col-sm-6 col-lg-8">
 
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted"><?php echo htmlspecialchars($sitename); ?></span>
								<i class="fa fa-send"></i> <b>News</b>
								</div>
								
								<div class="widget-content">
									<div style="position: relative; width: auto" class="slimScrollDiv">
										<div id="stats">
											<table class="table table-striped">
												<tbody>
													<tr>
														<th><center>Title</center></th>
														<th><center>Content</center></th>
														<th><center>Date</center></th>
														<th><center>Author</center></th>
													</tr>
													<tr>
													
													</tr>
													<?php
													$newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 4");
													while($row = $newssql ->fetch())
													{
													$id = $row['ID'];
													$title = $row['title'];
													$content = $row['content'];
													$autor = $row['author'];
													echo '
													<tr>
															<td><center>'.htmlspecialchars($title).'</center></td>
															<td><center>'.htmlspecialchars($content).'</center></td>
															<td><center> '.date("d/m/y" ,$row['date']).'</center></td>
															<td><center><span class="label label-success">'.htmlspecialchars($autor).'</span></center></td>
														</div>
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
						<div class="col-sm-6 col-lg-4">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted"><?php echo htmlspecialchars($sitename); ?></span>
								<i class="fa fa-user"></i> <b>My information</b>
								</div>
								
								<div class="widget-content">
									<table class="table table-striped table-vcenter">
									<?php
									$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
									$plansql -> execute(array(":id" => $_SESSION['ID']));
									$rowxd = $plansql -> fetch(); 
									$date = date("d/m/Y, h:i a", $rowxd['expire']);
									if (!$user->hasMembership($odb))
									{
									$rowxd['mbt'] = 0;
									$rowxd['concurrents'] = 0;
									$rowxd['name'] = 'No membership';
									$date = 'No membership';
									}
									?>

									<tbody>
										<tr>
											<td class="text-right"><strong>Username</strong></td>
											<td><?php echo $_SESSION['username']; ?></td>
										</tr>
										<tr>									
											<td class="text-right" style="width: 50%;"><strong>Membership</strong></td>
											<td><?php echo htmlspecialchars($rowxd['name']); ?> <a data-original-title="Upgrade" href="purchase.php" data-toggle="tooltip" title=""><i class="fa fa-chevron-up"></i></a></td>
										</tr>
										<tr>
											<td class="text-right"><strong>Membership Expired</strong></td>
											<td><?php echo $date; ?></td>
										</tr>
										<tr>
											<td class="text-right"><strong>Boot Time</strong></td>
											<?php
											if (!$user->hasMembership($odb))
											{
												echo '<td>No membership</td>';
											} else {
											?>
											<td><?php echo $rowxd['mbt']; ?> seconds</td>
											<?php } ?>
										</tr>
										<tr>	
											<td class="text-right"><strong>Current Boots</strong></td>
											<?php
											if (!$user->hasMembership($odb))
											{
												echo '<td>No membership</td>';
											} else {
											?>
											<td><?php echo $rowxd['concurrents']; ?> Current Boots</td>
											<?php } ?>
										</tr>
									</tbody>
									</table>
								
								</div>
							</div>
						</div>
					</div>	
						
					 <div class="row">	
						<div class="col-sm-6 col-lg-8">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted"><?php echo htmlspecialchars($sitename); ?></span>
								<i class="fa fa-desktop"></i> <b>Servers</b>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
									<div id="stats">
										<table class="table table-striped">
											<tbody>
												<tr>
													<th><center>Name</center></th>
													<th><center>Attacks</center></th>
													<th><center>Targets</center></th>
													<th><center>Stop Function</center></th>
													<th><center>Status</center></th>
												</tr>
												<tr>
												
												</tr>
												<?php
												if ($system == 'api') {
													$SQLGetInfo = $odb->query("SELECT * FROM `api` ORDER BY `id` DESC");
												} else {
													$SQLGetInfo = $odb->query("SELECT * FROM `servers` ORDER BY `id` DESC");
												}
												while ($getInfo = $SQLGetInfo->fetch(PDO::FETCH_ASSOC)) {
													$name    = $getInfo['name'];
													$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
													$load    = round($attacks / $getInfo['slots'] * 100, 2);
													echo '
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$(\'#ra'.$name.'\').load(\'ajax/servers.php?sv='.$name.'\').fadeIn("slow");
													}, 1000); // refresh every 10000 milliseconds
													</script>
													
													
													<tr>
																<td><center>' . $name . '</center></td>
																<td><center><div id="ra'.$name.'"></center></td>
																<td><center>All</center></td>
																<td><center>Yes</center></td>
																<td><center><span class="label label-success">Enabled</span></center></td>
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
				</div>
                     <? // NO BORRAR LOS TRES DIVS! ?>
               </div>
               </div>
             
          </div>

		<?php include("@/script.php"); ?>
    </body>
</html>