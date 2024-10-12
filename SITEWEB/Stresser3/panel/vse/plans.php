<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
		
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
		$updateapi = $_POST['api'.$_POST['update']];
		$updatevip = $_POST['vip'.$_POST['update']];
		
		if (empty($updatePrice) || empty($updateName) || empty($updateUnit) || empty($updateLength) || empty($updateMbt) || empty($updateconcurrents)){
			$notify = error('Failed to update due to missing values');
		}
		else {
			$SQLinsert = $odb -> prepare("UPDATE `plans` SET `name` = :name, `vip` = :vip, `mbt` = :mbt, `unit` = :unit, `length` = :length, `price` = :price, `concurrents` = :concurrents, `private` = :private, `api`=:api WHERE `ID` = :id");
			$SQLinsert -> execute(array(':name' => $updateName, ':vip' => $updatevip, ':mbt' => $updateMbt, ':unit' => $updateUnit, ':length' => $updateLength, ':price' => $updatePrice, ':concurrents' => $updateconcurrents, ':private' => $updateprivate, ':api' => $updateapi,':id' => $_POST['update']));
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
		$vip = $_POST['vip'];
		$api = $_POST['api'];
		
		/* if (empty($price) || empty($name) || empty($unit) || empty($length) || empty($mbt) || empty($concurrents)|| empty($vip))
		{
			$notify = error('Fill in all fields');
		} 
		else{ */
			$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :vip, :mbt, :unit, :length, :price, :concurrents, :private, :api)");
			$SQLinsert -> execute(array(':name' => $name, ':vip' => $vip, ':mbt' => $mbt, ':unit' => $unit, ':length' => $length, ':price' => $price, ':concurrents' => $concurrents, ':private' => $private, ':api' => $api));
			$notify = success('Plan has been added');
		//}
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


