<?php
ob_start();
include '../controls/database.php';
if (!($user -> LoggedIn()))
{
	header('location: ../login.php');
	die();
}
if (!($user -> isAdmin($odb)))
{
	die('You are not admin');
}
if (!($user -> notBanned($odb)))
{
	header('location: ../login.php');
	die();
}
$page = "Plans Management";
include ("head.php");
?>
<body>
		<?php include "header.php"; ?>
	
		<div class="container-fluid content">
		<div class="row">
				
			<?php include "side.php"; ?>
			<!-- end: Main Menu -->
						
			<!-- start: Content -->
			<div class="col-md-10 col-sm-11 main ">
			
			
			
			<div class="row">
			<div class="col-lg-5">
					<?php
						if (isset($_POST['addBtn']))
						{
							$nameAdd = $_POST['nameAdd'];
							$descriptionAdd = $_POST['descriptionAdd'];
							$unitAdd = $_POST['unit'];
							$lengthAdd = $_POST['lengthAdd'];
							$mbtAdd = intval($_POST['mbt']);
							$conAdd = $_POST['con'];
							$priceAdd = floatval($_POST['price']);
							
							if (empty($priceAdd) || empty($nameAdd) || empty($descriptionAdd) || empty($unitAdd) || empty($lengthAdd) || empty($mbtAdd) || empty($conAdd))
							{
								echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Please fill in all fields</div></div>';
							}
							else
							{
								$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :description, :mbt, :unit, :length, :price, :con)");
								$SQLinsert -> execute(array(':name' => $nameAdd, ':description' => $descriptionAdd, ':mbt' => $mbtAdd, ':unit' => $unitAdd, ':length' => $lengthAdd, ':price' => $priceAdd, ':con' => $conAdd));
								echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: Plan has been created</div></div>';
							}
						}
					?>
					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Add Plan</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="nameAdd" placeholder="Name" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="descriptionAdd" placeholder="Description" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="mbt" placeholder="Max Boot Time" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <select class="form-control" name="unit">
											<option value="Days">Days</option>
											<option value="Weeks">Weeks</option>
											<option value="Months">Months</option>
											<option value="Years">Years</option>
										</select>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="lengthAdd" placeholder="Length" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="price" placeholder="Price" type="text"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="con" placeholder="Concurrents" type="text"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
							<button type="submit" name="addBtn" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Create</button>
						</div>	
						</form>
			        </div>
				</div>
				<div class="col-lg-7">
				<?php
				if (isset($_POST['deleteBtn']))
					{
						$deletes = $_POST['deleteCheck'];
						foreach($deletes as $delete)
						{
							$SQL = $odb -> prepare("DELETE FROM `plans` WHERE `ID` = :id LIMIT 1");
							$SQL -> execute(array(':id' => $delete));
						}
						echo '<div class="g_12"><div class="alert alert-success><strong>Success</strong>: Plan(s) have been removed</div></div>';
					}
				?>
					<form method="POST">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-rss"></i><span class="break"></span>Plans Manager</h2>
						</div>
						<div class="panel-body">
							<input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger" />
							<table class="table table-striped">
								  <thead>
									<tr>
										<th>
											<input type="checkbox" class="simple_form tMainC" />
										</th>
											<th>Name</th>
											<th>Max Boot Time</th>
											<th>Price</th>
											<th>Concurrents</th>
									</tr>
								</thead>
								<tbody>
								<?php
									  $SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
									  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
									  {
										$planName = $show['name'];
										//$noteShow = $show['description'];
										$mbtShow = $show['mbt'];
										$priceShow = $show['price'];
										$conShow = $show['con'];
										$rowID = $show['ID'];
										echo '<tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td>'.htmlentities($planName).'</td><td>'.$mbtShow.'</td><td>'.$priceShow.'<td>'.$conShow.'</td></tr>';
									  }
							    ?>
								</tbody>
							 </table>  
						</div>
					</form>
				</div>
			</div>
					

				
			</div><!--/row-->
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "../footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->
	
</body>
</html>