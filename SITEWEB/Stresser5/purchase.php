<?php

$paginaname = 'Purchase';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php 
			
			include("@/header.php");

			?>
                    <div id="page-content">
            
                        <div class="row">	
						<div class="col-sm-6">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted"><?php echo $date1; ?></span>
								<i class="fa fa-tasks"></i> <b>Power Proof</b>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
								<iframe width="530" height="295" src="https://www.youtube.com/embed/<?php echo $video1; ?>" frameborder="0" allowfullscreen></iframe>	
								</div>
							</div>
						</div>
						
						<div class="col-sm-6">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted"><?php echo $date2; ?></span>
								<i class="fa fa-tasks"></i> <b>Power Proof</b>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
										<iframe width="530" height="295" src="https://www.youtube.com/embed/<?php echo $video2; ?>" frameborder="0" allowfullscreen></iframe>	
									
								</div>
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted">All payments are manual!</span>
								<i class="fa fa-shopping-cart"></i> <b>Purchase</b>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
									<div id="stats">
										<table class="table table-striped">
											<tbody>
												<tr>
													<th><center>Package</center></th>
													<th><center>Length</center></th>
													<th><center>Boot</center></th>
													<th><center>Price</center></th>
													<th><center>Payment Methods</center></th>
												</tr>
												<?php
												$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `price` ASC");
												while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC))
												{
													$name = $getInfo['name'];
													$price = $getInfo['price'];
													$length = $getInfo['length'];
													$unit = $getInfo['unit'];
													$concurrents = $getInfo['concurrents'];
													$mbt = $getInfo['mbt'];
													$ID = $getInfo['ID'];
													
													echo '
													<tr>
														<td><center>'.htmlspecialchars($name).'</center></td>
														<td><center>'.$length.' '.htmlspecialchars($unit).'</center></td>
														<td><center>'.$mbt.'sec and '.$concurrents.' concurrents</center></td>
														<td><center>$'.$price.'</td>
														<td><center>
														<a href="paypal.php?id='.$ID.'"><img src="img/paypal.png" /></a>
														<a href="bitcoin.php?id='.$ID.'"><img src="img/bitcoin.png" /></a>
														</center></td>
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