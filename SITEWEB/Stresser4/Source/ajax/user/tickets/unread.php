<?php

	ob_start(); 
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php'; 

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$userid = $_SESSION['ID'];

?>

					<div class="row">
                        <div class="col-sm-5 col-lg-3">
							<button class="btn btn-block btn-primary visible-xs push" data-toggle="collapse" data-target="#inbox-nav" type="button">Navigation</button>
                            <div class="collapse navbar-collapse remove-padding" id="inbox-nav">
                                <div class="block">
                                    <div class="block-header bg-primary">
                                        <ul class="block-options">
                                            <li>
                                                <button style="color: white;" data-toggle="modal" data-target="#ticket" type="button"><i class="fa fa-pencil"></i> New Message</button>
                                            </li>
                                        </ul>
                                        <h3 class="block-title">Inbox</h3>
                                    </div>
                                    <div class="block-content">
                                        <ul class="nav nav-pills nav-stacked push">
                                            <li>
                                                <a href="#" onclick="inbox()">
                                                    <i class="fa fa-fw fa-inbox push-5-r"></i>Inbox
                                                </a>
                                            </li>
                                            <li class="active">
                                                <a href="#" onclick="unread()">
                                                    <i class="fa fa-fw fa-star push-5-r"></i>Unread
												</a>
                                            </li>    
                                        </ul>
                                    </div>
                                </div>          
                            </div>
                        </div>
                        <div class="col-sm-7 col-lg-9">
                            <div class="block">
                                <div class="block-header bg-primary">
                                    <ul class="block-options">
                                        <li>
                                            <button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
                                        </li>
                                        <li>
                                            <button type="button" data-toggle="block-option" data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                                        </li>
                                    </ul>
									<div class="block-title text-normal">
                                        <strong>Messages</strong>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="pull-r-l">
                                        <table class="js-table-checkable table table-hover table-vcenter">
                                            <tbody>  
											<?php     
 
											$select = $odb->prepare("SELECT * FROM `tickets` WHERE `username` = :uname AND `status` = 'Waiting for user response' ORDER BY `id` DESC");
											$select->execute(array(':uname' => $_SESSION['username']));
											while($show = $select->fetch(PDO::FETCH_ASSOC)){
											 
											?>
                                                <tr href="ticket.php?id=<?php echo $show['id']; ?>" class="<?php if($show['status'] == "Waiting for user response") echo "active"; ?>">
													<td class="text-center" style="width: 70px;">
                                                        <label class="css-input css-checkbox css-checkbox-primary">
                                                            <input type="checkbox"><span></span>
                                                        </label> 
                                                    </td> 
													<td>
                                                        <a class="font-w600" href="ticket.php?id=<?php echo $show['id']; ?>"><?php echo $show['subject']; ?></a>
                                                        <div class="text-muted push-5-t"><?php echo substr(strip_tags($show['content']), 0, 20) . ".."; ?></div>
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