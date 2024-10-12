<?php

	ob_start(); 
	require_once '../../../app/config.php';
	require_once '../../../app/init.php'; 

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$userid = $_SESSION['ID'];

?>

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
