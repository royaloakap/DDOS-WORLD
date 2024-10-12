<?php

	$page = "Products";

	include 'header.php';

?>

<main id="main-container" style="min-height: 404px;"> 

	<div class="content bg-gray-lighter">

		<div class="row items-push">

			<div class="col-sm-8">

				<h1 class="page-heading">

					Products <small><?php echo $sitename; ?> Products</small>

				</h1>

			</div>

			<div class="col-sm-4 text-right hidden-xs">

				<ol class="breadcrumb push-10-t">

					<li>Home</li>

					<li><a class="link-effect" href="products.php">Products</a></li>

				</ol>

			</div>

		</div>

	</div>

	<div class="content content-narrow">

		<?php

		if(isset($notify)){

			echo '<div class="row col-md-12">' . $notify . "</div>";

		}

		?>

		<div class="row">  

			<div class="col-md-12">

				<div class="block">

					<div class="block-header bg-primary">

						<h3 class="block-title">Upgrade Options</h3>

					</div>

					<div class="block-content">

						<div class="table-responsive">

							<table class="table table-striped table-vcenter">

								<thead>

									<tr>

										<th class="text-center" style="font-size: 12px;">Name</th>
										
										<th class="text-center" style="font-size: 12px;">Network</th>

										<th class="text-center" style="font-size: 12px;">Price</th>

										<th class="text-center" style="font-size: 12px;">Attack Time</th>

										<th class="text-center" style="font-size: 12px;">Length</th>

										<th class="text-center" style="font-size: 12px;">Actions</th>

									</tr>

								</thead>

								<tbody style="font-size: 12px;" class="text-center">

								<?php

								$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `price` ASC");

								while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC)){

									$name = $getInfo['name'];

									$price = $getInfo['price'];

									$length = $getInfo['length'];

									$unit = $getInfo['unit'];

									$concurrents = $getInfo['concurrents'];

									$mbt = $getInfo['mbt'];

									$ID = $getInfo['ID'];

								    if($bitcoin == 1){ $BitcoinAccepted = '<a href="purchase/buy_plan.php?id='.$ID.'"><img src="assets/img/favicons/bitcoin.png" style="width: 12px;"></a>'; } else { $BitcoinAccepted = ''; }

									if($paypal == 1){ $PayPalAccepted = '<a href="paypal.php?id='.$ID.'"><img src="assets/img/favicons/paypal.jpg" style="width: 17px;"></a>'; } else { $PayPalAccepted = ''; }

									if($bitcoin == 0 && $paypal == 0){ $NeitherAccepted = 'Sales are currently closed'; } else { $NeitherAccepted = ''; }

									echo '<tr>

											<td>'.htmlspecialchars($name).'</td>
											
											<td>Normal Network</td>

											<td>$'.htmlentities($price).'</td>

											<td>'.htmlentities($mbt).'sec</td>

											<td>'.htmlentities($length).' '.htmlspecialchars($unit).'</td>

											<td>

												'. $BitcoinAccepted .'

												'. $PayPalAccepted .'

												'. $NeitherAccepted .'

											</td>

										  </tr>';

								}

								?>

								</tbody>

							</table>

						</div>

					</div>

				</div>

			</div>
			
			<div class="col-md-12">

				<div class="block">

					<div class="block-header bg-primary">
					
						<h3 class="block-title">VIP Options</h3>

					</div>
					
				

					<div class="block-content">

						<div class="table-responsive">

							<table class="table table-striped table-vcenter">

								<thead>

									<tr>

										<th class="text-center" style="font-size: 12px;">Name</th>
										
										<th class="text-center" style="font-size: 12px;">Network</th>

										<th class="text-center" style="font-size: 12px;">Price</th>

										<th class="text-center" style="font-size: 12px;">Attack Time</th>

										<th class="text-center" style="font-size: 12px;">Length</th>

										<th class="text-center" style="font-size: 12px;">Actions</th>

									</tr>

								</thead>

								<tbody style="font-size: 12px;" class="text-center">

								<?php

								$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 2 ORDER BY `price` ASC");

								while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC)){

									$name = $getInfo['name'];

									$price = $getInfo['price'];

									$length = $getInfo['length'];

									$unit = $getInfo['unit'];

									$concurrents = $getInfo['concurrents'];

									$mbt = $getInfo['mbt'];

									$ID = $getInfo['ID'];

								    if($bitcoin == 1){ $BitcoinAccepted = '<a href="purchase/buy_plan.php?id='.$ID.'"><img src="assets/img/favicons/bitcoin.png" style="width: 12px;"></a>'; } else { $BitcoinAccepted = ''; }

									if($paypal == 1){ $PayPalAccepted = '<a href="paypal.php?id='.$ID.'"><img src="assets/img/favicons/paypal.jpg" style="width: 17px;"></a>'; } else { $PayPalAccepted = ''; }

									echo '<tr>

											<td>'.htmlspecialchars($name).'</td>

											<td>VIP Network</td>
											
											<td>$'.htmlentities($price).'</td>

											<td>'.htmlentities($mbt).'sec</td>

											<td>'.htmlentities($length).' '.htmlspecialchars($unit).'</td>

											<td>

												'. $BitcoinAccepted .'

												'. $PayPalAccepted .'

												'. $NeitherAccepted .'

											</td>

										  </tr>';

								}

								?>

								</tbody>

							</table>

						</div>

					</div>

				</div>

			</div>
			<?php 
			if (isset($_POST['purchase_month']))
			{
				$host = $_POST['host'];
				if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
					echo '<div class="col-md-12"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>ERROR:</strong> Host is not a valid URL</div></div>';
				} else {
					header('Location: purchase/buy_blacklist.php?type=month&host='.$host.'');
				}
			}
			
			if (isset($_POST['purchase_lifetime']))
			{
				$host = $_POST['host'];
				if (filter_var($host, FILTER_VALIDATE_URL) === FALSE) {
					echo '<div class="col-md-12"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>ERROR:</strong> Host is not a valid URL</div></div>';
				} else {
					header('Location: purchase/buy_blacklist.php?type=lifetime&host='.$host.'');
				}
			}
			
			?>
			<div class="col-md-6">
			
				<div class="block">

					<div class="block-header bg-primary">
					
						<h3 class="block-title">Host Blacklist - Month</h3>

					</div>
					
				

					<div class="block-content">
					<form method="post">
						<div class="form-material floating">
							<input class="form-control" name="host" type="text">
							<label for="material-help2">Host</label>
							<div class="help-block text-right">http://example.com <br><br> If you bought it and your domain isn't in blacklist list open ticket and put transaction-</div>
						</div>
						<div class="form-material floating">
							<h1><center>Price: $20</center></h1>
						</div>
						<div class="form-material floating">
							<button name="purchase_month" class="btn btn-sm btn-primary btn-block" type="submit">Buy with CoinPayments!</button>
						</div>
					</form>
					</div>

				</div>

			</div>
			
			<div class="col-md-6">

				<div class="block">

					<div class="block-header bg-primary">
					
						<h3 class="block-title">Host Blacklist - Lifetime</h3>

					</div>
					
				

					<div class="block-content">
					<form method="post">
						<div class="form-material floating">
							<input class="form-control" name="host" type="text">
							<label for="material-help2">Host</label>
							<div class="help-block text-right">http://example.com <br><br> If you bought it and your domain isn't in blacklist list open ticket and put transaction-</div>
						</div>
						<div class="form-material floating">
							<h1><center>Price: $200</center></h1>
						</div>
						<div class="form-material floating">
							<button name="purchase_lifetime" class="btn btn-sm btn-primary btn-block" type="submit">Buy with CoinPayments!</button>
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