<?php

	$page = "Skrill";

	include 'header.php';
	
	if($_GET['id'] == "")
	{ 
		header("Location: index.php");
	} else {
			
		if(isset($_GET['id']) && Is_Numeric($_GET['id']) && $user -> LoggedIn())
		{
			$id = (int)$_GET['id'];
			$row = $odb -> query("SELECT * FROM `plans` WHERE `ID` = '$id'") -> fetch();
			if($row == "")
			{
				header("Location: index.php");
			} else {
	
	
?>

<main id="main-container" style="min-height: 404px;"> 

	<div class="content bg-gray-lighter">

		<div class="row items-push">

			<div class="col-sm-8">

				<h1 class="page-heading">

					Purchase <small><?php echo $row['name']; ?></small>

				</h1>

			</div>

			<div class="col-sm-4 text-right hidden-xs">

				<ol class="breadcrumb push-10-t">

					<li>Home</li>

					<li><a class="link-effect" href="products.php">Purchase Plan (<?php echo $_GET['id']; ?>)</a></li>

				</ol>

			</div>

		</div>

	</div>

	<div class="content content-narrow">

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


									</tr>

								</thead>

								<tbody style="font-size: 12px;" class="text-center">

									<tr>

											<td><?php echo $row['name']; ?></td>
											
											<td><?php 
											$mellamowillyrex = $row['private'];
											if($mellamowillyrex == 0)
											{
												$holakase = 'Normal Network';
											} else {
												$holakase = 'VIP Network';
											}
											
											echo $holakase;
											
											
											?></td>

											<td>$<?php echo $row['price']; ?></td>

											<td><?php echo $row['mbt']; ?>sec</td>

											<td><?php echo $row['length']; ?> <?php echo $row['unit']; ?></td>
										 </tr>

								</tbody>

							</table>

						</div>

						<div class="alert alert-success text-center">
							<h3><strong>Send $<?php echo $row['price']; ?> to <?php echo htmlspecialchars($ipnSecret); ?></strong> </h3>
							<p>When u pay create ticket with payment details</p>
						</div>
						
					</div>

				</div>

			</div>

		</div>     

	</div>

</main>

<?php
			}
		}
	
	}
	


	include 'footer.php';



?>