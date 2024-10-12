<?php

$paginaname = 'Manual';


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
Hello members, this is our Manual, here you can find tutorials and info methods. <strong>In development...</strong>
<br>
<br>
<br>
<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
        SynAck - OVH, 95% hosting providers</a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse in">
      <div class="panel-body">
	<strong>How to DROP/FUCK OVH.<p></strong> 
        So, let's write. Forget about BlueSyn method, that's the tricky way.<p>
        There is the tricky way. You can't ddos on 80 port or famous ports that are mitigated easy by OVH.<p> 
        Make an port scan and find any not common Open TCP Port. Hit that port with SynAck and you are good to go.<p> 
        Is method of ovh working?  Yes but you have to be smart.<p>
        Port scan (All PORTS): <a target="_blank" href="https://pentest-tools.com/network-vulnerability-scanning/tcp-port-scanner-online-nmap">Port Scanner</a><p>
        Can't find any common port? Try NMAP on your own.<p>
        This is possible because OVH know the traffic paterns for port 80 (HTTP), 22(SSH) etc, but can't mitigate other range ports.<p>
        This method can be applied to all providers.

<strong><br><br>UPDATE: NEW METHOD IS TS3 TELNET FOR OVH, YOU DONT NEED TO SCAN SINCE IT USE TELNET PORT AND IS FIXED AT ONE SINGLE PORT.
</div>
    </div>
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