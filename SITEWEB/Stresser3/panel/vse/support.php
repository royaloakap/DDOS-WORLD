<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Support Tickets";
	require_once 'header.php'; 
	
	/// Querys for the stats below
	$TotalUsers = $odb->query("SELECT COUNT(*) FROM `users`")->fetchColumn(0);
	$TodayAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()")->fetchColumn(0);
	$MonthAttack = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()  - INTERVAL 30 DAY")->fetchColumn(0);
	$TotalAttacks = $odb->query("SELECT COUNT(*) FROM `logs`")->fetchColumn(0);
	$TotalPools = $odb->query("SELECT COUNT(*) FROM `api`")->fetchColumn(0);
	$TotalPayments = $odb->query("SELECT COUNT(*) FROM `payments`")->fetchColumn(0);
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$TotalYesBoots = $odb->query("SELECT COUNT(id) FROM `logs` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-2' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$totlalUsersauth = $odb->query("SELECT SUM(2auth) FROM `users` WHERE `2auth` = '1'")->fetchColumn(0);
	// Tickets
	$OpenTickets = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `status` = 'Waiting for admin response'")->fetchColumn(0);
	$TotalTickets = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `status` = 'Waiting for user response'")->fetchColumn(0);
	$ClosedTickets = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `status` = 'Closed'")->fetchColumn(0);
	$TotalIN = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `status` = 'Waiting for admin response'")->fetchColumn(0);
?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?></h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
          <ol class="breadcrumb">
            <li><a href="#"><?php echo $sitename; ?></a></li>
            <li class="active"><?php echo $page; ?></li>
          </ol>
        </div>
        <!-- /.col-lg-12 -->
      </div>
      <!-- .row -->
      <div class="row">
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Total Open Tickets</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash"></div>
              </li>
              <li class="text-right"><i class=" text-success"></i> <span class="counter text-success"><?php echo $OpenTickets; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Total Closed Tickets</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash2"></div>
              </li>
              <li class="text-right"><i class=" text-purple"></i> <span class="counter text-purple"><?php echo $ClosedTickets; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Total Tickets</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash3"></div>
              </li>
              <li class="text-right"><i class=" text-info"></i> <span class="counter text-info"><?php echo $TotalTickets; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Total Users</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash4"></div>
              </li>
              <li class="text-right"><i class=" text-danger"></i> <span class="text-danger"><?php echo $TotalUsers; ?></span></li>
            </ul>
          </div>
        </div>
      </div>
      <!--/.row -->
      <!-- .row -->
     
      <!--/.row -->
      <!-- .row -->
      <div class="row">

		 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title">Support Tickets</h3>
           <div class="content" id="messages"></div>
          </div>
        </div>
      </div>
		<script>
			inbox();
			
			function inbox() {
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp = new XMLHttpRequest();
				}
				else {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function(){
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById("messages").innerHTML = xmlhttp.responseText;
						eval(document.getElementById("ajax").innerHTML);
					}
				}
				xmlhttp.open("GET","../vvv/files/user/tickets/inbox.php",true);
				xmlhttp.send();
			}
	</script>
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>