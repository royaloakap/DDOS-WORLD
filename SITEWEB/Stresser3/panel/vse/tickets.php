<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
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


<title><?php echo $sitename; ?> | Tickets</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Tickets</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Tickets</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
	  <div class="container-fluid">
      <div class="row">

		 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title"></h3>
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
				xmlhttp.open("GET","../vvv/files/vux/tickets/inbox.php",true);
				xmlhttp.send();
			}
	</script>
      <!--/.row -->