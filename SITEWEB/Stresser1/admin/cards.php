<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Gift Cards";
	require_once 'header.php'; 
	
	if(isset($_POST['createnewCard']))
	{
		$plan = $_POST['plan'];

		if(empty($plan))
		{
			$notify = error('Plan input was empty!');
		}

		if(empty($notify))
		{
			 /// Generate Gift Code
			$code = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 10);
			
			 /// Input to database
			$SQLinsert = $odb -> prepare("INSERT INTO `giftcards` VALUES(NULL, :code, :planID, 0, 0, UNIX_TIMESTAMP())");
			$SQLinsert -> execute(array(':code' => $code, ':planID' => $plan));	

			$notify = success('New Giftcard has been generated. New code is: '.$code.'');
		}	
	}
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
            <h3 class="box-title">Overview Giftcards</h3>
				<table class="table">
						<tr>
							<th style="font-size: 12px;">Code</th>
							<th class="text-center" style="font-size: 12px;">Plan</th>
							<th class="text-center" style="font-size: 12px;">Claimed By</th>
							<th class="text-center" style="font-size: 12px;">Date Claimed</th>
							<th class="text-center" style="font-size: 12px;">Date Created</th>
						</tr>
						<tr>
						<?php
							$SQLSelect = $odb -> query("SELECT * FROM `giftcards` ORDER BY `ID` ASC LIMIT 20");
							while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
							{
								$ID = $show['unit'];
								$code = $show['code'];
								$planID = $show['planID'];
								$claimedby = $show['claimedby'];
								$status = $show['status'];
								$dateClaimed = $show['dateClaimed'];
								$date = $show['date'];
								if(!($dateClaimed == "0"))
								{
									$dateClaimed = date("m-d-Y, h:i:s a" , $dateClaimed);
								}
								if($claimedby == "0") { $claimedby = "Unclaimed"; }
								if($dateClaimed == "0") { $dateClaimed = "Unclaimed"; }
								$date = date("m-d-Y, h:i:s a" , $date);
								$plan = $odb->query("SELECT `name` FROM `plans` WHERE `ID` = '$planID'")->fetchColumn(0);
								$usernName = $odb->query("SELECT `username` FROM `users` WHERE `ID` = '$claimedby'")->fetchColumn(0);
								echo '<tr">
										<td class="text-center" style="font-size: 12px;">'.$code.'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($plan).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($usernName).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($dateClaimed).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($date).'</td>
									</tr>';
							
							} 
							?>
									</tr>                                       
					</table>
          </div>
        </div>

		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add new giftcard</h3>
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