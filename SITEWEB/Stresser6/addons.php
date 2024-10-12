<?php

$paginaname = 'Addons';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
  <script src="../assets/js/spinner.js"></script>
			<?php 
			
			include("@/header.php");

			?>
			 <div class="page-wrapper">
			  <div class="page-content">
		     <div class="alert alert-fill-primary">	
           <span data-feather="shopping-cart" class="icon-md text-light mr-2"></span>
			  <span><?php echo htmlspecialchars($paginaname); ?></span>
            </div>			
										        <div class="row">
												<?php
												$SQLGetAddons = $odb -> query("SELECT * FROM `addons` ORDER BY `price` ASC");
												while ($getInfo = $SQLGetAddons -> fetch(PDO::FETCH_ASSOC))
												{
													$addon = $getInfo['addon'];
													$price = $getInfo['price'];
													$id = $getInfo['id'];
													
													echo '
                <div class="col-md-4 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h5 class="text-center text-uppercase mt-3 mb-4">'.htmlspecialchars($addon).'</h5>
                  <h3 class="text-center font-weight-light">$'.htmlspecialchars($price).'</h3>
                    <a href="payment.php?id='.$id.'" class="btn btn-primary d-block mx-auto mt-4">
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
