<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
	if(is_numeric($_GET['id']) == false) {
		header('Location: home.php');
		exit;
	}
	
	$SQLCount = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `id` = '{$_GET['id']}'");
	if($SQLCount->fetchColumn(0) == 0){
		header('Location: home.php');
		exit;
	}
	
	$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = {$_GET['id']}");
	while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC)){
		$username = $getInfo['username'];
		$subject = $getInfo['subject'];
		$status = $getInfo['status'];
		$original = $getInfo['content'];
		$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	}

	if ($user -> safeString($original)){
		header('Location: home.php'); }
?>


<title><?php echo $sitename; ?> | Ticket</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Ticket</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Ticket</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
			<div class="container-fluid">
<div class="col-lg-12" id="div"></div>
		 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title"></h3>
           <blockquote>
										<h5><?php echo $original; ?></h5>
										<footer><?php echo $username . ' [ ' . $date . ' ]'; ?></footer>
									</blockquote>
									<div id="response"></div>

		<hr>
<form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">
										<div class="form-group">
											<div class="col-xs-12">
												<div class="form-material floating">
													<label for="reply">Your reply <i style="display: none;" id="image" class="fa fa-cog fa-spin"></i></label> 
													<textarea class="form-control" id="reply" rows="8"></textarea>
													
												</div>
											</div>
										</div>                         
                                        <div class="form-group">
                                            <div class="col-xs-12 text-center">                                             
												<button class="btn btn-sm btn-success" onclick="doReply()">
													<i class=""></i> Reply to ticket
												</button>
												<button class="btn btn-sm btn-danger" onclick="doClose()">
													<i class=""></i> Close ticket
												</button>
                                            </div>
                                        </div>
                                    </form>
          </div>
        </div>
      </div>

   <script>		
			view();
			
			function view(){
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("response").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","../vvv/files/vux/tickets/view.php?id=<?php echo $_GET['id']; ?>",true);
				xmlhttp.send();
			}
			
			function doClose(){
				document.getElementById("image").style.display="inline"; 
				document.getElementById("div").style.display="none"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("div").style.display="inline";
						document.getElementById("image").style.display="none";
					}
				}
				xmlhttp.open("GET","../vvv/files/vux/tickets/close.php?id=<?php echo $_GET['id']; ?>",true);
				xmlhttp.send();
			}
				
			function doReply() {
				var reply=$('#reply').val();
				document.getElementById("image").style.display="inline"; 
				document.getElementById("div").style.display="none"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("div").style.display="inline";
						document.getElementById("image").style.display="none";
						if (xmlhttp.responseText.search("SUCCESS") != -1) {
							view();
						}
					}
				}
				xmlhttp.open("GET","../vvv/files/vux/tickets/reply.php?id=<?php echo $_GET['id']; ?>" + "&message=" + reply,true);
				xmlhttp.send();
			}
			
			</script>
<?php

	require_once 'footer.php';
	
?>