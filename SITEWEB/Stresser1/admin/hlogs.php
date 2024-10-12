<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Attack Logs";
	require_once 'header.php'; 
	
		
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
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
      <div class="row">
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Overview Payments</h3>
				<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Host</th>
									<th style="font-size: 12px;">Time</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Handler</th>
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
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
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
	  
<?php

	require_once 'footer.php';
	
?>