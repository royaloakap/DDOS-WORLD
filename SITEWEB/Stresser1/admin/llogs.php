<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Login Logs";
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
            <h3 class="box-title">Overview Logins</h3>
				<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">IP</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Country</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `loginlogs` ORDER BY `date` DESC LIMIT 30");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$username = $getInfo['username'];
								$ip = $getInfo['ip'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								$country = $getInfo['country'];
								echo '<tr>
										<td></td>
										<td>'.htmlspecialchars($username).'</td>
										<td>'.htmlspecialchars($ip).'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($country).'</td>
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