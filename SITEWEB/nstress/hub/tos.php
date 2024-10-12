<?php
ob_start();
require_once '@/config.php';
require_once '@/init.php';

$paginaname = 'TOS';

?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<head>
        <meta charset="utf-8">

        <title><?php echo htmlspecialchars($sitename); ?> - <?php echo $paginaname; ?></title>

        <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
        <meta name="author" content="StrikeREAD">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
        <link rel="shortcut icon" href="img/favicon.png">
        <link rel="apple-touch-icon" href="img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="img/icon180.png" sizes="180x180">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/plugins.css">
        <link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/themes/amethyst.css" id="theme-link">
        <link rel="stylesheet" href="css/themes.css">
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    </head>
    <body>
        <div id="page-wrapper" class="page-loading">
            <div class="preloader">
                <div class="inner">
                    <!-- Animation spinner for all modern browsers -->
                    <div class="preloader-spinner themed-background hidden-lt-ie10"></div>

                    <!-- Text for IE9 -->
                    <h3 class="text-primary visible-lt-ie10"><strong>Loading..</strong></h3>
                </div>
            </div>
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                <div id="sidebar-alt" tabindex="-1" aria-hidden="true">
                    <a href="javascript:void(0)" id="sidebar-alt-close" onclick="App.sidebar('toggle-sidebar-alt');"><i class="fa fa-times"></i></a>

                    <div id="sidebar-scroll-alt">
                        <!-- Sidebar Content -->
                        <div class="sidebar-content">
                            <!-- Profile -->
                         
                        </div>
                      
                    </div>
                   
                </div>
                
			
				
                <div id="sidebar">
                 
                    <div id="sidebar-brand" class="themed-background">
                        <a href="index.php" class="sidebar-title">
                            <i class="fa fa-terminal"></i> <span class="sidebar-nav-mini-hide"><strong><?php echo htmlspecialchars($sitename); ?></strong></span>
                        </a>
                    </div>
            

                 
                    <div id="sidebar-scroll">
                 
                        <div class="sidebar-content">
                    
                            <ul class="sidebar-nav">
                                <li>
                                    <a href="tos.php" class="active"><i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">TOS</span></a>
                                </li>
                                <li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
						<li>
                                    <a href="login.php" ><i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Login</span></a>
                                </li>
						<li>
                                    <a href="register.php" ><i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Register</span></a>
                                </li>
		
                            </ul>

                        </div>
                 
                    </div>
                 

               
                    <div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
                        <div class="text-center">
                            <small><?php echo htmlspecialchars($sitename); ?> v0.1</small><br>
                            <small><span id="year-copy"></span> &copy; <a href="<?php echo htmlspecialchars($siteurl); ?>" target="_blank"><?php echo htmlspecialchars($sitename); ?></a></small>
                        </div>
                    </div>
         
                </div>
				<div id="main-container">
				<header class="navbar navbar-inverse navbar-fixed-top">
                       
                        <ul class="nav navbar-nav-custom">
                          
                            <li>
                                <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');">
                                    <i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
                                    <i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>
                                </a>
                            </li>
                           

                      
                            <li class="hidden-xs animation-fadeInQuick">
                                <a href=""><strong><?php echo $paginaname; ?></strong></a>
                            </li>
                         
                        </ul>
               
                        <ul class="nav navbar-nav-custom pull-right">

                            <li class="dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="img/placeholders/avatars/avatar_custom.png" alt="avatar">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        <strong>Unknown</strong>
                                    </li>
                                    <li>
								
                                        <a href="login.php">
                                            <i class="fa fa-user fa-fw pull-right"></i>
                                            Login
                                        </a>
                                    </li>
							<li>
                                        <a href="register.php">
                                            <i class="fa fa-unlock fa-fw pull-right"></i>
                                          Register
                                        </a>
                                    </li>
                                </ul>
                            </li>
                          
                        </ul>
						
				
						
						
                       
                    </header>
				
<div id="page-content">
<div class="content-header">
<div class="header-section clearfix">
<a href="javascript:void(0)" class="pull-right">

</a>
<h1><?php echo htmlspecialchars($sitename); ?></h1>
<h2>TOS</h2>
</div>
</div>
<div class="row">
<div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
<div class="block">
<div class="block-title">
<div class="block-options pull-right">
<a href="javascript:void(0)" class="btn btn-effect-ripple btn-warning" data-toggle="tooltip" title="Add to favorites"><i class="fa fa-star"></i></a>
<a href="javascript:void(0)" class="btn btn-effect-ripple btn-danger" data-toggle="tooltip" title="Love it"><i class="fa fa-heart"></i></a>
</div>
<h2><i class="fa fa-rocket"></i> <?php echo htmlspecialchars($sitename); ?></h2>
</div>

