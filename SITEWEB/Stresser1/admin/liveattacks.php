<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "API Logs";
	require_once 'header.php'; 
	
		
?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?></h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
          <ol class="breadcrumb">
            <li><a href="#"><?php echo $sitename; ?></a></li>
            <li class="active"><?php echo $page; ?></li>
          </ol>
        </div>
        <!-- /.col-lg-12 -->
      </div>
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
      <div class="row">
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Live Attacks <i style="display: none;" id="manage" class="fa fa-cog fa-spin"></i></h3>
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
			xmlhttp.open("GET","../includes/ajax/admin/attacks/view.php",true);
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
			xmlhttp.open("GET","../includes/ajax/admin/attacks/stop.php?id=" + id,true);
			xmlhttp.send();
		}
		
		</script>
<?php

	require_once 'footer.php';
	
?>