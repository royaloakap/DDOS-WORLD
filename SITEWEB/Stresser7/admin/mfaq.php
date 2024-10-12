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
			  <?php 

if (isset($_POST['addfaq']))
{
if (empty($_POST['method']) || empty($_POST['description']))
{
$error = 'Please verify all fields';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `faq` VALUES(NULL, :method, :description)");
$SQLinsert -> execute(array(':method' => $_POST['method'], ':description' => $_POST['description']));
echo success('FAQ has been added <meta http-equiv="refresh" content="1;url=mfaq.php">');
}
else
{
echo error($error);
}
}?>		
        <div class="col-md-12 grid-margin stretch-card">
              <div class="card">                                   
                                    <div class="block-content tab-content">
                                    <table class="table table-bordered">
                                        <tr>
                    <th>Method</th>
					<th>Descriptions</th>
                    <th>Delete</th>
                  </tr>
                </thead>
								<form method="post">
                <tbody>
<?php
$SQLGetFAQ = $odb -> query("SELECT * FROM `faq`");
while($getInfo = $SQLGetFAQ -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['id'];
 $method = $getInfo['method'];
 $description = $getInfo['description'];
 echo '<tr><td>'.htmlspecialchars($method).'</td><td>'.htmlspecialchars($description).'</td><td><button type="submit" title="Delete FAQ" name="deletefaq" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-icon"><i data-feather="delete"></i></button></td></tr>';
}
if (isset($_POST['deletefaq']))
{
$delete = $_POST['deletefaq'];
$SQL = $odb -> query("DELETE FROM `faq` WHERE `id` = '$delete'");
echo success('FAQ has been removed <meta http-equiv="refresh" content="1;url=mfaq.php">');
}
?>
</tbody>
</form>                                   
                                    </table>
<p>
                        <div class="card text-white bg-primary">
                          <div class="card-header text-center">New FAQ</div>
	                    </div>
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Method:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="method"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Descriptions:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="description"/></div>
                                    </div>
									 <div class="card-body">
									<div  class="col-xs-4 text-center" >
                                     <button name="addfaq" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
									</form>
                                </div>
</p>
                                        </div>                    
                                    </div>
                                </div>