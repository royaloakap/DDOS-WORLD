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
								   <h6 class="alert alert-fill-primary">Available Methods</h6>
								     <table class="table table-striped">
 										<form method="post">
                                        <tr>
                                            <th>Name</th>
											<th>Tag</th>
											<th>Type</th>
											<th>Delete</th>
                                        </tr>
                                        <tr>
										<form method="post">


<?php

$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['id'];
 $name = $getInfo['name'];
 $fullname = $getInfo['fullname'];
 $type = $getInfo['type'];
 echo '<tr><td>'.htmlspecialchars($name).'</td><td>'.htmlspecialchars($fullname).'</td><td>'.$type.'</td><td><button type="submit" title="Delete API" name="deletem" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-icon"><i data-feather="delete"></i></button></td></tr>';
}
if (isset($_POST['deletem']))
{
$delete = $_POST['deletem'];
$SQL = $odb -> prepare("DELETE FROM `methods` WHERE `id` = :id");
$SQL -> execute(array(':id' => $delete));
echo success('Methods has been removed <meta http-equiv="refresh" content="3;url=methods.php">');
}
?>
</form>
                                        </tr>                                       
                                    </table>
									</div>
									  </div>
                                    								<div class="col-md-12 grid-margin stretch-card">	
                                   <div class="card">
								   <h6 class="alert alert-fill-primary">New Methods</h6>
                                            <div class="panel-body">
                                <div class="block-content controls">
								<form method="post">
								<?php

if (isset($_POST['add']))
{
if (empty($_POST['name']) || empty($_POST['fullname']) || empty($_POST['type']))
{
$error = 'Please verify all fields';
}

if (empty($error))
{
$name = $_POST['name'];
$fullname = $_POST['fullname'];
$type = $_POST['type'];
if ($system=='servers') {$command = $_POST['command'];} else {$command = '';}
$SQLinsert = $odb -> prepare("INSERT INTO `methods` VALUES(NULL, :name, :fullname, :type, :command)");
$SQLinsert -> execute(array(':name' => $name, ':fullname' => $fullname, ':type' => $type, ':command' => $command));
echo success('Method has been added <meta http-equiv="refresh" content="3;url=methods.php">');
}
else
{
echo error($error);
}
}
?>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="This will be used when executing the attack" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Tag Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control tip" title="This will be used on the Hub page" name="fullname"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Type:</strong></div>
                                        <div class="col-md-12">
                                            <select name="type" class="form-control">
									<option value="1">Low Protection - L4</option>
									<option value="2">High Protection - L4</option>
								    <option value="3">Premium Methods - L4</option>
									<option value="4">Low Protection - L7</option>
									<option value="5">High Protection - L7</option>
									<option value="6">Premium Methods - L7</option>
                                            </select>
                                        </div>
                                    </div>	
																		 <div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="add" class="btn btn-primary">Update</button>
                                </div> 
							  </div>					