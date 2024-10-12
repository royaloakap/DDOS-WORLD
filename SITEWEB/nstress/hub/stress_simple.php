<?php

$paginaname = 'Stress';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php 
			
			include("@/header.php");

			if (!($user->hasMembership($odb)) && $testboots == 0) {
				header('location: index.php');
				die();
			}

			?>

				
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
                    <div id="page-content">
<div class="alert alert-info">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    New method TS3 Telnet Priv8 NSTRESSER EDITION for OVH (Work on most OVH servers) </strong>
</div>
            
                        <div class="row">
						
                          
						<div class="col-xs-4">
						<div id="divall" style="display:inline"></div>
						<div id="div" style="display:inline"></div>
 
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted">Network Stresser</span>
								<i class="fa fa-bomb"></i> <b>Panel <img id="image" src="img/jquery.easytree/loading.gif" style="display:none"/></b>
								</div>
								
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">Host</label>
									<div class="col-md-9">
									<input type="text" id="host" class="form-control">
									<span class="help-block">IP Address/Website</span>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Port</label>
									<div class="col-md-9">
									<input type="text" id="port" value="80" class="form-control">
									<span class="help-block">Default: 80</span>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Time</label>
									<div class="col-md-9">
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
									<div class="col-md-9">
									<select id="method" class="form-control" size="1">

									<optgroup label="Special Methods">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'spe' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
									
									<optgroup label="UDP">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'udp' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
									
									<optgroup label="TCP">
									<?php
									$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'tcp' ORDER BY `id` ASC");
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$name     = $getInfo['name'];
										$fullname = $getInfo['fullname'];
										echo '<option value="' . $name . '">' . $fullname . '</option>';
									}
									?>
									</optgroup>
													</optgroup>
													<optgroup label="Layer 7">
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
									<div class="col-md-12">
									<button id="launch" onclick="start()" class="btn btn-effect-ripple btn-primary btn-block">Send Attack (Rotation)</button>
									</div>
									</div>
									</div>
									</div>
								
							</div>
						</div>
						
						<div class="col-xs-8">
						
							
 
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted">Network Stresser</span>
								<i class="fa fa-send"></i> <span <?php if ($user -> isAdmin($odb)) {echo 'class="tip" onclick="adminattacks()" title="Click for admin mode" style="cursor:pointer"';} ?>><strong>Manage</strong> Attacks</span> <img id="attacksimage" src="img/jquery.easytree/loading.gif" style="display:none"/>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
									<div id="attacksdiv" style="display:inline-block;width:100%"></div>
										
									</div>
								</div>							
							</div>
						
						
						<div class="col-xs-8">
						
							
 
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<span class="pull-right text-muted">Network Stresser</span>
								<i class="fa fa-send"></i> <b>Basic Servers</b>
								</div>
								
								<div style="position: relative; width: auto" class="slimScrollDiv">
									<div id="stats">
										<table class="table table-striped">
											<tbody>
												<tr>
													<th><center>Name</center></th>
													<th><center>Attacks</center></th>
													<th><center>Connection</center></th>
													
													<th><center>Type</center></th>
												</tr>
												<tr>
												
												</tr>												
												<?php
												if ($system == 'api') {
													$SQLGetInfo = $odb->query("SELECT * FROM `api` ORDER BY `id` DESC");
												} else {
													$SQLGetInfo = $odb->query("SELECT * FROM `servers` ORDER BY `id` DESC");
												}
												while ($getInfo = $SQLGetInfo->fetch(PDO::FETCH_ASSOC)) {
													$name    = $getInfo['name'];
													$kind    = $getInfo['kind'];
													$connection    = $getInfo['connection'];
													$attacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
													$load    = round($attacks / $getInfo['slots'] * 100, 2);
													echo '
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$(\'#ra'.$name.'\').load(\'ajax/servers.php?sv='.$name.'\').fadeIn("slow");
													}, 25000); // refresh every 10000 milliseconds
													</script>
													
													
													<tr>
																<td><center>' . $name . '</center></td>
																<td><center><div id="ra'.$name.'"></center></td>
																<td><center>' . $connection . '</center></td>
																<td><center><span class="label label-primary">' . $kind . '</span></center></td>														   </tr>';
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
								
							</div>
<span class="label label-info">Join our Discord Server: <a href="https://discord.gg/mP6SrPM" style="color: #ffffff">Click here</a></span>
						</div>
						 
					</div>	
<br>
   <div class="alert alert-info">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    How use HTTP Advanced: This method uses random search query's on the end of the link. Example: www.yourlink.com/search?= </strong>
</div>
</div>

				</div>
                     <? // NO BORRAR LOS TRES DIVS! ?>
               </div>
               </div>
             
          </div>

		<?php include("@/script.php"); ?>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/58432a0c8a20fc0cac4bcca0/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
    </body>
</html>
