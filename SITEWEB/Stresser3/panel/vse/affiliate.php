<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Affiliate System";
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
	
	     <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Withdraw Requests</h3>
				<table class="table">
						<tr>
							<th style="font-size: 12px;">Username</th>
							<th class="text-center" style="font-size: 12px;">Withdraw (Amount)</th>
							<th class="text-center" style="font-size: 12px;">Payment Method</th>
							<th class="text-center" style="font-size: 12px;">Payment Address</th>
							<th class="text-center" style="font-size: 12px;">Status</th>
							<th class="text-center" style="font-size: 12px;">Actions (Mark)</th>
						</tr>
						<tr>
						<?php
							$SQLSelect = $odb -> query("SELECT * FROM `affiliateWithdraws` ORDER BY `ID` ASC LIMIT 20");
							while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
							{
								$ID = $show['unit'];
								$userID = $show['userID'];
								$withdrawAmount = $show['withdrawAmount'];
								$paymentMethod = $show['paymentMethod'];
								$paymentAddress = $show['paymentAddress'];
								$date = $show['date'];
								$status = $show['status']; if($status == "0") { $status = '<font color="red">Pending</font>'; } else { $status = '<font color="green">Completed</font>'; }
								$usernName = $odb->query("SELECT `username` FROM `users` WHERE `ID` = '$userID'")->fetchColumn(0);
								echo '<tr">
										<td class="text-center" style="font-size: 12px;">'.$usernName.'</td>
										<td class="text-center" style="font-size: 12px;">$'.htmlentities($withdrawAmount).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($paymentMethod).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($paymentAddress).'</td>
										<td class="text-center" style="font-size: 12px;">'.($status).'</td>
										<td class="text-center" style="font-size: 12px;"><button class="btn btn-outline  btn-sm btn-info">Complete</button> </td>
									</tr>';
							
							} 
							?>
									</tr>                                       
					</table>
          </div>
        </div>

		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Affiliate  Settings</h3>
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="plan">Plan</label>
										<select class="form-control" id="plan" name="plan">
											<?php
											$SQLGetMethods = $odb -> query("SELECT * FROM `plans`");
											while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
												$ID = $getInfo['ID'];
												$name = $getInfo['name'];
												echo '<option value="'.$ID.'">'.$name.'</option>';
											}
											?>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="createnewCard" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>
			
          </div>
        </div>
		
      </div>
	  
<?php

	require_once 'footer.php';
	
?>