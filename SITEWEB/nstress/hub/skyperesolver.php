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
<div class="alert alert-success">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    The best Skype Resolver out there working in 2017. </strong>
</div>
	</div>						
						<div class="col-xs-12">
							<div class="widget">
								<div class="widget-content widget-content-mini themed-background-dark text-light-op">
								<i class="fa fa-skype"></i> <b>Skype Resolver <img src="img/jquery.easytree/loading.gif" id="skypeimage" style="display:none"/></b>
								</div>
								<div class="widget-content">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<label class="col-md-1 control-label">Username</label>
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