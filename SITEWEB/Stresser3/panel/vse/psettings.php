<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Payment Settings";
	require_once 'header.php'; 
	
	$updated = false;
	if(isset($_POST['billing1'])){
		
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
		
		if (isset($_POST['stripe'])){ 
			$SQL = $odb -> query("UPDATE `settings` SET `stripe` = '1'");
			$stripe = 1;
			$updated = true;
		}
		else{
			$SQL = $odb -> query("UPDATE `settings` SET `stripe` = '0'");
			$stripe = 0;
			$updated = true;
		}
		
	}
	
	if(isset($_POST['billing2'])){
	
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
	}
	
	if(isset($_POST['billing3'])){
	
		if ($stripePubKey != $_POST['stripePubKey']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `stripePubKey` = :stripePubKey");
			$SQL -> execute(array(':stripePubKey' => $_POST['stripePubKey']));
			$stripePubKey = $_POST['stripePubKey'];
			$updated = true;
		}

		if ($stripeSecretKey != $_POST['stripeSecretKey']){
			$SQL = $odb -> prepare("UPDATE `settings` SET `stripeSecretKey` = :stripeSecretKey");
			$SQL -> execute(array(':stripeSecretKey' => $_POST['stripeSecretKey']));
			$stripeSecretKey = $_POST['stripeSecretKey'];
			$updated = true;
		}
	}
		
		
		if($updated == true){
			$done = "Billing settings have been updated";
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
		if(isset($done)){
			echo success($done);
		}
		?>
      <div class="row">
	     <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Payment Settings</h3>
				<form class="form-horizontal" method="post">
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
											<div class="col-xs-12">
												<label class="css-input css-checkbox css-checkbox-info">
													<input name="stripe" type="checkbox" <?php if ($stripe == 1) { echo 'checked'; } ?>><span></span> Enable Stripe
												</label>
											</div>
                                        </div> 
                                        <div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="billing1" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
											</div>
                                    </form>

          </div>
        </div>
		 <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">CoinPayments Settings</h3>
			<form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												                                                    <label for="merchant">Merchant ID</label>
                                                    <input class="form-control" type="text" id="merchant" name="coinpayments" value="<?php echo htmlspecialchars($coinpayments); ?>">
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="secret">Secret Key</label>
                                                    <input class="form-control" type="text" id="secret" name="ipnSecret" value="<?php echo htmlspecialchars($ipnSecret); ?>">
                                                    
												
                                                </div>
                                            </div>
                                        </div> 
										 <div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="billing2" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
				

          </div>
        </div>
		 <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Stripe Settings</h3>
			<form class="form-horizontal push-10-t" method="post">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
													<label for="merchant">Stripe Publishable Key</label>
                                                    <input class="form-control" type="text" id="stripePubKey" name="stripePubKey" value="<?php echo htmlspecialchars($stripePubKey); ?>">
                                                </div>
                                            </div>
                                        </div> 
										<div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
												<label for="secret">Stripe Secret Key</label>
                                                    <input class="form-control" type="text" id="stripeSecretKey" name="stripeSecretKey" value="<?php echo htmlspecialchars($stripeSecretKey); ?>">
                                                    
												
                                                </div>
                                            </div>
                                        </div> 
										 <div class="form-group">
                                            <div class="col-sm-9">
                                                <button name="billing3" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </form>
				

          </div>
        </div>
      </div>
	  
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>