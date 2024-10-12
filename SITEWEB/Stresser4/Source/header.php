<?php 

	$es_movil = '0';
		if(preg_match('/(android|wap|phone|ipad)/i',strtolower($_SERVER['HTTP_USER_AGENT']))){
			$es_movil++;
		}

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Access denied");

	ob_start();

	

	require_once 'inc/config.php';

	require_once 'inc/init.php';

	

	if (!(empty($maintaince))) {

		header('Location: maintenance.php');

		exit;

	}

	

	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){

		header('location: login.php');

		die();

	}

	

?>

<html class="no-focus">

	<head>

        <meta charset="utf-8">

        <title><?php echo htmlspecialchars($sitename); ?> | <?php echo $page; ?></title>

        <meta name="description" content="<?php echo htmlspecialchars($sitename); ?>">

        <meta name="robots" content="noindex, nofollow">
		
	   <meta name="viewport" content="width=device-width initial-scale=1.0 maximum-scale=1.0 user-scalable=yes" />

        <link rel="shortcut icon" href="assets/img/favicons/favicon.png">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">

        <link rel="stylesheet" href="assets/js/plugins/slick/slick.min.css">

        <link rel="stylesheet" href="assets/js/plugins/slick/slick-theme.min.css">

        <link rel="stylesheet" id="css-main" href="assets/css/oneui.css">

		<link rel="stylesheet" id="css-theme" href="assets/css/themes/<?php echo htmlentities($theme); ?>">

		<link rel="stylesheet" href="assets/js/plugins/highlightjs/github-gist.min.css">

		<link rel="stylesheet" href="assets/js/plugins/datatables/jquery.dataTables.min.css">

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

                            <a class="h5 text-white" href="home.php">

                                <i class="si si-<?php echo htmlentities($logo); ?> text-primary"></i> <span class="h4 font-w600 sidebar-mini-hide"><?php echo htmlspecialchars($sitename); ?></span>

                            </a>

                        </div>

                        <div class="side-content">

                            <ul class="nav-main">

                                <li>

                                    <a href="home.php" <?php if (basename($_SERVER['PHP_SELF']) == "home.php") { ?> class="active" <?php } ?>><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard</span></a>

                                </li>
								
								
								
						<?php if($user -> hasMembership($odb)) { ?>
                                <li class="nav-main-heading"><span class="sidebar-mini-hide">Stress</li>

                                <li>

                                    <a href="hub.php" <?php if (basename($_SERVER['PHP_SELF']) == "hub.php") { ?> class="active" <?php } ?> ><i class="si si-rocket"></i><span class="sidebar-mini-hide">Panel <?php if($hub_status == 0){ echo ' <span class="label label-warning">Disabled</span>'; } ?></span> </a>

                                </li>
								
								<?php if ($user -> isSupporter($odb)){ ?>

								 <li>

                                    <a href="496fckDimitri.php" <?php if (basename($_SERVER['PHP_SELF']) == "496fckDimitri.php") { ?> class="active" <?php } ?> ><i class="si si-rocket"></i><span class="sidebar-mini-hide">Admin Options</a>

                                </li>

								<?php } ?>
						
						<?php } ?>
						 <li class="nav-main-heading"><span class="sidebar-mini-hide">Help Center</span></li>
						 
                                <li>

                                    <a href="faq.php" <?php if (basename($_SERVER['PHP_SELF']) == "faq.php") { ?> class="active" <?php } ?>><i class="si si-question"></i><span class="sidebar-mini-hide">FAQ</span></a>

                                </li>

                                <li>

                                    <a href="support.php" <?php if (basename($_SERVER['PHP_SELF']) == "support.php") { ?> class="active" <?php } ?>><i class="si si-envelope"></i><span class="sidebar-mini-hide">Tickets</span></a>

                                </li>
								
						 <li class="nav-main-heading"><span class="sidebar-mini-hide">Pages</span></li>
						 
						 <li>

                                    <a href="tools.php" <?php if (basename($_SERVER['PHP_SELF']) == "tools.php") { ?> class="active" <?php } ?>><i class="si si-wrench"></i><span class="sidebar-mini-hide">Tools</span></a>

						</li>
						
						<li>

                                    <a href="dstat.php" <?php if (basename($_SERVER['PHP_SELF']) == "dstat.php") { ?> class="active" <?php } ?>><i class="si si-layers"></i><span class="sidebar-mini-hide">Power-Proof</span></a>

                                </li>

						 <li>

                                    <a href="servers.php" <?php if (basename($_SERVER['PHP_SELF']) == "servers.php") { ?> class="active" <?php } ?>><i class="si si-screen-desktop"></i><span class="sidebar-mini-hide">Servers</span></a>

						</li>
						
						<li>

                                    <a href="products.php" <?php if (basename($_SERVER['PHP_SELF']) == "products.php") { ?> class="active" <?php } ?>><i class="si si-credit-card"></i><span class="sidebar-mini-hide">Products</span></a>

						</li>			

								<?php if ($user -> isSupporter($odb)){ ?>

								<li>

                                    <a href="admin/home.php"><i class="si si-magic-wand"></i><span class="sidebar-mini-hide">Administrator</span></a>

                                </li>

								<?php } ?>

                                <li class="nav-main-heading"><span class="sidebar-mini-hide">Administrators</span></li>

								<?php 				

									$findAdmins = $odb->query("SELECT * FROM `users` WHERE `rank` = '1'");

									while($rowAdmins = $findAdmins->fetch(PDO::FETCH_BOTH)){

										$diffOnline = time() - $rowAdmins['activity'];

										$countOnline = $odb->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username  AND {$diffOnline} < 60");

										$countOnline->execute(array(':username' => $rowAdmins['username']));

										$onlineCount = $countOnline->fetchColumn(0);

										$logo = "fa fa-ban";

										if($onlineCount == "1") $logo = "fa fa-circle-o";

										echo '

											<li><a href="#"><i class="'. $logo .'"></i><span class="sidebar-mini-hide">'. $rowAdmins['username'] .'</span></a></li>';

									}

								?>

                            </ul>

                        </div>

                    </div>

                </div><div class="slimScrollBar" style="width: 5px; position: absolute; top: 100px; opacity: 0.35; display: none; border-radius: 7px; z-index: 99; right: 2px; height: 475px; background: rgb(255, 255, 255);"></div><div class="slimScrollRail" style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 1; z-index: 90; right: 2px; background: rgb(51, 51, 51);"></div></div>

            </nav>
		
            <header id="header-navbar" class="content-mini content-mini-full">

                <ul class="nav-header pull-right">
			<?php
				if($es_movil>0){
				} else {
				?>
				
				<?php
				}
				
				if($es_movil>0){
				?>
				<li>

                        <div class="btn-group">

                            <button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">               
Menu
                                <span class="caret"></span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                              

                                <li>

                                    <a tabindex="-1" href="home.php">

                                        <i class="si si-speedometer pull-right"></i>Dashboard</a>

                                </li>

						<?php if($user -> hasMembership($odb)) { ?>
								<li class="divider"></li>

                                <li class="dropdown-header">Stress</li>             

                                <li>

                                    <a tabindex="-1" href="hub.php">

                                        <i class="si si-rocket pull-right"></i>Panel

                                    </a>

                                </li>
								
						<?php } ?>
							
							
								<li class="divider"></li>

                                <li class="dropdown-header">Help Center</li>      

						 <li>

                                    <a tabindex="-1" href="faq.php">

                                        <i class="si si-question pull-right"></i>FAQ

                                    </a>

                                </li>
								
								 <li>

                                    <a tabindex="-1" href="tickets.php">

                                        <i class="si si-envelope pull-right"></i>Tickets

                                    </a>

                                </li>
								
									<li class="divider"></li>

                                <li class="dropdown-header">Pages</li>      
								
								<li>

                                    <a tabindex="-1" href="tools.php">

                                        <i class="si si-wrench pull-right"></i>Tools

                                    </a>

                                </li>
								
								<li>

                                    <a tabindex="-1" href="servers.php">

                                        <i class="si si-screen-desktop pull-right"></i>Servers

                                    </a>

                                </li>
								
									<li>

                                    <a tabindex="-1" href="products.php">

                                        <i class="si si-credit-card pull-right"></i>Products

                                    </a>

                                </li>
								
								<?php if ($user -> isSupporter($odb)){ ?>
								
								<li>

                                    <a tabindex="-1" href="admin/">

                                        <i class="si si-magic-wand pull-right"></i>Admin

                                    </a>

                                </li>
								
								<?php } ?>
								


                            </ul>

                        </div>

                    </li>
					
				<?php } else {} ?>
				
                    <li>

                        <div class="btn-group">

                            <button style="height: 34px;" class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">

                                <img src="assets/img/avatars/anon.jpg" alt="Avatar">

                                <span class="caret"></span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                                <li class="dropdown-header">Profile</li>

                                <li>

                                    <a tabindex="-1" href="support.php">

                                        <i class="si si-envelope-open pull-right"></i>Tickets</a>

                                </li>

                                <li>

                                    <a tabindex="-1" href="profile.php">

                                        <i class="si si-user pull-right"></i>Profile

                                    </a>

                                </li>  

								<li class="divider"></li>

                                <li class="dropdown-header">Actions</li>             

                                <li>

                                    <a tabindex="-1" href="logout.php">

                                        <i class="si si-logout pull-right"></i>Log out

                                    </a>

                                </li>
								
								<li>

                                    <a tabindex="-1" href="logout.php">

                                        <i class="si si-logout pull-right"></i>Close Session

                                    </a>

                                </li>

                            </ul>

                        </div>

                    </li>
                </ul>

				<script>

				window.setInterval(function(){

					var xmlhttp;

					if (window.XMLHttpRequest){

						xmlhttp = new XMLHttpRequest();

					}

					else{

						xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

					}

					xmlhttp.open("GET","ajax/user/general/status.php?username=<?php echo $_SESSION['username']; ?>",true);

					xmlhttp.send();

				}, 5000);

				</script>	

			</header>