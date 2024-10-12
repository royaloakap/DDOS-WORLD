<?php
include("@/header.php");
$paginaname = 'Hub';


?>
<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<script src="../assets/js/spinner.js"></script>
<?php
$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$rowxd = $plansql -> fetch();
$name = $getInfo['name'];									
$date = date("d/m/Y, h:i a", $rowxd['expire']);									
?>
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content">
<script>
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						eval(document.getElementById("ajax").innerHTML);
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=attacks",true);
					xmlhttp.send();

					function start()
					{
					launch.disabled = true;
					var host=$('#host').val();
					var port=$('#port').val();
					var time=$('#time').val();
					var method=$('#method').val();
					document.getElementById("div").style.display="none"; 
					document.getElementById("image").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						launch.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("image").style.display="none";
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=start" + "&host=" + host + "&port=" + port + "&time=" + time + "&method=" + method,true);
					xmlhttp.send();
					}			

					function renew(id)
					{
					rere.disabled = true;
					document.getElementById("div").style.display="none";
					document.getElementById("image").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						rere.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("image").style.display="none";
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=renew" + "&id=" + id,true);
					xmlhttp.send();
					}

					function stop(id)
					{
					st.disabled = true;
					document.getElementById("div").style.display="none";
					document.getElementById("image").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						st.disabled = false;
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						document.getElementById("image").style.display="none";
						document.getElementById("div").style.display="inline";
						if (xmlhttp.responseText.search("success") != -1)
						{
						attacks();
						window.setInterval(ping(host),10000);
						}
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=stop" + "&id=" + id,true);
					xmlhttp.send();
					}

					function attacks()
					{
					document.getElementById("attacksdiv").style.display="none";
					document.getElementById("attacksimage").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						document.getElementById("attacksimage").style.display="none";
						document.getElementById("attacksdiv").style.display="inline-block";
						document.getElementById("attacksdiv").style.width="100%";
						eval(document.getElementById("ajax").innerHTML);
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=attacks",true);
					xmlhttp.send();
					}

					function adminattacks()
					{
					document.getElementById("attacksdiv").style.display="none";
					document.getElementById("attacksimage").style.display="inline"; 
					var xmlhttp;
					if (window.XMLHttpRequest)
					  {// code for IE7+, Firefox, Chrome, Opera, Safari
					  xmlhttp=new XMLHttpRequest();
					  }
					else
					  {// code for IE6, IE5
					  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					  }
					xmlhttp.onreadystatechange=function()
					  {
					  if (xmlhttp.readyState==4 && xmlhttp.status==200)
						{
						document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
						document.getElementById("attacksimage").style.display="none";
						document.getElementById("attacksdiv").style.display="inline-block";
						document.getElementById("attacksdiv").style.width="100%";
						eval(document.getElementById("ajax").innerHTML);
						}
					  }
					xmlhttp.open("GET","ajax/hub.php?type=adminattacks",true);
					xmlhttp.send();
					}
					</script>
			  <nav class="page-breadcrumb">
					 <div class="row">
                       <div class="col-md-12">
					   						<div id="divall" style="display:inline"></div>
						<div id="div" style="display:inline"></div>
						           <div class="alert alert-fill-primary" role="alert">
								          <?php
											if (!$user->hasMembership($odb))
											{
										echo '<td>WARNING! - You not have an active membership!</td>';
											}
                                            
											else
												
											{ 
										echo '<td>Attack hub available</td>'; 
											}										
											?>
											<img id="image" class="spinner-border spinner-border-sm" role="status"style="display:none"/>
											
      </div>
   </div>
  </div>
                    </nav>
    <div class="row flex-grow">
      <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
									<div class="form-group">
									<label class="col-md-3 control-label">Host</label>
									<div class="col-md-12">
									<input type="text" id="host" placeholder="https://example.com/login.php" class="form-control">
									<span class="help-block">IP Address/Website</span>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Port</label>
									<div class="col-md-12">
									<input  id="port" value="80" class="form-control">
									<span class="help-block">Default: 80</span>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Time</label>
									<div class="col-md-12">
									<input type="text" id="time" class="form-control">
									<?php
									$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
									$plansql -> execute(array(":id" => $_SESSION['ID']));
									$rowxd = $plansql -> fetch(); 
									$date = date("d/m/Y, h:i a", $rowxd['expire']);
									if (!$user->hasMembership($odb))
									{
									$rowxd['mbt'] = 0;
									$rowxd['concurrents'] = 0;
									$rowxd['name'] = 'No membership';
									$date = 'No membership';
									}
									?>
									<span class="help-block">Your max time is <?php echo $rowxd['mbt']; ?> seconds</span>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Method</label>
									<div class="col-md-12">
									<select class="form-control" id="method"">

									<optgroup label="Low Protection">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '4' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
									
									<optgroup label="High Protection">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '5' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
									<optgroup label="Premium Methods">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = '6' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) 
									if ($user -> isAdmin($odb))
									{
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option class="text-warning" value="' . $name . '">' . $fullname . '</option>';
									}
									else
																			{
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option class="text-warning" disabled value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
									
									</select>
									</div>
									</div>
                <div class="form-group form-actions">
               <div class="col-xs-2 text-center">
             <button type="button" id="launch" onclick="start()"  class="btn btn-effect-ripple btn-sm btn-primary" >Launch Attack<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>
                 </div>
				   </div>
				    </div>
					 </div>
	               <div class="col-md-6 grid-margin stretch-card">
                  <div class="card">
                 <div class="card-body">
              <span <?php if ($user -> isAdmin($odb)) {echo 'class="tip" onclick="adminattacks()" title="Click for admin mode" style="cursor:pointer"';} ?>><i  data-feather="sliders"></i></span> <img id="attacksimage" class="spinner-border text-danger" style="display:none"/>
				</div>
				<div style="position: relative; width: auto" class="slimScrollDiv">
			   <div id="attacksdiv" style="display:inline-block;width:100%"></div>										
			    </div>
				  </div>
                    </div>
                       </div>
					    </div>
<?php include("@/footer.php"); ?>					   

	  


</body>

</html>