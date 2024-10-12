<?php

$paginaname = 'Tools';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php 
			
			include("@/header.php");

			if (!($user->hasMembership($odb))) {
				header('location: index.php');
				die();
			}

			?>
				
					</script>
                    <div id="page-content">
					
                        <div class="row">
						<script>
						function skype()
						{
						var skypeName=$('#skypeName').val();
						document.getElementById("skypediv").style.display="none";
						document.getElementById("skypeimage").style.display="inline";
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
							document.getElementById("skypediv").innerHTML=xmlhttp.responseText;
							document.getElementById("skypeimage").style.display="none";
							document.getElementById("skypediv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/tools.php?type=skype",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("skypeName=" + skypeName);

						}

						function http()
						{
						var webName=$('#webName').val();
						document.getElementById("webdiv").style.display="none";
						document.getElementById("webimage").style.display="inline";
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
							document.getElementById("webdiv").innerHTML=xmlhttp.responseText;
							document.getElementById("webimage").style.display="none";
							document.getElementById("webdiv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/tools.php?type=http",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("webName=" + webName);

						}
						
						function geo()
						{
						var geoName=$('#geoName').val();
						document.getElementById("geodiv").style.display="none";
						document.getElementById("geoimage").style.display="inline";
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
							document.getElementById("geodiv").innerHTML=xmlhttp.responseText;
							document.getElementById("geoimage").style.display="none";
							document.getElementById("geodiv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/tools.php?type=geo",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("geoName=" + geoName);

						}
						function db()
						{
						var dbName=$('#dbName').val();
						document.getElementById("dbdiv").style.display="none";
						document.getElementById("dbimage").style.display="inline";
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
							document.getElementById("dbdiv").innerHTML=xmlhttp.responseText;
							document.getElementById("dbimage").style.display="none";
							document.getElementById("dbdiv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/tools.php?type=db",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("dbName=" + dbName);

						}
						</script>
<div class="col-xs-12">
<div class="alert alert-danger">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    Our Tools are under maintenance, some maybe not work at the moment. </strong>
</div>
	</div>						
						<div class="col-xs-4">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-skype"></i> <b>Skype Resolver <img src="img/jquery.easytree/loading.gif" id="skypeimage" style="display:none"/></b>
								</div>
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">Username</label>
									<div class="col-md-9">
									<input type="text" name="skypeName" id="skypeName" class="form-control" />
									</div>
									</div>
									<div class="form-group">
									<center>
									<div id="skypediv" style="display:none"></div>
									<br><br>
									</center>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="skype()" class="btn btn-effect-ripple btn-primary btn-block">Resolve</button>
									</div>
									</div>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-xs-4">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-laptop"></i> <b>HTTP Resolver <img src="img/jquery.easytree/loading.gif" id="webimage" style="display:none"/></b>
								</div>
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">Website</label>
									<div class="col-md-9">
									<input type="text" name="webName" id="webName" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<center>
									<div id="webdiv" style="display:none"></div>
									<br><br>
									</center>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="http()" class="btn btn-effect-ripple btn-primary btn-block">Resolve</button>
									</div>
									</div>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-xs-4">
 
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-pencil-square-o"></i> <b>IP Logger</b>
								</div>
								
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">Link</label>
									<div class="col-md-9">
									<input type="text" value="<?php echo $siteurl . 'news.php?id=' . $_SESSION['ID']; ?>" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Tiny Link</label>
									<div class="col-md-9">
									<input type="text" value="<?php echo get_tiny_url($siteurl . 'news.php?id=' . $_SESSION['ID']); ?>" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a href="#modal-compose" class="btn btn-effect-ripple btn-primary btn-block" data-toggle="modal">Logs</a>
									</div>
									</div>
									</div>
									
									
									<div id="modal-compose" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title"><strong>IP Logger</strong></h3>
									</div>
									<div class="modal-body">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<div class="col-xs-12">
									<table class="table table-striped">
									<form method="post">
									<tbody>
										<tr>
													<th><center>IP</center></th>
													<th><center>Date</center></th>
										</tr>
									<?php
									$SQLGetLogs = $odb->prepare("SELECT * FROM `iplogs` WHERE `userID` = :id ORDER BY `date` DESC LIMIT 5");
									$SQLGetLogs->execute(array(
										':id' => $_SESSION['ID']
									));
									while ($getInfo = $SQLGetLogs->fetch(PDO::FETCH_ASSOC)) {
										$id       = $getInfo['ID'];
										$loggedIP = $getInfo['logged'];
										$date     = date("m-d-Y, h:i:s a", $getInfo['date']);
										echo '<tr>
													<td><center><a>' . $loggedIP . '</a></center></td>
													<td><center>' . $date . '</center></td>
												</tr>';
									}
									if (empty($getInfo)) {

									}
									?>
									</form>
									</table>
									</div>
									</div>
									</div>
									</div>							
									</div>
									</div>
									</div>
								</div>							
							</div>
						</div>								
					</div>
					<div class="row">
					
						<div class="col-xs-8">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-child"></i> <b>GEO <img src="img/jquery.easytree/loading.gif" id="geoimage" style="display:none"/></b>
								</div>
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">IP</label>
									<div class="col-md-9">
									<input type="text" name="geoName" id="geoName" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label"></label>
									<div class="col-md-9">
									<div id="geodiv" style="display:none"></div>
									<br><br>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="geo()" class="btn btn-effect-ripple btn-primary btn-block">Look</button>
									</div>
									</div>
									</div>
								</div>
								
							</div>
						</div>		

						<div class="col-xs-4">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-database"></i> <b>SkypeDB <img src="img/jquery.easytree/loading.gif" id="dbimage" style="display:none"/></b>
								</div>
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-3 control-label">Username</label>
									<div class="col-md-9">
									<input type="text" name="dbName" id="dbName" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<center>
									<div id="dbdiv" style="display:none"></div>
									<br><br>
									</center>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="db()" class="btn btn-effect-ripple btn-primary btn-block">Search</button>
									</div>
									</div>
									</div>
								</div>
								
							</div>
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