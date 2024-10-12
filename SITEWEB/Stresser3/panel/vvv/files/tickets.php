<?php 

header("X-XSS-Protection: 1; mode=block");

function isXSS($string){
  $xssparameters = array(
    "<SCRIPT",
    "<script",
    "<script>",
    "<iframe",
    ".css",
    ".js",
    "<meta",
    ">",
    "UPDATE",
	"http-equiv",
	"stressit.club",
	"pornhub.com",
    "*",
    ",'",
    "''",
    "'",
    "<frame",
    "<img",
    "<embed",
    "<xml",
    "ALERT(",
    "<IFRAME",
    "</",
    "<?php",
    "?>",
    "SCRIPT>",
    "JS>",
    "<JS",
    "JSON>",
    ".replace",
    "unescape",
    "<JSON",
    "SCRIPT",
    "DIV",
    ".CCS",
    ".JS",
    "<META",
    "<FRAME",
    "<EMBED",
    "<XML",
    "<IFRAME",
    "<IMG",
    ";--",
    "nc",
    "ncat",
    "netcat",
    "curl",
    "telnet",
    "sudo",
    ".sh",
    "install",
    "sudo",
    "bash"
  );
  foreach ($xssparameters as $xssparameter) {
    if (strpos($string, $xssparameter) !== false) {
      return true;
    }
  }
}

function xss_cleaner($input_str) {
  $return_str = str_replace( array('<','>',"'",'"',')','('), array('&lt;','&gt;','&apos;','&#x22;','&#x29;','&#x28;'), $input_str );
  $return_str = str_ireplace( '%3Cscript', '', $return_str );
  return $return_str;
} 

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php';

    $TotalUsers = $odb->query("SELECT COUNT(*) FROM `users`")->fetchColumn(0);
	$TotalPaidUsers = $odb->query("SELECT COUNT(*) FROM `users` WHERE `membership`")->fetchColumn(0);
	$TodayAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()")->fetchColumn(0);
	$MonthAttack = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()  - INTERVAL 30 DAY")->fetchColumn(0);
	$TotalAttacks = $odb->query("SELECT COUNT(*) FROM `logs`")->fetchColumn(0);
	$TotalPools = $odb->query("SELECT COUNT(*) FROM `api`")->fetchColumn(0);
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$Closed = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Closed'")->fetchColumn(0);
	$Opened = $odb->query("SELECT COUNT(id) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Waiting for admin response'")->fetchColumn(0);
?>

<title>SCAPY | Tickets</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Tickets</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page">SCAPY</li>
								<li class="breadcrumb-item text-muted" aria-current="page">Tickets</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
            <div class="container-fluid">
                <div class="row">
					<div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
								<div class="mt-2 activity">
									<div class="col-lg-12">
										<div class="mt-2">
											<p><i class="fas fa-check"></i> Open <b>(<?php echo $Opened; ?>)</b></p>
										</div>
										<div class="mt-2">
											<p><i class="fas fa-times fa-3x" style='font-size:17px'></i> Closed <b>(<?php echo $Closed; ?>)</b></p>
										</div>
										<div class="mt-4">
											<b class="header-title">Color Codes:</b>
										</div>
										<div class="mt-2">
											<p><span class="fas fa-circle text-primary"></span> Waiting for user response</p>
											<p><span class="fas fa-circle text-success"></span> Waiting for admin response</p>
											<p><span class="fas fa-circle text-danger"></span> Closed</p>
										</div>
									</div>
								</div>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6 col-lg-8">
						<div id="div"></div>
                        <div class="card">
                            <div class="card-body">
								<div class="mt-2 activity">
									<form method="post" onsubmit="return false;">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label class="text-white" for="subject">Subject</label>
													<input class="form-control" id="subject" name="subject" type="text" placeholder="Example: Can I pay via ETH?">
												</div>
												<div class="form-group">
													<label class="text-white" for="content">Content</label>
													<textarea class="form-control" id="content" name="content" type="text" placeholder="Example: Hello! I'd like to pay via ETH crypto..."></textarea>
												</div>
												<center><button onclick="newticket()" type="submit" class="btn btn-primary">Send Ticket</button></center>
											</div>
										</div>
									</form>
								</div>
                            </div>
                        </div>
                    </div>
					<div class="col-md-6 col-lg-12">
                        <div class="card">
                            <div class="card-body">
								<h4 class="card-title">View Tickets</h4>
								<div class="mt-4 activity">
								    <div class="content" id="messages"><div class="table-responsive">
							</div>
					     </div>
						</div>
					    </div>
					<script>
					inbox();

					function inbox()
					{
						var xmlhttp;
						if (window.XMLHttpRequest)
						{
							xmlhttp = new XMLHttpRequest();
						}
						else
						{
							xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange = function()
						{
							if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
							{
								document.getElementById("messages").innerHTML = xmlhttp.responseText;
								eval(document.getElementById("ajax").innerHTML);
							}
						}
						xmlhttp.open("GET","vvv/files/inbox.php",true);
						xmlhttp.send();
					}
					
					function newticket()
					{
						var subject = $('#subject').val();
						var content = $('#content').val();
						var xmlhttp;
						if (window.XMLHttpRequest)
						{
							xmlhttp=new XMLHttpRequest();
						}
						else
						{
							xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange=function()
						{
							if (xmlhttp.readyState==4 && xmlhttp.status==200)
							{
								document.getElementById("div").innerHTML=xmlhttp.responseText;
								if (xmlhttp.responseText.search("SUCCESS") != -1)
								{
									inbox();
								}
							}
						}
						xmlhttp.open("GET","vvv/files/newticket.php?subject=" + subject + "&content=" + content,true);
						xmlhttp.send();
					}
					</script>			