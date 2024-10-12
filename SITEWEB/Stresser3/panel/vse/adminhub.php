<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
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
		if(isset($done)){
			echo success($done);
		}
		?>
      <div class="row">
<div class="col-lg-12" id="div"></div>
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Admin Hub <i style="display: none;" id="image" class="fa fa-cog fa-spin"></i></h3>
				<form class="form-horizontal"  method="post" onsubmit="return false;">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Host</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="host" name="host" placeholder="1.1.1.1 or http://link.com">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Port</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="port" name="port" placeholder="80">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Time (Seconds)</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="time" name="time" placeholder="30">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-3 control-label">Method</label>
                <div class="col-sm-9">
                  <select class="form-control" id="method" name="method">
														<optgroup label="Layer 4 Methods">
														<?php
														$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'layer4' ORDER BY `id` ASC");
														while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
															$name = $getInfo['name'];
															$fullname = $getInfo['fullname'];
															echo '<option value="' . htmlentities($name) . '">' . htmlentities($fullname) . '</option>';
														}
														?>
														</optgroup>
														<optgroup label="Layer 7 Methods">
														<?php
															$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'layer7' ORDER BY `id` ASC");
															while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
																$name     = $getInfo['name'];
																$fullname = $getInfo['fullname'];
																echo '<option value="' . $name . '">' . $fullname . '</option>';
															}
														?>
														</optgroup>
													</select>
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword4" class="col-sm-3 control-label">Servers</label>
                <div class="col-sm-9">
                  <select class="form-control" id="servers" name="servers">
				  <option value="vip">VIP Servers</option>
				  <option value="normal">Normal Servers</option>
				  <option value="all">Total Network</option>
				  </select>
                </div>
              </div>
              <div class="form-group m-b-0">
                <div class="col-sm-offset-3 col-sm-9">
                  <button class="btn btn-success" onclick="start()" type="submit">
													<i class="fa fa-plus push-5-r"></i> Start
												</button>

                </div>
              </div>
            </form>
				

          </div>
        </div>
      </div>
<script>
function start() {
			var host=$('#host').val();
			var port=$('#port').val();
			var time=$('#time').val();
			var method=$('#method').val();
			var servers=$('#servers').val();
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
					if (xmlhttp.responseText.search("success") != -1) {
						attacks();
						window.setInterval(ping(host),10000);
					}
				}
			}
			xmlhttp.open("GET","../vvv/files/vux/hub.php?type=start" + "&host=" + host + "&port=" + port + "&time=" + time + "&method=" + method + "&servers=" + servers,true);
			xmlhttp.send();
		}
</script>
	  
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>