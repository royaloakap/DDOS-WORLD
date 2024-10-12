<?php


	$page = "Panel";
	include 'header.php';



	if (!($user->hasMembership($odb)) && $testboots == 0) {

		header('location: index.php');

		exit;

	}
	
	if (!($user->isSupporter($odb))){

		header('location: index.php');

		exit;

	}

	

?>
			
		
			<main id="main-container" style="min-height: 404px;">
			<div class="content bg-white border-b">
			<script type="text/javascript">
				var auto_refresh = setInterval(
				function ()
				{
					$('#ta').load('ajax/user/tools/fuck.php?v=ta').fadeIn("slow");
					$('#ra').load('ajax/user/tools/fuck.php?v=ra').fadeIn("slow");
					$('#ts').load('ajax/user/tools/fuck.php?v=ts').fadeIn("slow");
					$('#tu').load('ajax/user/tools/fuck.php?v=my').fadeIn("slow");
					$('#live_servers').load('ajax/user/tools/servers.php').fadeIn("slow");			
				}, 1000);
			</script>
			<div class="row items-push text-uppercase">
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">Total Attacks</div>
					<a class="h2 font-w300 text-primary animated flipInX" id="ta" href="#"></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">Running Attacks</div>
					<a class="h2 font-w300 text-primary animated flipInX" id="ra" href="#"></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">Total Servers</div>
					<a class="h2 font-w300 text-primary animated flipInX" id="ts" href="#"></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">Your Attacks</div>
					<a class="h2 font-w300 text-primary animated flipInX" id="tu" href="#"></a>
				</div>
				</div>
		</div>

                <div class="content bg-gray-lighter">

                    <div class="row items-push">

                        <div class="col-sm-7">

                            <h1 class="page-heading">Panel <small> Launch and manage attacks</small>

                            </h1>

                        </div>

                        <div class="col-sm-5 text-right hidden-xs">

                            <ol class="breadcrumb push-10-t">

                                <li>Home</li>

                                <li><a class="link-effect" href="hub.php">Panel</a></li>

                            </ol>

                        </div> 

                    </div>

                </div>
				

                <div class="content content-narrow">    

                    <div class="row">  
					

						<div class="col-lg-12" id="div"></div>

					

                        <div class="col-lg-4">                          

                            <div class="block block-themed animated tada">

                                <div class="block-header bg-primary">                                 

                                    <h3 class="block-title">
									<?php
									$user = $_SESSION['username'];
									$oneday = time() - 86400;
									$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` LIKE :user AND `date` > :date");
									$SQL -> execute(array(":date" => $oneday, ':user' => $user));
									$todayattacks = $SQL->fetchColumn(0);
									?>
							
										Launch Attack

										<i style="display: none;" id="image" class="fa fa-cog fa-spin"></i>

									</h3>

                                </div>

                                <div class="block-content">

                                    <form class="form-horizontal push-10-t push-10" method="post" onsubmit="return false;">

                                        <div class="form-group">

                                            <div class="col-xs-6">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="host" name="host">

                                                    <label for="host">Host</label>

                                                </div>

                                            </div>

                                            <div class="col-xs-6">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="port" name="port">

                                                    <label for="port">Port</label>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material input-group floating">

                                                    <input class="form-control" type="text" id="time" name="time">

                                                    <label for="time">Time</label>

                                                    <span class="input-group-addon">seconds</span>

                                                </div>

                                            </div>

                                        </div>
										
                                        <div class="form-group">

                                            <div class="col-sm-12">

												<div class="form-material floating open">

													<select class="form-control" id="method" name="method" size="1">
														<optgroup label=""></optgroup>
														<optgroup label="UDP Amplication">

														<?php

														$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'udp' ORDER BY `id` ASC");

														while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {

															$name = $getInfo['name'];

															$fullname = $getInfo['fullname'];

															echo '<option value="' . htmlentities($name) . '">' . htmlentities($fullname) . '</option>';

														}

														?>

														</optgroup>
														
														<optgroup label="TCP Amplication">

														<?php

														$SQLGetLogs = $odb->query("SELECT * FROM `methods` WHERE `type` = 'tcp' ORDER BY `id` ASC");

														while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {

															$name = $getInfo['name'];

															$fullname = $getInfo['fullname'];

															echo '<option value="' . htmlentities($name) . '">' . htmlentities($fullname) . '</option>';

														}

														?>

														</optgroup>

														<optgroup label="Layer7 Attacks">

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

                                        </div>      
							<div class="form-group">
								<div class="col-xs-6">
									<label class="css-input css-checkbox css-checkbox-primary">
										<input type="checkbox"><span></span> Admin Network
									</label>
								</div>
							</div>

                                        <div class="form-group">

                                            <div class="col-xs-12 text-center">                                             

												<button class="btn btn-sm btn-success" onclick="start()" type="submit">

													<i class="fa fa-plus push-5-r"></i> Start

												</button>							

												<button data-toggle="modal" data-target="#tools" class="btn btn-sm btn-primary" type="submit">

													<i class="fa fa-cog push-5-r"></i> Tools

												</button>

												<button data-toggle="modal" data-target="#servers" class="btn btn-sm btn-gray" type="submit">

													<i class="fa fa-download push-5-r"></i> Servers

												</button>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div> 

                        </div>

						<div class="col-lg-8">

                            <div class="block animated tada">

                                <div class="block-header bg-primary">

									<h3 class="block-title">

										Manage Attacks

										<i style="display: none;" id="manage" class="fa fa-cog fa-spin"></i>

									</h3>

								</div>

                                <div class="block-content">
			
									<div class="animated zoomIn" id="attacksdiv" style="display:inline-block;width:100%"></div>

                                </div>

                            </div>

                        </div>

                    </div>     
                </div>

            </main>

		<script>

		attacks();

		getip();

		getfae();

		getservers();
	
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

			xmlhttp.open("GET","ajax/user/attacks/attacks.php",true);

			xmlhttp.send();

		}
		
		function getservers() {

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp = new XMLHttpRequest();

			}

			else {

				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

					document.getElementById("serverss").innerHTML = xmlhttp.responseText;

					eval(document.getElementById("ajax").innerHTML);

				}

			}

			xmlhttp.open("GET","ajax/user/tools/servers.php",true);

			xmlhttp.send();

		}

		

		function getip() {

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp = new XMLHttpRequest();

			}

			else {

				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange = function(){

				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

					document.getElementById("iplogger").innerHTML = xmlhttp.responseText;

					eval(document.getElementById("ajax").innerHTML);

				}

			}

			xmlhttp.open("GET","ajax/user/tools/iplogger.php",true);

			xmlhttp.send();

		}

		

			

		function start() {

			var host=$('#host').val();

			var port=$('#port').val();

			var time=$('#time').val();

			var method=$('#method').val();
			
			var vipmode=$('input:checkbox:checked').val();

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

			xmlhttp.open("GET","ajax/user/attacks/admin.php?type=start" + "&host=" + host + "&port=" + port + "&time=" + time + "&method=" + method + "&vipmode=" + vipmode,true);

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

			xmlhttp.open("GET","ajax/user/attacks/hub.php?type=renew&id=" + id,true);

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

			xmlhttp.open("GET","ajax/user/attacks/hub.php?type=stop" + "&id=" + id,true);

			xmlhttp.send();

		}

		

		function tools_s() {

			var tool_v=$('#tool_v').val();

			var resolver_t=$('#resolver_t').val();

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("tools_show").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + resolver_t + "&resolve=" + tool_v,true);

			xmlhttp.send();

		}
		
		function tools_skype() {

			var tool_vskype=$('#tool_vskype').val();

			var resolver_tskype=$('#resolver_tskype').val();

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("skypes").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + resolver_tskype + "&resolve=" + tool_vskype,true);

			xmlhttp.send();

		}
		
		function ping_s() {

			var ping_v=$('#ping_v').val();

			var ping_t=$('#ping_t').val();

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("pings").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + ping_t + "&resolve=" + ping_v,true);

			xmlhttp.send();

		}

		

		function removeip(id) {

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("ipdiv").innerHTML=xmlhttp.responseText;

					if (xmlhttp.responseText.search("SUCCESS") != -1) {

						getip();

					}

				}

			}

			xmlhttp.open("GET","ajax/user/removeip.php?id=" + id,true);

			xmlhttp.send();

		}

		

		function addfae() {

			var id=$('#faeid').val();

			var method=$('#fore').val();

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("faediv").innerHTML=xmlhttp.responseText;

					getfae();

				}

			}

			xmlhttp.open("GET","ajax/user/tools/fae.php?action=add&method=" + method + "&id=" + id,true);

			xmlhttp.send();

		}

		

		function removefae(id) {

			document.getElementById("fckimg").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("fckimg").style.display="none";

					document.getElementById("faediv").innerHTML=xmlhttp.responseText;

					if (xmlhttp.responseText.search("SUCCESS") != -1) {

						getfae();

					}

				}

			}

			xmlhttp.open("GET","ajax/user/tools/fae.php?action=remove&id=" + id,true);

			xmlhttp.send();

		}

		

		function getfae() {

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp = new XMLHttpRequest();

			}

			else {

				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange = function(){

				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

					document.getElementById("faelist").innerHTML = xmlhttp.responseText;

					eval(document.getElementById("ajax").innerHTML);

				}

			}

			xmlhttp.open("GET","ajax/user/tools/faelist.php",true);

			xmlhttp.send();

		}

		</script>

		<div class="modal" id="tools" tabindex="-1" role="dialog" aria-hidden="false" style="display: non;">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="block block-themed block-transparent remove-margin-b">

                        <div class="block-header bg-primary-dark">

                            <ul class="block-options">

                                <li>

                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>

                                </li>

                            </ul>

                            <h3 class="block-title">

								Stresser Tools
								
								<i style="display: none;" id="fckimg" class="fa fa-cog fa-spin"></i>

							</h3>

                        </div>

                        <div class="block-content">

                            <div class="block">

                                <ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">

                                    <li class="active">

                                        <a href="#tools_tab"><i class="si si-game-controller"></i> Tools</a>

                                    </li>
									
							 <li class="">

                                        <a href="#skype_tab"><i class="fa fa-skype"></i> Skype Tools</a>

                                    </li>
									
									 <li class="">

                                        <a href="#ping_tab"><i class="fa fa-send"></i> Ping Tools</a>

                                    </li>

                                    <li class="">

                                        <a href="#fae"><i class="fa fa-user"></i> Friends and Enemies</a>

                                    </li>

                                </ul>

                                <div class="block-content tab-content">

                                    <div class="tab-pane active" id="tools_tab">

                                        <form class="form-horizontal push-10-t push-10" method="post" onsubmit="return false;">

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="tool_v">

                                                    <label for="tool">Value</label>

                                                </div>

                                            </div>

                                        </div>         

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating open">

                                                    <select class="form-control" id="resolver_t" size="1">
														
														<option value="ports">Check ports open</option>
														
														<option value="domain">Domain Resolver</option>

														<option value="cloudflare">Cloudflare Resolver</option>
														
														<option value="geo">Geolocation</option>
														

                                                    </select>

                                                </div>

                                            </div>

                                        </div>                   

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <button onclick="tools_s()" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Find Results</button>

                                            </div>

                                        </div>

										<div id="tools_show"></div>

                                    </form>

                                    </div>
									
							<div class="tab-pane" id="skype_tab">

                                       <form class="form-horizontal push-10-t push-10" method="post" onsubmit="return false;">

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="tool_vskype">

                                                    <label for="tool">Value</label>

                                                </div>

                                            </div>

                                        </div>         

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating open">

                                                    <select class="form-control" id="resolver_tskype" size="1">

                                                        <option value="skype">Skype Resolver</option>
														
												    <option value="skypedb">Skype DB Logs</option>
													
													    <option value="ip2skype">IP to Skype</option>		

															  <option value="email2skype">Email to Skype</option>
															  
																  <option value="skype2email">Skype to Email</option>
													
                                                    </select>

                                                </div>

                                            </div>

                                        </div>                   

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <button onclick="tools_skype()" class="btn btn-sm btn-info"><i class="fa fa-skype"></i> Resolve</button>

                                            </div>

                                        </div>

										<div id="skypes"></div>

                                    </form>


                                    </div>
									
							<div class="tab-pane" id="ping_tab">

                                       <form class="form-horizontal push-10-t push-10" method="post" onsubmit="return false;">

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="ping_v">

                                                    <label for="tool">Value</label>

                                                </div>

                                            </div>

                                        </div>         

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating open">

                                                    <select class="form-control" id="ping_t" size="1">

                                                        <option value="ipv4">Ping IPv4</option>
														
												    <option value="ping_domain">Ping Domain</option>
													
                                                    </select>

                                                </div>

                                            </div>

                                        </div>                   

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <button onclick="ping_s()" class="btn btn-sm btn-info"><i class="fa fa-send"></i> Ping</button>

                                            </div>

                                        </div>

										<div id="pings"></div>

                                    </form>


                                    </div>

                                    <div class="tab-pane" id="fae">

                                        <form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating">

                                                    <input class="form-control" type="text" id="faeid">

                                                    <label for="tool">Value</label>

                                                </div>

                                            </div>

                                        </div>         

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <div class="form-material floating open">

                                                    <select class="form-control" id="fore" size="1">

                                                        <option value="f">Friend</option>

														<option value="e">Enemy</option>

                                                    </select>

                                                </div>

                                            </div>

                                        </div>                   

                                        <div class="form-group">

                                            <div class="col-xs-12">

                                                <button onclick="addfae()" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Add User</button>

                                            </div>

                                        </div>

										<div id="faediv"></div>

										<div id="faelist"></div>

                                    </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

		<div class="modal" id="servers" tabindex="-1" role="dialog" aria-hidden="false" style="display: non;">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="block block-themed block-transparent remove-margin-b">

                        <div class="block-header bg-primary-dark">

                            <ul class="block-options">

                                <li>

                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>

                                </li>

                            </ul>

                            <h3 class="block-title">

								Servers

							</h3>

                        </div>

                        <div class="block-content">

                            <div class="block">
                                <div id="live_servers"></div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55e4274704318bcf2cc63f28/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<?php



	include 'footer.php';



?>