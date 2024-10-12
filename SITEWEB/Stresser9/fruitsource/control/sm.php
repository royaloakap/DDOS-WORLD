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
$page = "Server Management";
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
			if (isset($_POST['apiAdd']))
				{
					$apiAdd = $_POST['api'];
					if (empty($apiAdd))
					{
						echo '<div class="alert alert-danger"><strong>Error</strong>: please fill in all the field that are required!</div>';
					}
					else
					{
						$SQLinsert = $odb -> prepare("INSERT INTO `api` VALUES(NULL, :api)");
						$SQLinsert -> execute(array(':api' => $apiAdd));
						echo '<div class="alert alert-success"><strong>Success</strong>: api has been added!</div>';
					}
				}
				?>
				<?php
				if (isset($_POST['deleteBtn']))
					{
						$deletes = $_POST['deleteCheck'];
						foreach($deletes as $delete)
						{
							$SQL = $odb -> prepare("DELETE FROM `api` WHERE `ID` = :id LIMIT 1");
							$SQL -> execute(array(':id' => $delete));
						}
						echo '<div class="g_12"><div class="alert alert-success><strong>Success</strong>: Server has been removed</div></div>';
					}
				?>
					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Add Server</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="api" placeholder="<?php
											$getNames = $odb -> query("SELECT * FROM `admin`");
											while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
												echo $Names['booterurl'];
											}
										?>/send.php?key=[key]&target=[host]&port=[port]&time=[time]&method=[method]" type="text"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
							<button type="submit" name="apiAdd" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Add Api</button>
						</div>	
						</form>
			        </div>
				</div>
				<div class="col-lg-7">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-rss"></i><span class="break"></span>Api Manager</h2>
						</div>
						<div class="panel-body">
							<form action="" method="POST">
							<input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger"/>
							<table class="table table-striped">
								  <thead>
									<tr>
									<th>#</th>
									<th>Api</th>
								  </tr>
								</thead>
								<tbody>
								<?php
									  $SQLSelect = $odb -> query("SELECT * FROM `api` ORDER BY `id` ASC");
									  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
									  {
										$apiShow = $show['api'];
										$rowID = $show['ID'];
										echo '<tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td>'.$apiShow.'</td></tr>';
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