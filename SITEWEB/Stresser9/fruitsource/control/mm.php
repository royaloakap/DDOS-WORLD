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
$page = "Method Management";
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
				if (isset($_POST['methodAdd']))
				{
					$friendlyAdd = $_POST['friendly'];
					$nameAdd = $_POST['name'];
					if (empty($nameAdd) || empty($friendlyAdd))
					{
						echo '<div class="alert alert-danger"><strong>Error</strong>: please fill in all the field that are required!</div>';
					}
					else
					{
						$SQLinsert = $odb -> prepare("INSERT INTO `methods` VALUES(NULL, :friendly, :name)");
						$SQLinsert -> execute(array(':friendly' => $friendlyAdd, ':name' => $nameAdd));
						echo '<div class="alert alert-success"><strong>Success</strong>: your method has been added!</div>';
					}
				}
				if (isset($_POST['deleteBtn']))
					{
						$deletes = $_POST['deleteCheck'];
						foreach($deletes as $delete)
						{
							$SQL = $odb -> prepare("DELETE FROM `methods` WHERE `ID` = :id LIMIT 1");
							$SQL -> execute(array(':id' => $delete));
						}
						echo '<div class="g_12"><div class="alert alert-success><strong>Success</strong>: Method has been removed</div></div>';
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
				                        <input class="form-control" name="friendly" type="text" placeholder="friendly"/>
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="name" type="text" placeholder="name"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
							<button type="submit" name="methodAdd" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Add Method</button>
						</div>	
						</form>
			        </div>
				</div>
				<div class="col-lg-7">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-rss"></i><span class="break"></span>Method Manager</h2>
						</div>
						<div class="panel-body">
							<form method="POST">
							<table class="table table-striped">
								  <thead>
									<tr>
									<th>#</th>
									<th>Friendly Name</th>
									<th>Methods Name</th>
									<th></th>
								  </tr>
								</thead>
								<tbody>
								<?php
								  $SQLSelect = $odb -> query("SELECT * FROM `methods` ORDER BY `id` ASC");
								  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
								  {
									$friendlyShow = $show['friendly'];
									$nameShow = $show['name'];
									$rowID = $show['ID'];
									echo '<tr><td><input type="checkbox" class="simple_form" name="deleteCheck[]" value="'.$rowID.'"/></td><td>'.$friendlyShow.'</td><td>'.$nameShow.'</td><td><input type="submit" name="deleteBtn" value="Delete" class="btn btn-danger"/></td></tr>';
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