<?php

$paginaname = 'Support';


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
					
                       
				
<div class="col-xs-12">
<div class="alert alert-info">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    Use our Online Support to Buy / Questions. </strong>
</div>
	</div>						
						<div class="col-xs-4">
					
                     <? // NO BORRAR LOS TRES DIVS! ?>
               </div>    
<div class="alert alert-danger">
  <strong><i class="fa fa-info-circle" aria-hidden="true"></i>    If our Support if Offline and you bought a plan please take the form and we will enable your plan. </strong>
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