<title><?php echo $sitename; ?> | Plans Manage</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Plans</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Plans</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
		 <div class="container-fluid">
      <div class="row">
	
	     <div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
				
				<table class="table">
						<tr>
							<th style="font-size: 12px;">Name</th>
							<th class="text-center" style="font-size: 12px;">Max Boot</th>
							<th class="text-center" style="font-size: 12px;">Price</th>
							<th class="text-center" style="font-size: 12px;">Length</th>
							<th class="text-center" style="font-size: 12px;">Concurrents</th>
							<th class="text-center" style="font-size: 12px;">Private</th>
							<th class="text-center" style="font-size: 12px;">VIP</th>
							<th class="text-center" style="font-size: 12px;">Sales</th>
							<th class="text-center" style="font-size: 12px;">API</th>
							<th class="text-center" style="font-size: 12px;">Users</th>
						</tr>
						<tr>
							<form method="post">
							<?php
							$SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `ID` DESC");
							while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
							{
								$unit = $show['unit'];
								$length = $show['length'];
								$price = $show['price'];
								$concurrents = $show['concurrents'];
								$planName = $show['name'];
								$mbtShow = $show['mbt'];
								if ($show['vip'] == 0) { $vip = 'No'; } else { $vip = 'Yes'; }
								if ($show['api'] == 0) { $api = 'No'; } else { $api = 'Yes'; }
								
								$id = $show['ID'];
								if ($show['private'] == 0) { $private = 'No'; } else { $private = 'Yes'; }
								$sales = $odb->query("SELECT COUNT(*) FROM `payments` WHERE `plan` = '$id'")->fetchColumn(0);
								$people = $odb->query("SELECT COUNT(*) FROM `users` WHERE `membership` = '$id'")->fetchColumn(0);
								echo '<tr">
										<td style="font-size: 12px;"><a class="link-effect" href="#" data-toggle="modal" data-target="#modal-fadein'. $id .'" >'.htmlspecialchars($planName).'</a></td>
										<td class="text-center" style="font-size: 12px;">'.$mbtShow.'</td>
										<td class="text-center" style="font-size: 12px;">$'.htmlentities($price).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($length).' '.htmlentities($unit).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($concurrents).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($private).'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($vip).'</td>
										<td class="text-center" style="font-size: 12px;">'.$sales.'</td>
										<td class="text-center" style="font-size: 12px;">'.htmlentities($api).'</td>
										<td class="text-center" style="font-size: 12px;">'.$people.'</td>
									</tr>';
								?>
									<div class="modal fade" id="modal-fadein<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
									  <div class="modal-dialog" role="document">
										<div class="modal-content text-dark">
										  <div class="modal-header">
											<h4 class="modal-title" id="exampleModalLabel1">Edit Plan</h4>
										  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										  </div>
										  <div class="modal-body">
										  
											<form class="form-horizontal" method="post">
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="name2">Name</label>
																			<input class="form-control" type="text" id="name2" name="name<?php echo $id; ?>" value="<?php echo htmlspecialchars($planName); ?>">
																			
																		</div>
																	</div>
																</div> 
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="price2">Price</label>
																			<input class="form-control" type="text" id="price2" name="price<?php echo $id; ?>" value="<?php echo htmlspecialchars($price); ?>">
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="mbt2">Max Boot Time</label>
																			<input class="form-control" type="number" id="mbt2" name="mbt<?php echo $id; ?>" value="<?php echo htmlspecialchars($mbtShow); ?>">
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="concurrents2">Concurrents</label>
																			<input class="form-control" type="number" id="concurrents2" name="concurrents<?php echo $id; ?>" value="<?php echo htmlspecialchars($concurrents); ?>">
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-4">
																		<div class="form-material">
																		<label for="length2">Length</label>
																			<input class="form-control" type="number" id="length2" name="length<?php echo $id; ?>" value="<?php echo htmlspecialchars($length); ?>">
																			
																		</div>
																	</div>
																	<div class="col-sm-8">
																		<div class="form-material">
																		<label for="unit2">Unit</label>
																			<select class="form-control" id="unit2" name="unit<?php echo $id; ?>" size="1">
																				<option value="Days" <?php echo selectedUnit('Days',$unit); ?>>Days</option>
																				<option value="Weeks" <?php echo selectedUnit('Weeks',$unit); ?> >Weeks</option>
																				<option value="Months" <?php echo selectedUnit('Months',$unit); ?>>Months</option>
																				<option value="Years" <?php echo selectedUnit('Years',$unit); ?>>Years</option>
																			</select>
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="api2">API</label>
																			<select class="form-control" id="api2" name="api<?php echo $id; ?>" size="1">
																				<option value="1" <?php echo selectedUnit(1,$show['api']); ?>>Yes</option>
																				<option value="0" <?php echo selectedUnit(0,$show['api']); ?>>No</option>
																			</select>
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="api2">VIP</label>
																			<select class="form-control" id="vip" name="vip<?php echo $id; ?>" size="1">
																				<option value="1" <?php echo selectedUnit(1,$show['vip']); ?>>Yes</option>
																				<option value="0" <?php echo selectedUnit(0,$show['vip']); ?>>No</option>
																			</select>
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-12">
																		<div class="form-material">
																		<label for="private2">Private</label>
																			<select class="form-control" id="private2" name="private<?php echo $id; ?>" size="1">
																				<option value="1" <?php echo selectedUnit(1,$show['private']); ?>>Yes</option>
																				<option value="0" <?php echo selectedUnit(0,$show['private']); ?>>No</option>
																			</select>
																			
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-sm-9">
																		<button name="update" value="<?php echo $id; ?>" class="btn btn-sm btn-primary" type="submit">Update</button>
																		<button name="delete" value="<?php echo $id; ?>" class="btn btn-sm btn-danger" type="submit">Delete</button>
																	</div>
																</div>
															</form>
											</div>
										  
										  <div class="modal-footer">
											
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
		
		<div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add Plan</h3>
				
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="name">Name</label>
										<input class="form-control" type="text" id="name" name="name">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="price">Price</label>
										<input class="form-control" type="text" id="price" name="price">
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="mbt">Max Boot Time</label>
										<input class="form-control" type="number" id="mbt" name="mbt">
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="concurrents">Concurrents</label>
										<input class="form-control" type="number" id="concurrents" name="concurrents">
									
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-4">
									<div class="form-material">
									<label for="length">Length</label>
										<input class="form-control" type="number" id="length" name="length">
										
									</div>
								</div>
								<div class="col-sm-8">
									<div class="form-material">
									<label for="unit">Unit</label>
										<select class="form-control" id="unit" name="unit" size="1">
											<option value="Days">Days</option>
											<option value="Weeks">Weeks</option>
                                            <option value="Months">Months</option>
											<option value="Years">Years</option>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="private">Private</label>
										<select class="form-control" id="private" name="private" size="1">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="private">VIP</label>
										<select class="form-control" id="vip" name="vip" size="1">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="api">API</label>
										<select class="form-control" id="api" name="api" size="1">
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
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