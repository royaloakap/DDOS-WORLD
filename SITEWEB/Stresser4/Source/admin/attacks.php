<?php
	include 'header.php';
	if (!($user->hasMembership($odb)) && $testboots == 0) {
		header('location: index.php');
		exit;
	}
?>
			<main id="main-container" style="min-height: 404px;">
                <div class="content bg-gray-lighter">
                    <div class="row items-push">
                        <div class="col-sm-7">
                            <h1 class="page-heading">Users <small> Manage Attacks</small>
                            </h1>
                        </div>
                        <div class="col-sm-5 text-right hidden-xs">
                            <ol class="breadcrumb push-10-t">
                                <li>User Management</li>
                                <li><a class="link-effect" href="attacks.php">Attacks</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content content-narrow">    
                    <div class="row">  
						<div class="col-lg-12" id="div"></div>
						<div class="col-lg-7">
                            <div class="block">
                                <div class="block-header bg-primary">
									<h3 class="block-title">
										Manage Attacks
										<i style="display: none;" id="manage" class="fa fa-cog fa-spin"></i>
									</h3>
								</div>
                                <div class="block-content">
                                    <div id="attacksdiv" style="display:inline-block;width:100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>     
                </div>
            </main>
		<script>
		attacks();
	
		function attacks() {
			document.getElementById("attacksdiv").style.display = "none";
			document.getElementById("manage").style.display = "inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			}
			else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("attacksdiv").innerHTML = xmlhttp.responseText;
					document.getElementById("manage").style.display = "none";
					document.getElementById("attacksdiv").style.display = "inline-block";
					document.getElementById("attacksdiv").style.width = "100%";
					eval(document.getElementById("ajax").innerHTML);
				}
			}
			xmlhttp.open("GET","../ajax/admin/attacks/view.php",true);
			xmlhttp.send();
		}
		
		function stop(id) {
			document.getElementById("manage").style.display="inline"; 
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
					document.getElementById("manage").style.display="none";
					if (xmlhttp.responseText.search("success") != -1) {
						attacks();
						window.setInterval(ping(host),10000);
					}
				}
			}
			xmlhttp.open("GET","../ajax/admin/attacks/stop.php?id=" + id,true);
			xmlhttp.send();
		}
		
		</script>
<?php

	include 'footer.php';

?>