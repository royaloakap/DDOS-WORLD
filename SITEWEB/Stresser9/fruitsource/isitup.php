<?php
function gen_domain($domain, $port) {
    return $domain . (isset($port) && $port != 80 ? ":" . $port : "");
}

function is_valid_domain($domain) {
	return (
		filter_var($domain, FILTER_VALIDATE_URL) ||
		filter_var("http://" . $domain, FILTER_VALIDATE_URL) ||
		filter_var("http://" . $domain, FILTER_VALIDATE_IP) ||
		filter_var($domain, FILTER_VALIDATE_IP)
	);
}

function sendHTTPHead($domain, $headers) {
	$ch = curl_init($domain);
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER     => array(
		),
		CURLOPT_TIMEOUT        => 15,
		CURLOPT_CUSTOMREQUEST  => "HEAD",
		CURLOPT_REFERER        => "http://gigastress.com/",
		CURLOPT_USERAGENT      => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :
									"GigaStress/1.1"),
	));
	curl_exec($ch);
	return $ch;
}

function get_response($domain, $port) {    
    if ( is_valid_domain($domain) ) {
		$x = sendHTTPHead("http://" . $domain, array());
        $data = array
        (
            "code" => curl_getinfo($x, CURLINFO_HTTP_CODE),
            "time" => curl_getinfo($x, CURLINFO_CONNECT_TIME),
            "valid" => true
        );
    }
    else
    {
        $data = array
        (
            "code" => null,
            "time" => 0,
            "valid" => false
        );
    }
    
    return $data;
}

function fetchID($data) {
    $goodHTTPCodes = array(200, 301, 302, 303, 304, 307, 400, 401, 403, 405);
    return (
		($data['valid'] === false ? 3 : (
						is_numeric($data['code']) && in_array($data['code'], $goodHTTPCodes) ? 1 : 2
			)
		)
	);
}

function gen_html($id, $domain, $port, $time, $code) {
    $units = ($time == 1 ? "second" : ($time > 1 ? "seconds" : "ms"));
	
	$time = ($time < 1 ? $time*1000 : round($time, 2));
    if ($time <= 0 )
		$time = "< 1";

    if ( $id == 1 )
    {
        $html  = "<div class='alert alert-success'>
				Request was sent to <a href=\"http://" . gen_domain($domain, $port) . "\">" . $domain . "</a> and it took " . $time . " " . $units . " to receive a
				" . $code . " response code to " . $domain . (filter_var("http://" . $domain, FILTER_VALIDATE_URL) ? " (" . gethostbyname($domain) . ")" : "") . "</div>";
    }
    else if ( $id == 2 )
    {
        $html = "<div class='alert alert-warning'>
			A request was sent to <a href=\"http://" . gen_domain($domain, $port) . "\">" . $domain . "</a>" .
			(!empty($code) && is_numeric($code) ? " and we received a " . $code . " response code," : " and") . " it appears to be down.</div>";
    }
    else if ( $id == 3 )
    {
        $html  = "<div class='alert alert-danger'>You specified an invalid domain or ip address</div>";
    }
	
    return $html;
}

function filter_domain($domain)
{
	$domain = preg_replace("/[^A-Za-z0-9-\/\.\:]/", "", trim($domain));
	// Split the variable into two, $domain & $port.
	$result = explode(":", $domain);
	// If the port is not numeric or not set we use port 80.
	if (!isset($result[1]) || !is_numeric($result[1]))
	{
	$result[1] = 80;
	}
	return $result;
}
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
$page = "IsItUp?";
include ("head.php");
if (isset($_POST['checkStatus'], $_POST['address']) && !empty($_POST['address'])) {
	$addr = preg_replace("/^https?:\/\//is", "", $_POST['address']);
	$addr = rtrim($addr, "/");
	
	list($domain, $port) = filter_domain($addr);
	
	$data = get_response($domain, $port);
	
	$time = round($data["time"], 3);
	$id = fetchID($data);
	$html = gen_html($id, $domain, $port, $time, $data["code"]);
}
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
			<div class="col-md-6 col-md-offset-3">
				<?php
					echo (isset($html) && !empty($html) ? $html : "");
				?>
			        <div class="panel panel-default">
			            <div class="panel-heading">
			                <h2><strong>Is</strong>ItUp?</h2>
			            </div>
			            <div class="panel-body">
							<form action="" method="post" class="form-horizontal ">
				                <div class="form-group">
				                    <label class="col-md-3 control-label" for="hf-email">Host</label>
				                    <div class="col-md-9">
				                      <input name="address" class="form-control" type="text" placeholder="http://example.com/"/>
				                    </div>
				                </div>
							</div>
						<div class="panel-footer">
		                    <button type="submit" name="checkStatus" class="btn btn-sm btn-success"><i class="fa fa-fire"></i>Is It Up?</button>
		                </div>
						</form>
			        </div>
					</div>
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