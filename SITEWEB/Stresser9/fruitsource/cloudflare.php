<?php
ob_start();
include 'controls/database.php';
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}
if (!($user->hasMembership($odb)))
{
	header('location: purchase.php');
	die();
}
function get_host($ip){
        $ptr= implode(".",array_reverse(explode(".",$ip))).".in-addr.arpa";
        $host = dns_get_record($ptr,DNS_PTR);
        if ($host == null) return $ip;
        else return $host[0]['target'];
} 
function isCloudflare($ip)
{
	$host = get_host($ip);
	if($host=="cf-".implode("-", explode(".", $ip)).".cloudflare.com")
	{
		return true;
	} else {
		return false;
	}
}
$page = "Cloudflare Resolver";
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
			<div class="col-md-6">
				<?php
				$resolved = '';
				$output = array();
				if (isset($_POST['resolveBtn']))
				{
					$resolved = $_POST['toResolve'];
					$lookupArr = array("mail.", "direct.", "direct-connect.", "direct-connect-mail.", "cpanel.", "ftp.");
					foreach ($lookupArr as $lookupKey)
					{
						$lookupHost = $lookupKey . $resolved;
						$foundHost = gethostbyname($lookupHost);
						
						if ($foundHost == $lookupHost)
						{
							$output[] = "No DNS Found";
						}
						else
						{
							$extra = "<font color=\"green\">(Not Cloudflare)</font>";
							if(isCloudflare($foundHost))
							{
								$extra = "<font color=\"red\">(Cloudflare)</font>";
							}
							$output[] = $foundHost." ".$extra;
						}
					}

				}
				?>
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Cloudflare</strong> Resolver</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Host</label>
				                    <div class="col-md-9">
				                     	<input class="form-control" name="toResolve" type="text" placeholder="http://google.com"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
		                    <button type="submit" name="resolveBtn" class="btn btn-sm btn-success"><i class="fa fa-cloud"></i> Resolve</button>
		                </div>
						</form>
			        </div>
					</div>
					<div class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2><i class="fa fa-align-justify"></i><span class="break"></span>Resolver Results</h2>
						</div>
						<div class="panel-body">
							<table class="table table-bordered">
								  <thead>
									  <tr>
									<td>Domain:</td>
									<td><?php echo $resolved;?></td>
								</tr>
								<tr>
									<td>Mail:</td>
									<td><?php echo $output[0];?></td>
								</tr>
								<tr>
									<td>Direct:</td>
									<td><?php echo $output[1];?></td>
								</tr>
								<tr>
									<td>Direct-Connect:</td>
									<td><?php echo $output[2];?></td>
								</tr>
								<tr>
									<td>Direct-Connect-Mail:</td>
									<td><?php echo $output[3];?></td>
								</tr>

								<tr>
									<td>CPanel:</td>
									<td><?php echo $output[4];?></td>
								</tr><tr>
									<td>FTP:</td>
									<td><?php echo $output[5];?></td>
								</tr>
								  </tbody>
							 </table>  
						</div>
					</div>
				</div>
			</div><!--/row-->
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "footer.php"; ?>
	<?php include "security/project-security.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/jquery.sparkline.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="assets/js/jquery.flot.min.js"></script>
	<script src="assets/js/jquery.flot.pie.min.js"></script>
	<script src="assets/js/jquery.flot.stack.min.js"></script>
	<script src="assets/js/jquery.flot.resize.min.js"></script>
	<script src="assets/js/jquery.flot.time.min.js"></script>
	<script src="assets/js/jquery.flot.spline.min.js"></script>
	<script src="assets/js/jquery.autosize.min.js"></script>
	<script src="assets/js/jquery.placeholder.min.js"></script>
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/daterangepicker.min.js"></script>
	<script src="assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap.min.js"></script>
	<script src="assets/js/raphael.min.js"></script>
	<script src="assets/js/morris.min.js"></script>
	<script src="assets/js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="assets/js/uncompressed/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/js/uncompressed/gdp-data.js"></script>
	<script src="assets/js/gauge.min.js"></script>
	
	<!-- theme scripts -->
	<script src="assets/js/custom.min.js"></script>
	<script src="assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->
	
</body>
</html>