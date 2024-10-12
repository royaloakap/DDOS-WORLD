<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	if(isset($_POST['delete'])){
		$deleteSQL = $odb->prepare("DELETE FROM `plans` WHERE `ID` = :id");
		$deleteSQL->execute(array(':id' => $_POST['delete']));
		$notify = success('Plan deleted');
	}
	
	if (isset($_POST['update'])){
		$updateName = $_POST['name'.$_POST['update']];
		$updateUnit = $_POST['unit'.$_POST['update']];
		$updateLength = $_POST['length'.$_POST['update']];
		$updateMbt = intval($_POST['mbt'.$_POST['update']]);
		$updatePrice = floatval($_POST['price'.$_POST['update']]);
		$updateconcurrents = $_POST['concurrents'.$_POST['update']];
		$updateprivate = $_POST['private'.$_POST['update']];
		
		if (empty($updatePrice) || empty($updateName) || empty($updateUnit) || empty($updateLength) || empty($updateMbt) || empty($updateconcurrents)){
			$notify = error('Failed to update due to missing values');
		}
		else {
			$SQLinsert = $odb -> prepare("UPDATE `plans` SET `name` = :name, `mbt` = :mbt, `unit` = :unit, `length` = :length, `price` = :price, `concurrents` = :concurrents, `private` = :private WHERE `ID` = :id");
			$SQLinsert -> execute(array(':name' => $updateName, ':mbt' => $updateMbt, ':unit' => $updateUnit, ':length' => $updateLength, ':price' => $updatePrice, ':concurrents' => $updateconcurrents, ':private' => $updateprivate, ':id' => $_POST['update']));
			$notify = success('Plan has been updated');
		}
	}
	
	if (isset($_POST['addplan'])){
		
		$name = $_POST['name'];
		$unit = $_POST['unit'];
		$length = $_POST['length'];
		$mbt = intval($_POST['mbt']);
		$price = floatval($_POST['price']);
		$concurrents = $_POST['concurrents'];
		$private = $_POST['private'];
		
		if (empty($price) || empty($name) || empty($unit) || empty($length) || empty($mbt) || empty($concurrents))
		{
			$notify = error('Fill in all fields');
		}
		else{
			$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :mbt, :unit, :length, :price, :concurrents, :private)");
			$SQLinsert -> execute(array(':name' => $name, ':mbt' => $mbt, ':unit' => $unit, ':length' => $length, ':price' => $price, ':concurrents' => $concurrents, ':private' => $private));
			$notify = success('Plan has been added');
		}
	}
	
	$SQLGetInfo = $odb -> prepare("SELECT * FROM `plans` WHERE `ID` = :id LIMIT 1");
	$SQLGetInfo -> execute(array(':id' => $_GET['id']));
	$planInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
	$currentName = $planInfo['name'];
	$currentMbt = $planInfo['mbt'];
	$currentUnit = $planInfo['unit'];
	$currentPrice = $planInfo['price'];
	$currentLength = $planInfo['length'];
	$currentconcurrents = $planInfo['concurrents'];
	$currentprivate = $planInfo['private'];
	
	function selectedUnit($check, $currentUnit){
		if ($currentUnit == $check){
			return 'selected="selected"';
		}
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Settings <small>Manage Plans</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Settings</li>
					<li><a class="link-effect" href="plans.php">Plans</a></li>
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
			<div class="col-md-8">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Manage Plans</h3>
					</div>
					<table class="table">
						<tr>
							<th style="font-size: 12px;">Name</th>
							<th class="text-center" style="font-size: 12px;">Max Boot</th>
							<th class="text-center" style="font-size: 12px;">Price</th>
							<th class="text-center" style="font-size: 12px;">Length</th>
							<th class="text-center" style="font-size: 12px;">Concurrents</th>
							<th class="text-center" style="font-size: 12px;">Type</th>
							<th class="text-center" style="font-size: 12px;">Sales</th>
							<th class="text-center" style="font-size: 12px;">Users</th>
						</tr>
						<tr>
							<form method="post">
							<?php
							$SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `length` ASC");
							while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
							{
								$unit = $show['unit'];
								$length = $show['length'];
								$price = $show['price'];
								$concurrents = $show['concurrents'];
								$planName = $show['name'];
								$mbtShow = $show['mbt'];
								$id = $show['ID'];
								if ($show['private'] == 0) { $private = 'Normal'; }								if ($show['private'] == 1) { $private = 'Private'; }								if ($show['private'] == 2) { $private = 'VIP'; }
								$sales = $odb->query("SELECT COUNT(*) FROM `payments` WHERE `plan` = '$id'")->fetchColumn(0);
								$people = $odb->query("SELECT COUNT(*) FROM `users` WHERE `membership` = '$id'")->fetchColumn(0);
								echo '<tr">
										<td style="font-size: 12px;"><a class="link-effect" href="#" data-toggle="modal" data-target="#modal-fadein'. $id .'" >'.htmlspecialchars($planName).'</a></td>
										<td class="text-center" style="font-size: 12px;">'.$mbtShow.'</td>
										<td class="text-center" style="font-size: 12px;">$'.htmlentities($price).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($length).' '.htmlentities($unit).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($concurrents).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($private).'</td>
										<td class="text-center" style="font-size: 12px;">'.$sales.'</td>
										<td class="text-center" style="font-size: 12px;">'.$people.'</td>
									</tr>';
								?>
									<div class="modal" id="modal-fadein<?php echo $id; ?>" tabindex="-1" role="dialog" aria-hidden="false" style="display: non;">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="block block-themed block-transparent remove-margin-b">
													<div class="block-header bg-primary-dark">
														<ul class="block-options">
															<li>
																<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
															</li>
														</ul>
														<h3 class="block-title">Edit Plan: <?php echo htmlspecialchars($planName); ?></h3>
													</div>
													<div class="content">
														<div class="row block-content block block-content">
															<form class="form-horizontal push-10-t" method="post">
																<div class="form-group row">
																	<div class="col-sm-12">
																		<div class="form-material">
																			<input class="form-control" type="text" id="name2" name="name<?php echo $id; ?>" value="<?php echo htmlspecialchars($planName); ?>">
																			<label for="name2">Name</label>
																		</div>
																	</div>
																</div> 
																<div class="form-group row">
																	<div class="col-sm-12">
																		<div class="form-material">
																			<input class="form-control" type="text" id="price2" name="price<?php echo $id; ?>" value="<?php echo htmlspecialchars($price); ?>">
																			<label for="price2">Price</label>
																		</div>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-sm-12">
																		<div class="form-material">
																			<input class="form-control" type="number" id="mbt2" name="mbt<?php echo $id; ?>" value="<?php echo htmlspecialchars($mbtShow); ?>">
																			<label for="mbt2">Max Boot Time</label>
																		</div>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-sm-12">
																		<div class="form-material">
																			<input class="form-control" type="number" id="concurrents2" name="concurrents<?php echo $id; ?>" value="<?php echo htmlspecialchars($concurrents); ?>">
																			<label for="concurrents2">Concurrents</label>
																		</div>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-sm-4">
																		<div class="form-material">
																			<input class="form-control" type="number" id="length2" name="length<?php echo $id; ?>" value="<?php echo htmlspecialchars($length); ?>">
																			<label for="length2">Length</label>
																		</div>
																	</div>
																	<div class="col-sm-8">
																		<div class="form-material">
																			<select class="form-control" id="unit2" name="unit<?php echo $id; ?>" size="1">
																				<option value="Days" <?php echo selectedUnit('Days',$unit); ?>>Days</option>
																				<option value="Weeks" <?php echo selectedUnit('Weeks',$unit); ?> >Weeks</option>
																				<option value="Months" <?php echo selectedUnit('Months',$unit); ?>>Months</option>
																				<option value="Years" <?php echo selectedUnit('Years',$unit); ?>>Years</option>
																			</select>
																			<label for="unit2">Unit</label>
																		</div>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-sm-12">
																		<div class="form-material">
																			<select class="form-control" id="private2" name="private<?php echo $id; ?>" size="1">																																							<option value="0" <?php echo selectedUnit(0,$show['private']); ?>>Normal</option>																																								<option value="2" <?php echo selectedUnit(2,$show['private']); ?>>VIP</option>																				
																				<option value="1" <?php echo selectedUnit(1,$show['private']); ?>>Private</option>												
																			</select>
																			<label for="private2">Type</label>
																		</div>
																	</div>
																</div>
																<div class="form-group row">
																	<div class="col-sm-9">
																		<button name="update" value="<?php echo $id; ?>" class="btn btn-sm btn-primary" type="submit">Update</button>
																		<button name="delete" value="<?php echo $id; ?>" class="btn btn-sm btn-danger" type="submit">Delete</button>
																	</div>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
							<?php
							} 
							?>
							</form>
						</tr>                                       
					</table>
				</div>
			</div>
			<div class="col-md-4">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Add New Plan</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="name" name="name">
										<label for="name">Name</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="price" name="price">
										<label for="price">Price</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="number" id="mbt" name="mbt">
										<label for="mbt">Max Boot Time</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="number" id="concurrents" name="concurrents">
										<label for="concurrents">Concurrents</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<div class="form-material">
										<input class="form-control" type="number" id="length" name="length">
										<label for="length">Length</label>
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-material">
										<select class="form-control" id="unit" name="unit" size="1">
											<option value="Days">Days</option>
											<option value="Weeks">Weeks</option>
                                            <option value="Months">Months</option>
											<option value="Years">Years</option>
										</select>
										<label for="unit">Unit</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" id="private" name="private" size="1">
											<option value="0">Normal</option>																															<option value="2">VIP</option>																															<option value="1">Private</option>			
										</select>
										<label for="private">Type</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addplan" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
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