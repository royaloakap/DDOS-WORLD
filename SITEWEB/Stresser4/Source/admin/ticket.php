<?php

	include 'header.php';
	
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
		header('Location: home.php');
		exit;
	}
	
?>
			<main id="main-container" style="min-height: 404px;">
                <div class="content bg-gray-lighter">
                    <div class="row items-push">
                        <div class="col-sm-7">
                            <h1 class="page-heading">Ticket <small> View Your Ticket</small>
                            </h1>
                        </div>
                        <div class="col-sm-5 text-right hidden-xs">
                            <ol class="breadcrumb push-10-t">
                                <li>Home</li>
                                <li><a class="link-effect" href="support.php">Tickets</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content content-narrow">    
                    <div class="row">  
						<div class="col-lg-12" id="div"></div>
                        <div class="col-lg-6">                          
                            <div class="block block-themed">
                                <div class="block-header bg-primary">                                 
                                    <h3 class="block-title">
										Conversation
									</h3>
                                </div>
                                <div class="block-content">
									<blockquote>
										<h5><?php echo $original; ?></h5>
										<footer><?php echo $username . ' [ ' . $date . ' ]'; ?></footer>
									</blockquote>
									<div id="response"></div>
                                </div>
                            </div>                          
                        </div>
						<div class="col-lg-6">                          
                            <div class="block block-themed">
                                <div class="block-header bg-primary">                                 
                                    <h3 class="block-title">
										Post a reply
										<i style="display: none;" id="image" class="fa fa-cog fa-spin"></i>
									</h3>
                                </div>
                                <div class="block-content">
									<form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">
										<div class="form-group">
											<div class="col-xs-12">
												<div class="form-material floating">
													<textarea class="form-control" id="reply" rows="8"></textarea>
													<label for="reply">Your reply</label>
												</div>
											</div>
										</div>                         
                                        <div class="form-group">
                                            <div class="col-xs-12 text-center">                                             
												<button class="btn btn-sm btn-success" onclick="doReply()">
													<i class="fa fa-plus push-5-r"></i> Reply to ticket
												</button>
												<button class="btn btn-sm btn-danger" onclick="doClose()">
													<i class="fa fa-ban push-5-r"></i> Close ticket
												</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>                          
                        </div>
                    </div>     
                </div>
            </main>
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
				xmlhttp.open("GET","../ajax/admin/tickets/view.php?id=<?php echo $_GET['id']; ?>",true);
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
				xmlhttp.open("GET","../ajax/admin/tickets/close.php?id=<?php echo $_GET['id']; ?>",true);
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
				xmlhttp.open("GET","../ajax/admin/tickets/reply.php?id=<?php echo $_GET['id']; ?>" + "&message=" + reply,true);
				xmlhttp.send();
			}
			
			</script>
<?php

	include 'footer.php';

?>