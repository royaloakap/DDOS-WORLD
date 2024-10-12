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
$page = "Settings Management";
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
				if (isset($_POST['changeBtn']))
		                {
                		        $bootername = $_POST['bootername'];
								$booterlogo = $_POST['booterlogo'];
								$wmsg = $_POST['wmsg'];
								$booterurl = $_POST['booterurl'];
                        		$errors = array();
                        		if (empty($bootername) || empty($booterlogo) || empty ($wmsg))
                        		{
                        		        $errors[] = 'Please verify all fields';
                        		}
                        		if (empty($errors))
                        		{
                        		        $SQLinsert = $odb -> prepare("UPDATE `admin` SET `bootername` = :newbootername, `booterurl` = :url, `booterlogo` = :newbooterlogo, `wmsg` = :newwmsg WHERE `id` = 1");
                        		        $SQLinsert -> execute(array(":newbootername" => $bootername, ":url" => $booterurl, ":newbooterlogo" => $booterlogo, ":newwmsg" => $wmsg));
                        		        echo '<div class="alert alert-success"><strong>Success</strong>: site configurations have been updated</div>';
                        		}
                        		else
                        		{
                        		        echo '<div class="alert alert-danger"><strong>Error</strong>: please fill in required information!</div>';
                        		}
                		}
				?>
					<div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Site Settings</strong></h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="POST" class="form-horizontal">
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="bootername" value="<?php echo $odb->query("SELECT `bootername` FROM `admin` LIMIT 1")->fetchColumn(0); ?>" type="text">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                        <input class="form-control" name="booterurl" value="<?php echo $odb->query("SELECT `booterurl` FROM `admin` LIMIT 1")->fetchColumn(0); ?>" type="text">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                         <input class="form-control" name="wmsg" value="<?php echo $odb->query("SELECT `wmsg` FROM `admin` LIMIT 1")->fetchColumn(0); ?>" type="text">
				                    </div>
				                </div>
								<div class="form-group">
				                    <div class="col-sm-12">
				                         <input class="form-control" name="booterlogo" value="<?php echo $odb->query("SELECT `booterlogo` FROM `admin` LIMIT 1")->fetchColumn(0); ?>" type="text">
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
							<button type="submit" name="changeBtn" class="btn btn-sm btn-success"><i class="fa fa-flash"></i> Change</button>
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