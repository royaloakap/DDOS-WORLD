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
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> totalBoots($odb) + 336516; ?>"></span></strong>
                                        </h2>
                                        <span class="text-muted">Total Attacks</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="javascript:void(0)" class="widget">
                                    <div class="widget-content widget-content-mini text-right clearfix">
                                        <div class="widget-icon pull-left themed-background-info">
                                            <i class="fa fa-cog fa-spin text-light-op"></i>
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
                                            <i class="fa fa-sitemap text-light-op"></i>
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
                                            <i class="fa fa-users text-light-op"></i>
                                        </div>
                                        <h2 class="widget-heading h3 text-danger">
                                            <strong><span data-toggle="counter" data-to="<?php echo $stats -> totalUsers($odb) + 12700;; ?>"></span></strong>
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
														<th><center>Date</center></th>													</tr>
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
															<td><center><span class="label label-success"> '.date("d/m/y" ,$row['date']).'</span></center></td>
															
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
											<td class="text-right"><strong>Concurrent Boots</strong></td>
											<?php
											if (!$user->hasMembership($odb))
											{
												echo '<td>No membership</td>';
											} else {
											?>
											<td><?php echo $rowxd['concurrents']; ?></td>
											<?php } ?>
										</tr>
										<tr>	
											<td class="text-right"><strong>Premium Network</strong></td>
											<?php
											if ($user->isPremium($odb))
											{
												echo '<td>Yes (Grab that power)</td>';
											}
											else
											{
												echo '<td>No</td>';
											}
											?>
											
											
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
													
													<th><center>Type</center></th>
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
													$kind    = $getInfo['kind'];
													$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
													$load    = round($attacks / $getInfo['slots'] * 100, 2);
													echo '
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$(\'#ra'.$name.'\').load(\'ajax/servers.php?sv='.$name.'\').fadeIn("slow");
													}, 25000); // refresh every 10000 milliseconds
													</script>
													
													
													<tr>
																<td><center>' . $name . '</center></td>
																<td><center><div id="ra'.$name.'"></center></td>
																<td><center>All</center></td>
																<td><center><span class="label label-primary">' . $kind . '</span></center></td>
																
																
														   </tr>';
												}
												?>

<?php
												if ($system == 'api') {
													$SQLGetInfo = $odb->query("SELECT * FROM `apipremium` ORDER BY `id` DESC");
												} else {
													$SQLGetInfo = $odb->query("SELECT * FROM `servers` ORDER BY `id` DESC");
												}
												while ($getInfo = $SQLGetInfo->fetch(PDO::FETCH_ASSOC)) {
													$name    = $getInfo['name'];
													$kind    = $getInfo['kind'];
													$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
													$load    = round($attacks / $getInfo['slots'] * 100, 2);
													echo '
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$(\'#ra'.$name.'\').load(\'ajax/servers.php?sv='.$name.'\').fadeIn("slow");
													}, 25000); // refresh every 10000 milliseconds
													</script>
													
													
													<tr>
																<td><center>' . $name . '</center></td>
																<td><center><div id="ra'.$name.'"></center></td>
																<td><center>All</center></td>
																<td><center><span class="label label-primary">' . $kind . '</span></center></td>
																
																
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



<div class="modal fade" id="modal1" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-exclamation-circle"></i>&nbsp; Welcome to Network Stresser</h4>
        </div>
        <div class="modal-body">
         <center>Professional Stressing at any time. </center>

<p><center><img src="pub4.gif" alt="HTML5 Icon" style="width:468px;height:60px;"><br><br>

Questions? Join our Discord server<a href="https://discordapp.com/invite/mP6SrPM"> HERE!</a></center>

</center>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
</div>

<script>
$(document).ready(function(){

	$("#modal1").modal();

});
</script>







		<?php include("@/script.php"); ?>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/58432a0c8a20fc0cac4bcca0/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
    </body>
</html>