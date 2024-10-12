

<?php 



	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Not Allowed Here");

	ob_start();

	

	require_once '../inc/config.php';

	require_once '../inc/init.php';

	

	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){

		header('Location: ../relogin.php');

		die();

	} 

	

	if (!($user -> isSupporter($odb))){

		header('location: ../home.php');

		die();

	}

	

?>

<html class="no-focus">

	<head>

        <meta charset="utf-8">

        <title><?php echo htmlspecialchars($sitename) . " - " . $_SESSION['username']?></title>

        <meta name="description" content="<?php echo htmlspecialchars($sitename); ?>">

        <meta name="robots" content="noindex, nofollow">

        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

        <link rel="shortcut icon" href="../assets/img/favicons/favicon.png">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">

        <link rel="stylesheet" href="../assets/js/plugins/slick/slick.min.css">

        <link rel="stylesheet" href="../assets/js/plugins/slick/slick-theme.min.css">

        <link rel="stylesheet" id="css-main" href="../assets/css/oneui.css">

		<link rel="stylesheet" id="css-theme" href="../assets/css/themes/<?php echo htmlentities($theme); ?>">

		<link rel="stylesheet" href="../assets/js/plugins/datatables/jquery.dataTables.min.css">

    </head>

    <body class="">

        <div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed side-overlay-o">

            <nav id="sidebar">

                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 475px;"><div id="sidebar-scroll" style="overflow: hidden; width: auto; height: 475px;">

                    <div class="sidebar-content">

                        <div class="side-header side-content bg-white-op">

                            <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">

                                <i class="fa fa-times"></i>

                            </button>

                            <a class="h5 text-white" href="/panel/home.php">

                                <i class="si si-<?php echo htmlentities($logo); ?> text-primary"></i> <span class="h4 font-w600 sidebar-mini-hide">Yoursite.com</span>

                            </a>

                        </div>

                        <div class="side-content">

                            <ul class="nav-main">

								<li>

                                    <a href="home.php"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard</span></a>

                                </li>

								<?php

								if($user->isAdmin($odb)){

								?>

                                <li class="nav-main-heading"><span class="sidebar-mini-hide">Settings</span></li>

                                <li>

                                    <a href="general.php"><i class="si si-globe"></i><span class="sidebar-mini-hide">General</span></a>

                                </li>

                                <li>

                                    <a href="hub.php"><i class="si si-rocket"></i><span class="sidebar-mini-hide">Hub</span></a>

                                </li>

                                <li>

                                    <a href="plans.php"><i class="si si-basket"></i><span class="sidebar-mini-hide">Plans</span></a>

                                </li>

								<li>

                                    <a href="news.php"><i class="si si-envelope"></i><span class="sidebar-mini-hide">News</span></a>

                                </li>

								<li>

                                    <a href="faq.php"><i class="si si-question"></i><span class="sidebar-mini-hide">FAQ</span></a>

                                </li>
								
								
						<li>

                                    <a href="support.php"><i class="si si-envelope"></i><span class="sidebar-mini-hide">Tickets</span></a>

                                </li>
                                
                                	<li>

                                    <a href="../index.php"><i class="si si-back"></i><span class="sidebar-mini-hide">Return</span></a>

                                </li>


                                <li class="nav-main-heading"><span class="sidebar-mini-hide">User Management</span></li>

                                <li>

                                    <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-user"></i><span class="sidebar-mini-hide">Users</span></a>

                                    <ul>

										<li>

											<a href="users.php"><span class="sidebar-mini-hide">All Users</span></a>

										</li>

										<li>

											<a href="attacks.php"><span class="sidebar-mini-hide">Running Attacks</span></a>

										</li>

									</ul>

								</li>

								<li>

                                    <a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-feed"></i><span class="sidebar-mini-hide">Logs</span></a>

                                    <ul>

                                        <li>

                                            <a href="payments.php">Payments</a>

                                        </li>

                                        <li>

                                            <a href="logins.php">Logins</a>

                                        </li>

                                        <li>

                                            <a href="attacklogs.php">Attacks</a>

                                        </li>

                                        <li>

                                            <a href="userlogs.php">Action Logs</a>

                                        </li>

                                        <li>

                                            <a href="reports.php">Reports</a>

                                        </li>

                                    </ul>

								</li>

								<?php

								}

								?>

                            </ul>

                        </div>

                    </div>

                </div><div class="slimScrollBar" style="width: 5px; position: absolute; top: 100px; opacity: 0.35; display: none; border-radius: 7px; z-index: 99; right: 2px; height: 475px; background: rgb(255, 255, 255);"></div><div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 1; z-index: 90; right: 2px; background: rgb(51, 51, 51);"></div></div>

            </nav>

            <header id="header-navbar" class="content-mini content-mini-full">

                <ul class="nav-header pull-right">
				<li>

                        <div class="btn-group">

                            <button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">               
Menu
                                <span class="caret"></span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                             
								<?php if ($user -> isSupporter($odb)){ ?>
								<li>
									 <a tabindex="-1" href="home.php">

                                       Dashboard

                                    </a>

                                </li>
								
								<li>
									 <a tabindex="-1" href="general.php">

                                     General
                                    </a>

                                </li> 
								
								<li>

                                    <a tabindex="-1" href="hub.php">
Hub

                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="plans.php">
Plans

                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="news.php">
News

                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="faq.php">
FAQ

                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="support.php">
Tickets

                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="users.php">
All Users
                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="attacks.php">
Running Attacks
                                    </a>

                                </li>
						
						<li>

                                    <a tabindex="-1" href="payments.php">
Payments
                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="logins.php">
Login Logs
                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="attacklogs.php">
Attacks Logs
                                    </a>

                                </li>
								
						<li>

                                    <a tabindex="-1" href="userlogs.php">
Action Logs
                                    </a>

                                </li>
								
								<?php } ?>
								


                            </ul>

                        </div>

                    </li>
					
                    <li>

                        <div class="btn-group">

                            <button style="height: 34px;" class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">

                                <img src="../assets/img/avatars/anon.jpg" alt="Avatar">

                                <span class="caret"></span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                                <li class="dropdown-header">Profile</li>

                                <li>

                                    <a tabindex="-1" href="../support.php">

                                        <i class="si si-envelope-open pull-right"></i>Tickets</a>

                                </li>

                                <li>

                                    <a tabindex="-1" href="../profile.php">

                                        <i class="si si-user pull-right"></i>Profile

                                    </a>

                                </li>                         

                                <li class="divider"></li>

                                <li class="dropdown-header">Actions</li>             

                                <li>

                                    <a tabindex="-1" href="../home.php">

                                        <i class="si si-action-undo pull-right"></i>Return

                                    </a>

                                </li>

								<li>

                                    <a tabindex="-1" href="../logout.php">

                                        <i class="si si-logout pull-right"></i>Log out

                                    </a>

                                </li>

                            </ul>

                        </div>

                    </li>

                </ul>

			</header>

			<script>

				window.setInterval(function(){

					var xmlhttp;

					if (window.XMLHttpRequest){

						xmlhttp = new XMLHttpRequest();

					}

					else{

						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

					}

					xmlhttp.open("GET","../ajax/user/general/status.php?username=<?php echo $_SESSION['username']; ?>",true);

					xmlhttp.send();

				}, 5000);

			</script>