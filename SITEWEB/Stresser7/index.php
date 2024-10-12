<?php
include("@/header.php");
$paginaname = 'Dashboard';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<script src="../assets/js/spinner.js"></script>
  <div class="page-wrapper">
     <div class="page-content">
	 	<div class="alert alert-fill-primary">	
          <span data-feather="home" class="icon-md text-light mr-2"></span>
	   <span><?php echo htmlspecialchars($paginaname); ?></span>
       </div>
   <div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow">
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Registred Members</h6>
              <div class="dropdown mb-2">
                
              </div>
            </div>
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <h3 class="mb-2"><?php echo $stats -> totalUsers($odb);; ?></h3>
                <div class="d-flex align-items-baseline">
                </div>
              </div>
              <div class="col-6 col-md-12 col-xl-7">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 60%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Servers</h6>
              <div class="dropdown mb-2">
                
              </div>
            </div>
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <h3 class="mb-2"><?php echo $stats -> serversonline($odb); ?></h3>
                <div class="d-flex align-items-baseline">
                </div>
              </div>
              <div class="col-6 col-md-12 col-xl-7">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 45%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
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
                
              </div>
            </div>
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <h3 class="mb-2"><?php echo $stats -> runningBoots($odb); ?></h3>
                <div class="d-flex align-items-baseline">
                </div>
              </div>
              <div class="col-6 col-md-12 col-xl-7">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 45%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
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
                
              </div>
            </div>
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <h3 class="mb-2"><?php echo $stats -> activeUsers($odb); ?></h3>
                <div class="d-flex align-items-baseline">
                </div>
              </div>
              <div class="col-6 col-md-12 col-xl-7">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 45%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              </div>
            </div>
          </div>
        </div>
       </div>
        </div>
         </div>
            </div>

                                              <div class="col-md-12">
                                               <div class="card">
                                                <div class="card-body">
                                                 <h6 class="card-title">News</h6>
                                                  <div id="content">       
													<?php
													$newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 2");
													while($row = $newssql ->fetch())
													{
													$id = $row['ID'];
													$title = $row['title'];
													$content = $row['content'];
													$autor = $row['author'];
													echo '
													    <div id="content">
													<ul class="timeline">
													<li class="event"> '.htmlspecialchars($content).'</li>                                                      
													   </ul>
													</div>
													';
													}
													?>			          
		  </div>
        </div>
      </div>
    </div>
  </div>
	 <?php include("@/footer.php"); ?>  
</body>