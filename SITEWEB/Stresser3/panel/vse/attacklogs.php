<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
		
?>


<title><?php echo $sitename; ?> | Attack Logs</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Attack Logs</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Attack Logs</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>


  <!-- Page Content -->
                 <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 col-lg-12">
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
				<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Host</th>
									<th style="font-size: 12px;">Time</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Servers</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetLogs = $odb -> query("SELECT * FROM `logs` ORDER BY `date` DESC LIMIT 600");
							while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
								$user = $getInfo['user'];
								$host = $getInfo['ip'];
								if (filter_var($host, FILTER_VALIDATE_URL)) {$port='';} else {$port=':'.$getInfo['port'];}
								$time = $getInfo['time'];
								$method = $getInfo['method'];
								$handler = $getInfo['handler'];
								$date = date("d-m-Y, h:i:s a" ,$getInfo['date']);
								echo '<tr>
										<td>'.htmlspecialchars($user).'</td>
										<td>'.htmlspecialchars($host).$port.' ('.htmlspecialchars($method).')<br></td>
										<td>'.$time.'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($handler).'</td>
									  </tr>';
							}
							?>	
							</tbody>
						</table>
			
          </div>
        </div>
		
      </div>