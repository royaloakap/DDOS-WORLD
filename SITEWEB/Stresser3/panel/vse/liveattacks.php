<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "API Logs";
	require_once 'header.php'; 
	
		
?>


<title><?php echo $sitename; ?> | Live Attacks</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Live Attacks</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Live Attacks</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
			<div class="container-fluid">
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
      <div class="row">
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title"><i style="display: none;" id="manage" class=""></i></h3>
				<div id="attacksdiv" style="display:inline-block;width:100%"></div>
			
          </div>
        </div>
		
      </div>
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
			xmlhttp.open("GET","../vvv/files/vux/attacks/view.php",true);
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


					}

				}

			}

			xmlhttp.open("GET","../vvv/files/vux/attacks/stop.php?id=" + id,true);

			xmlhttp.send();

		}

		
		</script>