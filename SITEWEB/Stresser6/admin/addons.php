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
			  <?php if (isset($_POST['newaddon']))
{
			$addon = $_POST['addon'];
			$price = floatval($_POST['price']);
			$errors = array();
			
			if (empty($price) || empty($addon))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `addons` VALUES(NULL, :addon, :price)");
				$SQLinsert -> execute(array(':addon' => $addon, ':price' => $price));
				echo success('Addon has been added');
			}
			else
			{
				echo error($error);
			}
}
?>
	  
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
			                                <div class="card text-white bg-primary">
                                <div class="card-header text-center">Anvalaible addons</div>
	                            </div>
                 <table class="table table-bordered">
                                        <tr>
                        <th>Name</th>
                        <th>Price</th>
						<th>Action</th>
                  </tr>
                </thead>
                <tbody>
				<form method="post">
<?php
												$SQLGetPlans = $odb -> query("SELECT * FROM `addons`");
												while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC))
												{
													$addon = $getInfo['addon'];
													$price = $getInfo['price'];
													$id = $getInfo['id'];
													
													echo '
													<tr>
														<td>'.htmlspecialchars($addon).'</td>
														<td>$'.$price.'</td>
														<td><button type="submit"  name="addondrop" value="'.htmlspecialchars($id).'" class="btn btn-danger btn-icon"><i data-feather="delete"></i></button></td>
													</tr>';
												}
												if (isset($_POST['addondrop']))
                                              {
                                              $delete = $_POST['addondrop'];
                                              $SQL = $odb -> prepare("DELETE FROM `addons` WHERE `id` = :id");
                                              $SQL -> execute(array(':id' => $delete));
                                              echo success('Addon has been removed <meta http-equiv="refresh" content="3;url=addons.php">');
                                               }
?>
</form>
   </table>
                                        </div>
</div>											
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
			                                <div class="card text-white bg-primary">
                                <div class="card-header text-center">Manager addons</div>
	                            </div>
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Addons:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="addon"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="price"/></div>
                                    </div>
									<div class="card-header">
									<div  class="col-xs-4 text-center" >
                                     <button name="newaddon" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
									</form>
                                </div>