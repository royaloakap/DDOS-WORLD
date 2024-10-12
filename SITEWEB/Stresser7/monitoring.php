<?php

$paginaname = 'Monitoring';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php 
			
			include("@/header.php");

			?>
<script src="../assets/js/spinner.js"></script>
  <div class="page-wrapper">
     <div class="page-content">
		     <div class="alert alert-fill-primary">	
           <span data-feather="server" class="icon-md text-light mr-2"></span>
			  <span><?php echo htmlspecialchars($paginaname); ?></span>
            </div>			  
			<div class="col-lg-12">		
				<div class="row">		
				  <?php
						$newssql = $odb -> query("SELECT * FROM `api` LIMIT 0,10");
						while($row = $newssql ->fetch()){
							$name = $row['name'];
							$slots = $row['slots'];
                            $layer = $row['layer'];							
							$status = $row['status'];
							if($status == 0)
							{
								$status = "Online";
							}
							else 
							{
								$status = "Maintaince";
							}
							
							$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
							$load    = round($attacks / $slots * 100, 2);
					
							
							echo'
					 <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h5 class="text-center text-uppercase mt-3 mb-4">'.htmlspecialchars($name).'</h5>
                  <div class="d-flex align-items-center mb-2">
                    <i data-feather="monitor" class="icon-md text-light mr-2"></i>
                    <p>Running Attacks: '.$attacks.'/'.$slots.' </p>
                  </div>
                    <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width:'.$load.'%; aria-valuenow="'.$load.'" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
              </div>
            </div>';
				  
						}
						?>
                </ul>
			</div>
              </div>
			    </div>		  
    </body>
</html>