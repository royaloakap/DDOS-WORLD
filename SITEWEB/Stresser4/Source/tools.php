<?php

	$page = "Tools";

	include 'header.php';

	

?>

<main id="main-container" style="min-height: 404px;"> 

	<div class="content bg-gray-lighter">

		<div class="row items-push">

			<div class="col-sm-8">

				<h1 class="page-heading">

					Tools <small>Skype Resolver, Domain Resolver...</small>

				</h1>

			</div>

			<div class="col-sm-4 text-right hidden-xs">

				<ol class="breadcrumb push-10-t">

					<li>Home</li>

					<li><a class="link-effect" href="tools.php">Tools</a></li>

				</ol>

			</div>

		</div>

	</div>

	<script>
		function tools_s() {

			var tool_v=$('#tool_v').val();

			var resolver_t=$('#resolver_t').val();

			document.getElementById("tool").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("tool").style.display="none";

					document.getElementById("tools").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + resolver_t + "&resolve=" + tool_v,true);

			xmlhttp.send();

		}
		
		function tools_skype() {

			var tool_vskype=$('#tool_vskype').val();

			var resolver_tskype=$('#resolver_tskype').val();

			document.getElementById("skype").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("skype").style.display="none";

					document.getElementById("skypes").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + resolver_tskype + "&resolve=" + tool_vskype,true);

			xmlhttp.send();

		}
		
		function ping_s() {

			var ping_v=$('#ping_v').val();

			var ping_t=$('#ping_t').val();

			document.getElementById("ping").style.display="inline"; 

			var xmlhttp;

			if (window.XMLHttpRequest) {

				xmlhttp=new XMLHttpRequest();

			}

			else {

				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");

			}

			xmlhttp.onreadystatechange=function() {

				if (xmlhttp.readyState==4 && xmlhttp.status==200) {

					document.getElementById("ping").style.display="none";

					document.getElementById("pings").innerHTML=xmlhttp.responseText;

				}

			}

			xmlhttp.open("GET","ajax/user/tools/tools.php?type=" + ping_t + "&resolve=" + ping_v,true);

			xmlhttp.send();

		}




		</script>
	
	<div class="content content-narrow">

		<div class="row">  

			<div class="col-md-12">

				<div class="block">

					<div class="block-header bg-primary">

						<h3 class="block-title">Tools <i style="display: none;" id="tool" class="fa fa-cog fa-spin"></i> </h3>

					</div>

					<div class="block-content">
					
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

										<div id="tools"></div>

                                    </form>

					</div>

				</div>

			</div>
			
			<div class="col-md-6">

				<div class="block">

					<div class="block-header bg-primary">

						<h3 class="block-title">Skype Tools <i style="display: none;" id="skype" class="fa fa-cog fa-spin"></i> </h3>

					</div>

					<div class="block-content">
					
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

				</div>

			</div>
			
			<div class="col-md-6">

				<div class="block">

					<div class="block-header bg-primary">

						<h3 class="block-title">Ping Tools <i style="display: none;" id="ping" class="fa fa-cog fa-spin"></i> </h3>

					</div>

					<div class="block-content">
					
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

				</div>

			</div>
			
			
			
			
			

		</div>     

	</div>

</main>
<?php



	include 'footer.php';



?>