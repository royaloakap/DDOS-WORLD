<?php
include("header.php");
?>
	

                   <div class="page-wrapper">
                     <div class="page-content">                                                                                          
       <div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow">
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Registred Members</h6>
              <div class="dropdown mb-2">
               <?php echo $stats -> totalUsers($odb); ?>			  
              </div>
            </div>                                                                               
           </div>                                
          </div>
         </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Paid User</h6>
              <div class="dropdown mb-2">
               <?php echo $stats -> activeUsers($odb); ?>			  
              </div>
            </div>                                                                               
           </div>                                
          </div>
         </div>
		 <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Running Attack</h6>
              <div class="dropdown mb-2">
               <?php echo $stats -> runningBoots($odb); ?>			  
              </div>
            </div>                                                                               
           </div>                                
          </div>
         </div>
		 <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Server Onlie</h6>
              <div class="dropdown mb-2">
               <?php echo $stats -> serversonline($odb); ?>			  
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
</html>
