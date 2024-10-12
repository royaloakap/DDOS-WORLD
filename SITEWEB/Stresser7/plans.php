<?php
include("@/header.php");
$paginaname = 'Plans';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->

			 <div class="page-wrapper">
			  <div class="page-content">
		     <div class="alert alert-fill-primary">	
           <span data-feather="shopping-cart" class="icon-md text-light mr-2"></span>
			  <span><?php echo htmlspecialchars($paginaname); ?></span>
            </div>			
										        <div class="row">
												<?php
												$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `price` ASC");
												while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC))
												{
													$name = $getInfo['name'];
													$premium = $getInfo['premium'];
							if($premium == 0)
							{
								$premium = "No";
							}
							else 
							{
								$premium = "Yes";
							}
													$price = $getInfo['price'];
													$length = $getInfo['length'];
													$unit = $getInfo['unit'];
													$concurrents = $getInfo['concurrents'];
													$mbt = $getInfo['mbt'];
													$ID = $getInfo['ID'];
													
													echo '
                <div class="col-md-4 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h5 class="text-center text-uppercase mt-3 mb-4">'.htmlspecialchars($name).'</h5>
                  <h3 class="text-center font-weight-light">$'.htmlspecialchars($price).'</h3>
                  <p class="text-muted text-center mb-4 font-weight-light">per '.htmlspecialchars($length).' days</p>
                  <div class="d-flex align-items-center mb-2">
                    <i data-feather="minus" class="icon-md text-primary mr-2"></i>
                    <p>Attack time: '.htmlspecialchars($mbt).'</p>
                  </div>
                  <div class="d-flex align-items-center mb-2">
                    <i data-feather="minus" class="icon-md text-primary mr-2"></i>
                    <p>Concurrents: '.htmlspecialchars($concurrents).'</p>
                  </div>
                  <div class="d-flex align-items-center mb-2">
                    <i data-feather="star" class="icon-md text-light mr-2"></i>
                    <p>Premium Methods: '.htmlspecialchars($premium).'</p>
                  </div>
                    <a href="bitcoin.php?id='.$ID.'" class="btn btn-primary d-block mx-auto mt-4">
                     <i class="link-icon" data-feather="pocket"></i>
                     <span class="link-title">Purchase</span>
                   </a>
                </div>
              </div>
            </div>';
												}
												?>
												
							   </div>
							 </div>
						  </div>						
					   </div>
					</div>
</html>
