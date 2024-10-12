<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "General Settings";
	require_once 'header.php'; 
	
	$updated = false;
	
	if (isset($_POST['website'])){
		
		if ($sitename != $_POST['sitename']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `sitename` = :sitename");
			$SQL -> execute(array(':sitename' => $_POST['sitename']));
			$sitename = $_POST['sitename'];
			$updated = true;
		}

		if ($description != $_POST['description']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `description` = :description");
			$SQL -> execute(array(':description' => $_POST['description']));
			$description = $_POST['description'];
			$updated = true;
		}
		
		if ($tos != $_POST['tos']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `tos` = :tos");
			$SQL -> execute(array(':tos' => $_POST['tos']));
			$tos = $_POST['tos'];
			$updated = true;
		}
		
		if ($siteurl != $_POST['url']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `url` = :url");
			$SQL -> execute(array(':url' => $_POST['url']));
			$siteurl = $_POST['url'];
			$updated = true;
		}
		
		if ($siteurl != $_POST['maintenance']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `maintaince` = :maintenance");
			$SQL -> execute(array(':maintenance' => $_POST['maintenance']));
			$maintaince = $_POST['maintenance'];
			$updated = true;
		}
		
		if ($google_site != $_POST['google_site']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `google_site` = :google_site");
			$SQL -> execute(array(':google_site' => $_POST['google_site']));
			$google_site = $_POST['google_site'];
			$updated = true;
		}
		
		if ($google_secret != $_POST['google_secret']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `google_secret` = :google_secret");
			$SQL -> execute(array(':google_secret' => $_POST['google_secret']));
			$google_secret = $_POST['google_secret'];
			$updated = true;
		}
		
		if (isset($_POST['cloudflare'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `cloudflare` = '1'");
			$cloudflare = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `cloudflare` = '0'");
			$cloudflare = 0;
			$updated = true;
		}
		
		if($updated == true){
			$done = "Website settings have been updated";
		}
	}
	
	if(isset($_POST['billing'])){
	
		if ($coinpayments != $_POST['coinpayments']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `coinpayments` = :coinpayments");
			$SQL -> execute(array(':coinpayments' => $_POST['coinpayments']));
			$insert = $odb -> prepare("INSERT INTO `reports` VALUES (NULL, ?, ?, ?)");
			$insert -> execute(array($_SESSION['username'], 'Changing payment settings', time()));
			$coinpayments = $_POST['coinpayments'];
			$updated = true;
		}

		if ($ipnSecret != $_POST['ipnSecret']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `ipnSecret` = :ipnSecret");
			$SQL -> execute(array(':ipnSecret' => $_POST['ipnSecret']));
			$ipnSecret = $_POST['ipnSecret'];
			$updated = true;
		}
		
		if (isset($_POST['paypal'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `paypal` = '1'");
			$paypal = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `paypal` = '0'");
			$paypal = 0;
			$updated = true;
		}
		
		if (isset($_POST['bitcoin'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `bitcoin` = '1'");
			$bitcoin = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `bitcoin` = '0'");
			$bitcoin = 0;
			$updated = true;
		}
		
		if($updated == true){
			$done = "Website settings have been updated";
		}
	}
	
	if(isset($_POST['stresser'])){

		if ($maxattacks != $_POST['maxattacks']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `maxattacks` = :maxattacks");
			$SQL -> execute(array(':maxattacks' => $_POST['maxattacks']));
			$maxattacks = $_POST['maxattacks'];
			$updated = true;
		}

		if ($skype != $_POST['skype']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `skype` = :skype");
			$SQL -> execute(array(':skype' => $_POST['skype']));
			$skype = $_POST['skype'];
			$updated = true;
		}
		
		if ($system != $_POST['system']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `system` = :system");
			$SQL -> execute(array(':system' => $_POST['system']));
			$system = $_POST['system'];
			$updated = true;
		}
	
		
		if (isset($_POST['cooldown'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `cooldown` = '1'");
			$cooldown = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `cooldown` = '0'");
			$cooldown = 0;
			$updated = true;
		}
		
		if ($cooldownTime != $_POST['cooldownTime']){
			
			$input = ($_POST['cooldownTime'] + time());
			$SQL = $odb -> prepare("UPDATE `settings` SET `cooldownTime` = :cooldownTime");
			$SQL -> execute(array(':cooldownTime' => $input));
			$cooldownTime = $_POST['cooldownTime'];
			$updated = true;
		}

		if (isset($_POST['uniqueattacks'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `unique_attacks` = '1'");
			$uniqueattacks = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `unique_attacks` = '0'");
			$uniqueattacks = 0;
			$updated = true;
		}

		if (isset($_POST['rotation'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `rotation` = '1'");
			$rotation = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `rotation` = '0'");
			$rotation = 0;
			$updated = true;
		}
		
		if (isset($_POST['testboots'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `testboots` = '1'");
			$testboots = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `testboots` = '0'");
			$testboots = 0;
			$updated = true;
		}
		
		if($updated == true){
			$done = "Website settings have been updated";
		}
	}
	
	if(isset($_POST['designer'])){

		if ($theme != $_POST['theme']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `theme` = :theme");
			$SQL -> execute(array(':theme' => $_POST['theme']));
			$theme = $_POST['theme'];
			$updated = true;
		}
		
		if ($logo != $_POST['logo']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `logo` = :logo");
			$SQL -> execute(array(':logo' => $_POST['logo']));
			$logo = $_POST['logo'];
			$updated = true;
		}
		
		if($updated == true){
			$done = "Designer settings have been updated";
		}
	}	
	
?>


  <!-- Page Content -->
<title><?php echo $sitename; ?> | Settings</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Settings</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Settings</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
			<div class="container-fluid">
	  <?php
		if(isset($done)){
			echo success($done);
		}
		?>
      <div class="row">
	     <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
				<form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												  <label for="site-name">Name</label>
                                                    <input class="form-control" type="text" id="site-name" name="sitename" value="<?php echo htmlspecialchars($sitename); ?>">
                                                  
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="site-desc">Description</label>
                                                    <input class="form-control" type="text" id="site-desc" name="description" value="<?php echo htmlspecialchars($description); ?>">
                                                    
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												 <label for="site-url">Website URL</label>
                                                    <input class="form-control" type="text" id="site-url" name="url" value="<?php echo htmlspecialchars($siteurl); ?>">
                                                    
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="maintenance">Maintenance</label>
                                                    <input class="form-control" type="text" id="maintenance" name="maintenance" value="<?php echo htmlspecialchars($maintaince); ?>" placeholder="Leave empty to disable">
                                                    
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="google_site">Google ReCaptcha Public</label>
                                                    <input class="form-control" type="text" id="google_site" name="google_site" value="<?php echo htmlspecialchars($google_site); ?>" placeholder="Find these details in Google ReCaptcha">
                                                    
													
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="google_secret">Google ReCaptcha Secret</label>
                                                    <input class="form-control" type="text" id="google_secret" name="google_secret" value="<?php echo htmlspecialchars($google_secret); ?>" placeholder="Find these details in Google ReCaptcha">
                                                    
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="cloudflare" type="checkbox" <?php if ($cloudflare == 1) { echo 'checked'; } ?>><span></span> Cloudflare Mode
												</label>
											</div>
                                        </div> 
                                        <div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="website" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
				

          </div>
        </div>
		<div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
			 <form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="maxattack">Max Attack Slot</label>
                                                    <input class="form-control" type="number" id="maxattack" name="maxattacks" value="<?php echo htmlspecialchars($maxattacks); ?>">
                                                    
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												 <label for="attacktype">Attack System</label>
                                                    <select class="form-control" id="attacktype" name="system" size="1">
                                                        <option value="api" <?php if ($system == 'api') { echo 'selected'; } ?>>API</option>
														<option value="servers" <?php if ($system == 'servers') { echo 'selected'; } ?>>Servers</option>
                                                    </select>
                                                   
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="rotation" type="checkbox" <?php if ($rotation == 1) { echo 'checked'; } ?>><span></span> Rotation
												</label>
											</div>
											<div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="testboots" type="checkbox" <?php if ($testboots == 1) { echo 'checked'; } ?>><span></span> Test Boots
												</label>
											</div>
                                            <div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="uniqueattacks" type="checkbox" <?php if ($uniqueattacks == 1) { echo 'checked'; } ?>><span></span> Unique Attacks
												</label>
											</div>
                                        </div> 
										<hr>
										<div class="form-group">
                                            <div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="cooldown" type="checkbox" <?php if ($cooldown == 1) { echo 'checked'; } ?>><span></span> Cool Down
												</label>
											</div>
											<div class="col-xs-12">
												<label for="attacktype">Time for Cooldown Lasts</label>
                                                    <select class="form-control" id="cooldownTime" name="cooldownTime" size="1">
                                                        <option value="1800">30 Minutes</option>
														<option value="3600">1 Hour</option>
														<option value="5400">1 Hour and Half</option>
														<option value="7200">2 Hours</option>
														<option value="9000">2 Hours and half</option>
                                                    </select>
											</div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="stresser" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
          </div>
        </div>
      </div>
	  
      <!--/.row -->