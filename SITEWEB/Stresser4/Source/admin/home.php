<?php

	include 'header.php';

	$nowDate = time();
	$dayDate = strtotime('-1 day', $nowDate);
	$weekDate = strtotime('-7 day', $nowDate);
	$day = 0; $week = 0; $total = 0;
	$dayQuery = $odb->query("SELECT * FROM `payments` WHERE `date` BETWEEN '{$dayDate}' AND '{$nowDate}'");
	while($show = $dayQuery->fetch(PDO::FETCH_BOTH)){
		$day = $day + $show['paid'];
	}
	$weekQuery = $odb->query("SELECT `paid` FROM `payments` WHERE `date` BETWEEN '{$weekDate}' AND '{$nowDate}'");
	while($show = $weekQuery->fetch(PDO::FETCH_BOTH)){
		$week = $week + $show['paid'];
	}
	$totalQuery = $odb->query("SELECT `paid` FROM `payments`");
	while($show = $totalQuery->fetch(PDO::FETCH_BOTH)){
		$total = $total + $show['paid'];
	}
	
?>
			<main id="main-container" style="min-height: 404px;">
				<div class="content bg-image overflow-hidden" style="background-image: url('../assets/img/photos/photo3@2x.jpg');">
                    <div class="push-50-t push-15">
                        <h1 class="h2 text-white animated zoomIn">Dashboard</h1>
                        <h2 class="h5 text-white-op animated zoomIn">Administrator Settings</h2>
                    </div>
                </div>
				<div class="content">
					<div class="col-md-5">
						<div class="block">
							<div class="block-header bg-primary">
								<h3 class="block-title">Latest Sales</h3>
							</div>
							<div class="block-content bg-gray-lighter">
								<div class="row items-push">
									<div class="col-xs-4">
										<div class="text-muted"><small><i class="si si-calendar"></i> 24 hrs</small></div>
										<div class="font-w600">$<?php echo $day; ?> Total</div>
									</div>
									<div class="col-xs-4">
										<div class="text-muted"><small><i class="si si-calendar"></i> 7 days</small></div>
										<div class="font-w600">$<?php echo $week; ?> Total</div>
									</div>
									<div class="col-xs-4 h1 font-w300 text-right">$<?php echo $total; ?></div>
								</div>
							</div>
							<div class="block-content">
								<div class="pull-t pull-r-l">  
									<div class="js-slider remove-margin-b" data-slider-autoplay="false" data-slider-autoplay-speed="2500">
										<div>
											<table class="table remove-margin-b font-s13">
												<tbody>
													<?php
													$salesQuery = $odb->query("SELECT * FROM `payments` ORDER BY `id` DESC LIMIT 6");
													while($sales = $salesQuery->fetch(PDO::FETCH_BOTH)){
														$checkPlan = $odb->query("SELECT `name` FROM `plans` WHERE `ID` = '{$sales['plan']}'")->fetchColumn(0);
														echo'
														<tr>
															<td class="font-w600">
																<a href="#">'. $checkPlan .'</a>
															</td>
															<td class="hidden-xs text-muted text-right" style="width: 140px;">'. date('d-m h:i:s a', $sales['date']) .'</td>
															<td class="font-w600 text-success text-right" style="width: 70px;">+ $'. $sales['paid'] .'</td>
														</tr>';
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-12 col-lg-12">
								<div class="block">
									<div class="block-header bg-primary">
										<h3 class="block-title">Messages [ No Reply ]</h3>
									</div>
									<div class="block-content">
										<div class="pull-r-l">
											<table class="js-table-checkable table table-hover table-vcenter">
												<tbody>
												<?php

												$select = $odb->query("SELECT * FROM `tickets` WHERE `status` = 'Waiting for admin response' ORDER BY `id` DESC");
												while($show = $select->fetch(PDO::FETCH_ASSOC)){
												
												?>
													<tr class="<?php if($show['status'] == "Waiting for admin response") echo "active"; ?>">
														<td class="text-center" style="width: 70px;">
															<label class="css-input css-checkbox css-checkbox-primary">
																<input type="checkbox"><span></span>
															</label>
														</td>
														<td>
															<a class="font-w600" href="ticket.php?id=<?php echo $show['id']; ?>"><?php echo $show['subject']; ?></a>
															<div class="text-muted push-5-t"><?php echo htmlentities(substr(strip_tags($show['content']), 0, 20)) . ".."; ?></div>
														</td>
														<td class="text-muted"><?php echo $show['status']; ?></td>
														<td class="visible-lg text-muted" style="width: 120px;">
															<em><?php echo date('m-d-Y', $show['date']); ?></em>
														</td>
													</tr>
												<?php
												
												}
												
												?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            </main>
<?php

	include 'footer.php';

?>