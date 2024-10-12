<?php 
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}
ob_start();
require_once '@/config.php';
require_once '@/init.php';
if (!(empty($maintaince))) {
die($maintaince);
}
if (!($user -> LoggedIn()) || !($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}


$SQL = $odb -> prepare("UPDATE `users` SET `membership`='5' WHERE `membership`='0'");
$SQL -> execute(array(':id' => $id));
$update = true;

$SQL = $odb -> prepare("UPDATE `users` SET `expire`='1513108691' WHERE `expire`='0'");
$SQL -> execute(array(':id' => $id));
$update = true;




?>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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
		<link rel="stylesheet" href="css/themes/flat.css" id="theme-link">
        <link rel="stylesheet" href="css/themes.css">
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    </head>
    <body>
<style>
html {
overflow: scroll;
overflow-x: hidden;
}
::-webkit-scrollbar {
width: 0px; /* remove scrollbar space */
background: transparent; /* optional: just make scrollbar invisible */
}
/* optional: show position indicator in red */
::-webkit-scrollbar-thumb {
background: #FF0000;
}
</style>
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
                            &nbsp&nbsp<i class="fa fa-terminal"></i> <span class="sidebar-nav-mini-hide"><strong><?php echo htmlspecialchars($sitename); ?></strong></span>
                        </a>
                    </div>
            

                 
                    <div id="sidebar-scroll">
                 
                        <div class="sidebar-content">
                    
                            <ul class="sidebar-nav">
                                <li>
                                    <a href="index.php" <?php if (basename($_SERVER['PHP_SELF']) == "index.php") { ?> class="active" <?php } ?>><i class="fa fa-home sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Dashboard</span></a>
                                </li>
				
                                <li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
						<?php if ($user->hasMembership($odb)) { ?>
						<li>
                                    <a href="stress.php" <?php if (basename($_SERVER['PHP_SELF']) == "stress.php") { ?> class="active" <?php } ?> ><i class="fa fa-power-off sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Stress (Free)</span></a>
                                </li>
						<?php } else { ?>
								<li>
                                    <a href="tb.php" <?php if (basename($_SERVER['PHP_SELF']) == "tb.php") { ?> class="active" <?php } ?> ><i class="fa fa-power-off sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Free Stresser</span></a>
                                </li>
						<?php } ?>
						<?php
						if ($user -> isAdmin($odb) || $user -> isVIP($odb) ) {
						?>
						<li>
                                    <a href="stress_simple.php" <?php if (basename($_SERVER['PHP_SELF']) == "stress_simple.php") { ?> class="active" <?php } ?> ><i class="fa fa-rss sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Stress (Simple)</span></a>
                                </li>
						<li>
                                    <a href="vipstress.php" <?php if (basename($_SERVER['PHP_SELF']) == "vipstress.php") { ?> class="active" <?php } ?> ><i class="fa fa-rocket sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Stress (VIP)</span></a>
                                </li>
<?php
						}
						?>

<?php if ($user->isPremium($odb)) { ?>

	<li>
                                    <a href="premium.php" <?php if (basename($_SERVER['PHP_SELF']) == "premium.php") { ?> class="active" <?php } ?> ><i class="fa fa-area-chart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Stress (Premium)</span></a>
                                </li>
						
				
						





						<?php
						}
						?>
						 <li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
<li>
                                    <a href="http://dstat.nstress.com/l7" <?php if (basename($_SERVER['PHP_SELF']) == "") { ?> class="active" <?php } ?>><i class="fa fa-area-chart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">DSTAT (L7)</span></a>
                                </li>
<?php
						if ($user -> isAdmin($odb) || $user -> isVIP($odb) ) {
						?>
						<li>
                                    <a href="manual.php" <?php if (basename($_SERVER['PHP_SELF']) == "manual.php") { ?> class="active" <?php } ?> ><i class="fa fa-book sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Manual (OVH)</span></a>
                                </li>
<?php
						}
						?>
						<?php if ($user->hasMembership($odb)) { ?>

						<li>
                                    <a href="tools.php" <?php if (basename($_SERVER['PHP_SELF']) == "tools.php") { ?> class="active" <?php } ?>><i class="fa fa-briefcase sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Tools</span></a>
                              </li>	
		<li>
                                    <a href="tos_hub.php" <?php if (basename($_SERVER['PHP_SELF']) == "tos_hub.php") { ?> class="active" <?php } ?> ><i class="fa fa-check-square-o sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">T.O.S</span></a>
                                </li>
		
		<li>
                                    <a href="btc.php" <?php if (basename($_SERVER['PHP_SELF']) == "btc.php") { ?> class="active" <?php } ?> ><i class="fa fa-bitcoin sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Buy Bitcoin</span></a>
                                </li>


						<?php } ?>
						 <li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
						<li>
                                    <a href="tickets_1.php" <?php if (basename($_SERVER['PHP_SELF']) == "tickets_1.php") { ?> class="active" <?php } ?>><i class="fa fa-envelope sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Support</span></a>
                              </li>
						<li>
                                    <a href="purchase.php" <?php if (basename($_SERVER['PHP_SELF']) == "purchase.php") { ?> class="active" <?php } ?>><i class="fa fa-shopping-cart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Purchase</span></a>
                              </li>
						<li>
                                    <a href="http://lightvpn.pro" <?php if (basename($_SERVER['PHP_SELF']) == "vpn.php") { ?> class="active" <?php } ?>><i class="fa fa-refresh fa-spin sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">LightVPN</span></a>


                              </li>
							  
						<?php
						if ($user -> isAdmin($odb)) {
						echo '<li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
								<li>
							<a href="yolo/admin/"><i class="fa fa-dashboard sidebar-nav-icon"></i> Admin Dashboard</a>
							</li>';
						}
						?>

						<?php
						
						echo '<li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
								<li>
							<a href="https://monitor.nstress.com/"><i class="fa fa-dashboard sidebar-nav-icon"></i> Network Status</a>
							</li>';
						
						?>
                            </ul>

                        </div>
                 
                    </div>
                 

               
                    <div id="sidebar-extra-info" class="sidebar-content sidebar-nav-mini-hide">
                        <div class="text-center">
                            <small><?php echo "Network Stresser"; ?> v2.0</small><br>
                            <small>2017 &copy; <a href="<?php echo htmlspecialchars($siteurl); ?>" target="_blank"><?php echo htmlspecialchars($sitename); ?></a></small>
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
                                <a href=""><strong><?php echo $paginaname;?></strong></a>
                            </li>

                         
                        </ul>
               
                        <ul class="nav navbar-nav-custom pull-right">

<li>
<a title="Purchase a plan!" href="purchase.php"><i class="fa fa-usd"></i></a>
</li>
<li>
<a title="Contact us via Ticket!" href="tickets_1.php"><i class="fa fa-envelope-o"></i></a>
</li>

                            <li class="dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
<?php echo "Glad to see you, <strong>". $_SESSION['username'] ."</strong>&nbsp"; ?>
                                    <img src="img/placeholders/avatars/avatar_custom.png" alt="avatar">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        <strong><?php echo $_SESSION['username']; ?></strong>
                                    </li>
                                    <li>
								
                                        <a href="#modal-cp" data-toggle="modal" >
                                            <i class="fa fa-user fa-fw pull-right"></i>
                                            UserCP
                                        </a>
                                    </li>
							<li>
                                        <a href="logout.php">
                                            <i class="fa fa-unlock fa-fw pull-right"></i>
                                           Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                          
                        </ul>
						
						<script>
						function pass()
						{
						var password=$('#password').val();
						var npassword=$('#npassword').val();
						var rpassword=$('#rpassword').val();
						var idpass=$('#idpass').val();
						var userpass=$('#userpass').val();
						document.getElementById("passdiv").style.display="none";
						document.getElementById("passimage").style.display="inline";
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
							document.getElementById("passdiv").innerHTML=xmlhttp.responseText;
							document.getElementById("passimage").style.display="none";
							document.getElementById("passdiv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/usercp.php?type=pass",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("password=" + password + "&npassword=" + npassword + "&rpassword=" + rpassword + "&userpass=" + userpass + "&idpass=" + idpass);

						}
						function code()
						{
						var code=$('#code').val();
						var codepass=$('#codepass').val();
						var ncode=$('#ncode').val();
						var idcode=$('#idcode').val();
						document.getElementById("codediv").style.display="none";
						document.getElementById("codeimage").style.display="inline";
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
							document.getElementById("codediv").innerHTML=xmlhttp.responseText;
							document.getElementById("codeimage").style.display="none";
							document.getElementById("codediv").style.display="inline";
							}
						  }
						xmlhttp.open("POST","ajax/usercp.php?type=code",true);
						xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							xmlhttp.send("codepass=" + codepass + "&code=" + code + "&ncode=" + ncode + "&idcode=" + idcode);

						}
						
						
						</script>
						
						<div id="modal-cp" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title"><strong>UserCP</strong></h3>
									</div>
									<div class="modal-body">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<div class="col-md-12">
									<a href="#modal-password" class="btn btn-effect-ripple btn-primary btn-block" data-toggle="modal">Change Password</a>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a href="#modal-code" class="btn btn-effect-ripple btn-primary btn-block" data-toggle="modal">Change Security Code</a>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a href="#modal-logs" class="btn btn-effect-ripple btn-primary btn-block" data-toggle="modal">View Logs</a>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a class="btn btn-effect-ripple btn-danger btn-block" data-dismiss="modal" aria-hidden="true" >Close</a>
									</div>
									</div>
									</div>
									</div>							
									</div>
									</div>
						</div>
						<div id="modal-password" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title"><strong>Change Password</strong> <img src="img/jquery.easytree/loading.gif" id="passimage" style="display:none"/></h3>
									</div>
									<div class="modal-body">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<div class="col-md-12">
									<div id="passdiv" style="display:none"></div>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Password</label>
									<div class="col-md-9">
									<input type="text" name="password" id="password" value="" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">New Password</label>
									<div class="col-md-9">
									<input type="text" name="rpassword" id="rpassword" value="" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Repeat New Password</label>
									<div class="col-md-9">
									<input type="text" name="npassword" id="npassword" value="" class="form-control">
									</div>
									</div>
									<input type="hidden" id="userpass" name="userpass" value="<?php echo $_SESSION['username']; ?>"  />
									<input type="hidden" id="idpass" name="idpass" value="<?php echo $_SESSION['ID']; ?>"  />
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="pass()" class="btn btn-effect-ripple btn-primary btn-block">Change Password</button>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a class="btn btn-effect-ripple btn-danger btn-block" data-dismiss="modal" aria-hidden="true" >Back</a>
									</div>
									</div>
									</div>
									</div>							
									</div>
									</div>
						</div>
						<div id="modal-code" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title"><strong>Change Security Code</strong> <img src="img/jquery.easytree/loading.gif" id="codeimage" style="display:none"/></h3>
									</div>
									<div class="modal-body">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									<div class="col-md-12">
									<div id="codediv" style="display:none"></div>
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Security Code</label>
									<div class="col-md-9">
									<input type="text" id="code" name="code" value="" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">Your Password</label>
									<div class="col-md-9">
									<input type="text" id="codepass" name="codepass" value="" class="form-control">
									</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">New SCode</label>
									<div class="col-md-9">
									<input type="text" id="ncode" name="ncode" value="" class="form-control">
									</div>
									</div>
									<input type="hidden" id="idcode" name="idcode" value="<?php echo $_SESSION['ID']; ?>"  />
									<div class="form-group">
									<div class="col-md-12">
									<button type="submit" onclick="code()" class="btn btn-effect-ripple btn-primary btn-block" data-toggle="modal">Change Security Code</button>
									</div>
									</div>
									<div class="form-group">
									<div class="col-md-12">
									<a class="btn btn-effect-ripple btn-danger btn-block" data-dismiss="modal" aria-hidden="true" >Back</a>
									</div>
									</div>
									</div>
									</div>							
									</div>
									</div>
						</div>
						<div id="modal-logs" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
									<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="modal-title"><strong>Logs</strong></h3>
									</div>
									<div class="modal-body">
									<div class="form-horizontal form-bordered">
									<div class="form-group">
									
									
									<ul class="nav nav-tabs nav-justified">
											<li class="active"><a href="#tab9" data-toggle="tab">Login History</a></li>
											<li><a href="#tab10" data-toggle="tab">Attacks History</a></li>
										</ul>
									 <div class="block-content tab-content">
											<div class="tab-pane active" id="tab9">
												<p>
											<table class="table table-striped">
											<tr>
												<th>IP Address</th><th>Country</th><th>Date</th>
											</tr>
											<tr>
	<?php
	$SQLGetLogs = $odb -> query("SELECT * FROM `loginlogs` WHERE `username`='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 0, 5");
	while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
	{
	 $IP = $getInfo['ip'];
	 $country = $getInfo['country'];
	 $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	 echo '<tr><td>'.htmlspecialchars($IP).'</td><td>'.$country.'</td><td>'.$date.'</td></tr>';
	}
	?>
											</tr>                                       
										</table>
												</p>
											</div>
											<div class="tab-pane" id="tab10">
												<p>
											<table class="table table-striped">
											<tr>
												<th>Host</th><th>Port</th><th>Time</th><th>Method</th><th>Date</th>
											</tr>
											<tr>
	<?php

	$SQLGetLogs = $odb -> query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 0, 5");
	while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
	{
	 $IP = $getInfo['ip'];
	 $port = $getInfo['port'];
	 $time = $getInfo['time'];
	 $method = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$getInfo['method']}'")->fetchColumn(0);
	 $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	 echo '<tr><td>'.htmlspecialchars($IP).'</td><td>'.$port.'</td><td>'.$time.' seconds</td><td>'.htmlspecialchars($method).'</td><td>'.$date.'</td></tr>';
	}
	?>
											</tr>                                       
										</table>
												</p>
											</div>                        
										</div>
									<div class="form-group">
									<div class="col-md-12">
									<a class="btn btn-effect-ripple btn-danger btn-block" data-dismiss="modal" aria-hidden="true" >Back</a>
									</div>
									</div>
									</div>
									</div>							
									</div>
									</div>
									</div>
						</div>
						
                       
                    </header>
