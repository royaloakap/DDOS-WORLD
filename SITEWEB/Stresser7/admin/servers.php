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

<meta http-equiv="content-type" content="text/html;charset=UTF-12" />								
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content"> 							
								<div class="col-md-12 grid-margin stretch-card">	
                                   <div class="card">
								   <h6 class="alert alert-fill-primary">New server</h6>
								<form method="post">
										<?php 
				if (isset($_POST['okBtn']))
				{
					$api = $_POST['api'];
					$name = $_POST['name'];
					$slots = $_POST['slots'];
$methods = implode(" ",$_POST['methods']);
if (!(is_numeric($slots)))
{
$error = 'Slots field has to be numeric';
}
	if (empty($_POST['api']) || empty($_POST['name']) || empty($_POST['slots']) || empty($_POST['methods']))
{
$error = 'Please verify all fields';
}
						else
$SQLinsert = $odb -> prepare("INSERT INTO `api` VALUES(NULL, :name, :api, :slots, :methods)");
$SQLinsert -> execute(array(':api' => $api, ':name' => $name, ':slots' => $slots, ':methods' => $methods));
echo success('API has been added <meta http-equiv="refresh" content="3;url=servers.php">');
}
else
{
error($error);
}

								
				?>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>API Link:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="Syntax: [host], [port], [time], [method]" name="api"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Slots:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="slots"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Allowed Methods:</strong></div>
                                        <div class="col-md-12">
                                            <select class="form-control" name="methods[]" multiple="multiple">
<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $name = $getInfo['name'];
 echo '<option value="'.$name.'">'.$name.'</option>';
 }
?>
                                            </select>
										</div>
                                    </div>
									 <div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="okBtn" onclick='window.location.reload()' class="btn btn-primary">Update</button>
                                </div> 
							  </div>
							  </div>
							  </div>
	   <div style="position: relative; width: auto" class="col-md-12 grid-margin stretch-card">
        <div class="card">                           	  
        <div class="table-responsive">
          <table class="table table-hover">
                    <th>Name</th>
                    <th>API</th>
                    <th>Slots</th>
                    <th>Methods</th>
                    <th>Delete</th>

										<form method="post">

<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `api`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['id'];
 $api = $getInfo['api'];
 $name = $getInfo['name'];
 $slots = $getInfo['slots'];
 $methods = $getInfo['methods'];
 
 echo '<tr><td>'.htmlspecialchars($name).'</td><td>Encrypted</td><td>'.htmlspecialchars($slots).'</td><td class="row">'.htmlspecialchars($methods).'</td><td><button type="submit" title="Delete API" name="apidrop" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-icon"><i data-feather="delete"></i></button></td></tr>';
}
if (isset($_POST['apidrop']))
{
$delete = $_POST['apidrop'];
$SQL = $odb -> prepare("DELETE FROM `api` WHERE `id` = :id");
$SQL -> execute(array(':id' => $delete));
echo success('API has been removed <meta http-equiv="refresh" content="3;url=servers.php">');
}
?>
</form>								
                                    </table>
									</div>
								</div>
								</div>
               