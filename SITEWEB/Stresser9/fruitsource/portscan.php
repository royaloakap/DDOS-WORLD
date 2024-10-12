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
$page = "Port Scan";
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
			<div class="<?php echo ($success === false ? "col-md-6 col-md-offset-3" : "col-md-6"); ?>">
			<?php
                        // Main Script begins here
                        error_reporting(~E_ALL);
 
        //ip port range and ip
                        $host=$_POST['ip'];
                        $from = $_POST['from']; //48 connections supported for now, change php.ini default_socket_timeout for more
                        $to = $_POST['to'];
                        //validation
                        if (empty($_POST["ip"]) || empty($_POST['from']) || empty($_POST['to']))
                        {
                         echo"<b> Incomplete data, Go back! </b>";
                        }
                        elseif (!(filter_var($host, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)))
                        {
                          echo "<b>This IP address is not valid ! </b>";
                        }
                        elseif (!(is_numeric($from)) || !(is_numeric($to)))
                        {
                            echo "<b>Entered data is not a Port numeber</b>";
                        }
                        elseif ($from > $to || $from==$to)
                        {
                            echo "<b>Please enter lower value in the <i>FROM</i> field !</b>";
                        }
                        else
                        {
                            echo "<br><b><u>Scanned IP/Host : $host </u><br><u><i>List of Open Ports:</i></u></b><br>";
 
            //Creating Socket
                            $socket = socket_create(AF_INET , SOCK_STREAM , SOL_TCP);
                            for($port = $from; $port <= $to ; $port++)
                            {
                                //connect to the host and port
                                $connection = socket_connect($socket , $host ,  $port);
                                if($connection)
                                {
                                    //display port open warning on connect
                                    echo "port $port Open (Warning !) <img src='warning.png' height=30px width=30px alt='open port'> ".'<br>';
                                    //close the socket connection
                                    socket_close($socket);
                                    //Create a new since earlier socket was closed , we need to close and recreate only when a connection is made
                                    //otherwise we can use the same socket
                                    $socket = socket_create(AF_INET , SOCK_STREAM , SOL_TCP);
                                }
                                else
                                {
                                }
                            }
							if (!empty($result)) {
								echo "<div class='alert alert-" . ($success ? "success" : "danger") . "'>" . $result . "</div>";
							}
                        }
 
        ?>
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Port</strong> Scan</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Host</label>
				                    <div class="col-md-9">
				                       <input name="address" class="form-control" type="text" placeholder="Address or domain"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
		                    <button type="submit" name="runScan" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Run Port Scan</button>
		                </div>
						</form>
			        </div>
					</div>
<?php
if ($success === true) {
?>
			
				<div class="col-lg-6">
					<div class="panel panel-default">
						<div class="panel-heading" data-original-title="">
							<h2><i class="fa fa-check"></i><span class="break"></span>Port Scan Results</h2>
						</div>
						<div class="panel-body">
						<pre style="overflow:hidden;height:auto;width:100%;background:none;border:none;color:#000000;">

<?php echo implode(PHP_EOL, $results); ?>

								</pre>
						</div>
					</div>
				</div>

<?php
}
?>
			</div><!--/row-->
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "footer.php"; ?>
		
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