<pre>
<font color="yellow">
1. All sales are final there is no refund.
2. Don't share your account or you will be banned.
3. By using this stresser you are responsible for what you are using it for. You have to stick with the laws from where you come from,remember you are responsible for your own actions and a reminder that post-stresser is a stress testing networking tool.
4. No money will be refunded.
5. All sales are final.
6. We are not responsible for your actions.
7. Do not abuse or could be banned.
8. No chargebacks.
9. Do not login using a VPN or Proxy, This isn't strict but may be seen as account sharing.
10. You cannot stress test any educational or government websites.
11. You cannot share your account with ANYONE. Each account is limited to 1 person per account.
12. DDoS services are strictly prohibited.
13. You cannot stress any servers that you don't have consent to stress.
14. This tool is for educational purposes only.
</font>
</pre>
<br> See full TOS: <a href="raw_tos.php">Click here</a><br>
<br>
<pre>
<font color="yellow">
1. Terms
By accessing this web site, you are agreeing to be bound by these Terms and Conditions of Use, all applicable laws and regulations; and agree that you are responsible for compliance with any applicable local, state, and federal laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained within this web site are protected by applicable copyright and trademark law.

2. Use License
"User" is defined as the registrant and owner/purchaser of the currently active account. Permission is granted to the user to stress test dedicated servers and networks that are either: 2a (a). Owned by the User (b). Owned by a party that explicitly consents to the testing performed by the User. The purpose of this service is to improve your firewalls and server security, not to violate Internet usage laws. This is a license given to you, and only you, the User; and anything you do while on Network Stresser is at your sole responsibility, and Network Stresser Interactive and the administration, ownership, and employees thereof, are in no way liable for any actions you perform. Under this license you may not:
Modify in any way or attempt to download any source code connected to https://nstress.com/;
or any pages, services, or tools therein.
Intentionally send a DDoS flood to an IP address not in compliance with Section 2a of "USE LICENSE" ;
Flood any servers connected with https://nstress.com/ or related hosts;
Remove or alter any copyright or other proprietary notations from the materials; or
Transfer the materials to another person/entity or "mirror" the materials on any other server;
Request refunds or attempt to dispute any payments to our service.
You are liable for what you do at https://nstress.com/, and if you break any of these terms; you may be banned without any refund or notice, and/or subjected to prosecution of applicable U.S Laws if necessary. In the event of a payment dispute, the account of the User will be immediately suspended and avenues to retain the payment will be taken. We reserve the right to publish any and all IP logs, Contact Information, and Stress Logs for all Users in violation of these Terms of Service.
3. Disclaimer
The materials on Network Stresser's web site are provided "as is". Network Stresser makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, Network Stresser does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet web site or otherwise relating to such materials or on any sites linked to this site.
4. Limitations
In no event shall Network Stresser or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on Network Stresser's Internet site, even if Network Stresser or a Network Stresser authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.

5. Revisions and Errata
The materials appearing on Network Stresser's web site could include technical, typographical, or photographic errors. Network Stresser does not warrant that any of the materials on its web site are accurate, complete, or current. Network Stresser may make changes to the materials contained on its web site at any time without notice. Network Stresser does not, however, make any commitment to update the materials.

6. Site Terms of Use Modifications
Network Stresser may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use.

7. Governing Law
Any claim relating to Network Stresser's web site shall be governed by the laws of the State of Illinois without regard to its conflict of law provisions.

General Terms and Conditions applicable to Use of a Web Site.

Privacy Policy
Your privacy is very important to us. Accordingly, we have developed this Policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.

Before or at the time of collecting personal information, we will identify the purposes for which information is being collected.
We will collect and use of personal information solely with the objective of fulfilling those purposes specified by us and for other compatible purposes, unless we obtain the consent of the individual concerned or as required by law.
We will only retain personal information as long as necessary for the fulfilment of those purposes.
We will collect personal information by lawful and fair means.
Personal data should be relevant to the purposes for which it is to be used, and, to the extent necessary for those purposes, should be accurate, complete, and up-to-date.
We will protect personal information by reasonable security safeguards against loss or theft, as well as unauthorized access, disclosure, copying, use or modification.
We will make readily available to customers information about our policies and practices relating to the management of personal information.
We are committed to conducting our business in accordance with these principles in order to ensure that the confidentiality of personal information is protected and maintained.

</font>
</pre>

<hr>

</div>
</div>
</div>
</div>
asd
</div>
                    <? // NO BORRAR LOS TRES DIVS! ?>
               </div>         
          </div>
	</div>

		<?php include("@/script.php"); ?>
    </body>
</html>