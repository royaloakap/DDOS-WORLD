<?php
	$page = "Support";
	include 'header.php';
?>
			<main id="main-container" style="min-height: 404px;">
				<div class="content bg-gray-lighter">
                    <div class="row items-push">
                        <div class="col-sm-7">
                            <h1 class="page-heading">Tickets <small>Answer us anything</small>
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
                <div class="content" id="messages"></div>
            </main>
			<script>
			inbox();
			
			function inbox() {
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp = new XMLHttpRequest();
				}
				else {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function(){
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById("messages").innerHTML = xmlhttp.responseText;
						eval(document.getElementById("ajax").innerHTML);
					}
				}
				xmlhttp.open("GET","ajax/user/tickets/inbox.php",true);
				xmlhttp.send();
			}
			
			function unread() {
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp = new XMLHttpRequest();
				}
				else {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function(){
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						document.getElementById("messages").innerHTML = xmlhttp.responseText;
						eval(document.getElementById("ajax").innerHTML);
					}
				}
				xmlhttp.open("GET","ajax/user/tickets/unread.php",true);
				xmlhttp.send();
			}
			
			function newticket() {
				var subject = $('#subject').val();
				var content = $('#content').val();
				document.getElementById("icon").style.display="inline"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("icon").style.display="none";
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						if (xmlhttp.responseText.search("SUCCESS") != -1) {
							inbox();
						}
					}
				}
				xmlhttp.open("GET","ajax/user/tickets/newticket.php?subject=" + subject + "&content=" + content,true);
				xmlhttp.send();
			}
			</script>
			<div class="modal" id="ticket" tabindex="-1" role="dialog" aria-hidden="false" style="display: non;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="block block-themed block-transparent remove-margin-b">
							<div class="block-header bg-primary-dark">
								<ul class="block-options">
									<li>
										<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
									</li>
								</ul>
								<h3 class="block-title">Create a new ticket</h3>
								<i style="display: none;" id="icon" class="fa fa-cog fa-spin"></i>
							</div>
							<div class="block-content">
								<div id="div"></div>
								<form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">
									<div class="form-group">
										<div class="col-xs-12">
											<div class="form-material floating">
												<input class="form-control" type="text" id="subject">
												<label for="subject">Subject</label>
											</div>
										</div>
									</div> 
									<div class="form-group">
										<div class="col-xs-12">
											<div class="form-material floating">
												<textarea class="form-control" id="content" rows="8"></textarea>
												<label for="content">Content</label>
											</div>
										</div>
									</div> 
									<div class="form-group">
										<div class="col-xs-12">
											<button onclick="newticket()" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Create Ticket</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>	
<?php

	include 'footer.php';

?>