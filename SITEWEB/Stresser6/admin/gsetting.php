<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>

<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />								
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content">
			  <?php include("fuctions/init.php"); ?>		  
         <div class="row">			  
			<div class="col-md-6 grid-margin stretch-card">
              <div class="card">
                              <div class="card text-white bg-primary">
                                <div class="card-header text-center">General Settings</div>
	                            </div>
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="sitename" value="<?php echo htmlspecialchars($sitename); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site Description:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($description); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>ToS URL:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" value="<?php echo htmlspecialchars($tos); ?>" name="tos"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site URL:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="url" value="<?php echo htmlspecialchars($siteurl); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Maintaince Message:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" name="maintaince" title="Leave empty for maintaince mode off" value="<?php echo htmlspecialchars($maintaince); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Cloudflare Mode:</strong></div>
                                        <div class="col-md-12"><input  type="checkbox" name="cloudflare"  <?php if ($cloudflare == 1) { echo 'checked'; } ?>/></div>
                                    </div>
									<div class="card-header">
									<div  class="col-xs-4 text-center" >
                                     <button name="update" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>								                         
                            </div>
                          </div>
			<div class="col-md-6  grid-margin stretch-card">
              <div class="card">
                              <div class="card text-white bg-primary">
                                <div class="card-header text-center">Payments Settings</div>
	                            </div>
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Ethereum address:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="ethereum" value="<?php echo htmlspecialchars($ethereum); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Bitcoin address:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="bitcoin" value="<?php echo htmlspecialchars($bitcoin); ?>"/></div>
                                    </div>
									<div class="row-form">
                                        <div class="col-md-4"><strong>Litecoin address:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="litecoin" value="<?php echo htmlspecialchars($litecoin); ?>"/></div>
                                    </div>
									<div class="row-form">
                                        <div class="col-md-4"><strong>WebMoney address:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="webmoney" value="<?php echo htmlspecialchars($weboney); ?>"/></div>
                                    </div>
									<div class="row-form">
                                        <div class="col-md-4"><strong>Qiwi address:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="qiwi" value="<?php echo htmlspecialchars($qiwi); ?>"/></div>
                                    </div>
									<div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="update" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>								                         
                            </div>
                          </div>
						  <div class="col-md-6  grid-margin stretch-card">
                            <div class="card">
                              <div class="card text-white bg-primary">
                                <div class="card-header text-center">Hub Settings</div>
	                            </div>
                                <div class="block-content controls">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Attack Slots:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="insert 0 to disable" name="maxattacks" value="<?php echo htmlspecialchars($maxattacks); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Attack System:</strong></div>
                                        <div class="col-md-12">
                                            <select name="system" class="form-control">
                                                <option value="api" <?php if ($system == 'api') { echo 'selected'; } ?>>API</option>
                                                <option value="servers" <?php if ($system == 'servers') { echo 'selected'; } ?>>Servers</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Rotation:</strong></div>
                                        <div class="col-md-12"><input type="checkbox" name="rotation" <?php if ($rotation == 1) { echo 'checked'; } ?> /></div>
                                    </div>
								  <div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="update" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
                                </div>                               
                                  </div>
							       </div>								   
                                     </div>
                                       </div>															    
                                         </body>

</html>