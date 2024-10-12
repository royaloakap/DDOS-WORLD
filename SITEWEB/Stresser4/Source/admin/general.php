<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
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
			<main id="main-container" style="min-height: 404px;">                
                <div class="content bg-gray-lighter">
                    <div class="row items-push">
                        <div class="col-sm-8">
                            <h1 class="page-heading">
                                Settings <small>General settings</small>
                            </h1>
                        </div>
                        <div class="col-sm-4 text-right hidden-xs">
                            <ol class="breadcrumb push-10-t">
                                <li>Settings</li>
                                <li><a class="link-effect" href="general.php">General</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content content-narrow">
					<?php
					if(isset($done)){
						echo '<div class="row col-md-12">' . success($done) . "</div>";
					}
					?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="block">
                                <div class="block-header bg-primary">
                                    <h3 class="block-title">Website Settings</h3>
                                </div>
                                <div class="block-content block-content-narrow">
                                    <form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="site-name" name="sitename" value="<?php echo htmlspecialchars($sitename); ?>">
                                                    <label for="site-name">Name</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="site-desc" name="description" value="<?php echo htmlspecialchars($description); ?>">
                                                    <label for="site-desc">Description</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="site-tos" name="tos" value="<?php echo htmlspecialchars($tos); ?>">
                                                    <label for="site-tos">ToS URL</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="site-url" name="url" value="<?php echo htmlspecialchars($siteurl); ?>">
                                                    <label for="site-url">Website URL</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="maintenance" name="maintenance" value="<?php echo htmlspecialchars($maintaince); ?>" placeholder="Leave empty to disable">
                                                    <label for="maintenance">Maintenance</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="google_site" name="google_site" value="<?php echo htmlspecialchars($google_site); ?>" placeholder="Find these details in Google ReCaptcha">
                                                    <label for="google_site">Google ReCaptcha Public</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="google_secret" name="google_secret" value="<?php echo htmlspecialchars($google_secret); ?>" placeholder="Find these details in Google ReCaptcha">
                                                    <label for="google_secret">Google ReCaptcha Secret</label>
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
                        </div>
						<div class="col-md-4">
                            <div class="block">
                                <div class="block-header bg-primary">
                                    <h3 class="block-title">Billing Settings</h3>
                                </div>
                                <div class="block-content block-content-narrow">
                                    <form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="merchant" name="coinpayments" value="<?php echo htmlspecialchars($coinpayments); ?>">
                                                    <label for="merchant">Merchant ID</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="secret" name="ipnSecret" value="<?php echo htmlspecialchars($ipnSecret); ?>">
                                                    <label for="secret">Paypal Email</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="bitcoin" type="checkbox" <?php if ($bitcoin == 1) { echo 'checked'; } ?>><span></span> Enable Bitcoin
												</label>
											</div>
											<div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="paypal" type="checkbox" <?php if ($paypal == 1) { echo 'checked'; } ?>><span></span> Enable PayPal
												</label>
											</div>
                                        </div> 
                                        <div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="billing" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
							<div class="block">
                                <div class="block-header bg-primary">
                                    <h3 class="block-title">Desginer Settings</h3>
                                </div>
                                <div class="block-content block-content-narrow">
                                    <form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <select class="form-control" id="theme" name="theme" size="1">
                                                        <option value="default.min.css" <?php if ($theme == 'default.min.css') { echo 'selected'; } ?>>Default</option>
														<option value="amethyst.min.css" <?php if ($theme == 'amethyst.min.css') { echo 'selected'; } ?>>Amethyst</option>
														<option value="city.min.css" <?php if ($theme == 'city.min.css') { echo 'selected'; } ?>>City</option>
														<option value="flat.min.css" <?php if ($theme == 'flat.min.css') { echo 'selected'; } ?>>Flat</option>
														<option value="modern.min.css" <?php if ($theme == 'modern.min.css') { echo 'selected'; } ?>>Modern</option>
														<option value="smooth.min.css" <?php if ($theme == 'smooth.min.css') { echo 'selected'; } ?>>Smooth</option>
													</select>
                                                    <label for="theme">Website Theme</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <select class="form-control" id="logo" name="logo" size="1">
                                                        <option value="fire" <?php if ($logo == 'fire') { echo 'selected'; } ?>>Fire</option>
														<option value="diamond" <?php if ($logo == 'diamond') { echo 'selected'; } ?>>Diamond</option>
														<option value="energy" <?php if ($logo == 'energy') { echo 'selected'; } ?>>Energy</option>
														<option value="game-controller" <?php if ($logo == 'game-controller') { echo 'selected'; } ?>>Game Controller</option>
														<option value="globe" <?php if ($logo == 'globe') { echo 'selected'; } ?>>Globe</option>
														<option value="shield" <?php if ($logo == 'shield') { echo 'selected'; } ?>>Shield</option>
													</select>
                                                    <label for="logo">Website Logo</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="designer" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div class="block">
                                <div class="block-header bg-primary">
                                    <h3 class="block-title">Stresser Settings</h3>
                                </div>
                                <div class="block-content block-content-narrow">
                                    <form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="number" id="maxattack" name="maxattacks" value="<?php echo htmlspecialchars($maxattacks); ?>">
                                                    <label for="maxattack">Max Attack Slot</label>
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="text" id="skype" name="skype" value="<?php echo htmlspecialchars($skype); ?>">
                                                    <label for="skype">Skype API</label>
                                                </div>
                                            </div>
                                        </div>
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <select class="form-control" id="attacktype" name="system" size="1">
                                                        <option value="api" <?php if ($system == 'api') { echo 'selected'; } ?>>API</option>
														<option value="servers" <?php if ($system == 'servers') { echo 'selected'; } ?>>Servers</option>
                                                    </select>
                                                    <label for="attacktype">Attack System</label>
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
                    </div>     
                </div>
            </main>
<?php

	include 'footer.php';

?>