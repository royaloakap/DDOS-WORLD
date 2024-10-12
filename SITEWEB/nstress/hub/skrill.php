<?php

$paginaname = 'Skrill';

	ob_start();
	session_start();
	require_once("@/config.php");
	require_once("@/init.php");
	
		if($_GET['id'] == "")
	{ 
		header("Location: index.php");
	} else {
			
	if(isset($_GET['id']) && Is_Numeric($_GET['id']) && $user -> LoggedIn())
	{
	$id = (int)$_GET['id'];
	$row = $odb -> query("SELECT * FROM `plans` WHERE `ID` = '$id'") -> fetch();
	if($row == "")
	{
	 header("Location: index.php");
	} else {

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
                                    <a class="active"><i class="fa fa-exclamation-triangle sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Purchasing Package</span></a>
                                </li>
                                <li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
						<li>
                                    <a href="purchase.php" ><i class="fa fa-shopping-cart sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Purchase</span></a>
                                </li>
								<li class="sidebar-separator">
                                    <i class="fa fa-ellipsis-h"></i>
                                </li>
								<li>
                                    <a href="tickets.php" ><i class="fa fa-envelope sidebar-nav-icon"></i><span class="sidebar-nav-mini-hide">Tickets</span></a>
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
                                
                               
                            </li>
                          
                        </ul>
						
						
						
                       
                    </header>
				
				<div id="page-content">
<div class="content-header">
<div class="row">
<div class="col-sm-6">
<div class="header-section">
<h1><?php echo $row['name']; ?></h1>
</div>
</div>
<div class="col-sm-6 hidden-xs">
<div class="header-section">
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-sm-10 col-sm-offset-1 col-lg-8 col-lg-offset-2">
<div class="block">
<div class="block-title">
<div class="block-options pull-right">
<a href="javascript:void(0)" class="btn btn-effect-ripple btn-default" onclick="App.pagePrint();"><i class="fa fa-print"></i> Print</a>
</div>
<h2><?php echo $row['name']; ?></h2>
</div>
<div class="table-responsive">
<table class="table table-striped table-hover table-bordered table-vcenter">
<thead>
<tr>
<th class="text-center"></th>
<th style="width: 50%;">Package</th>
<th class="text-center">Boot</th>
<th class="text-center">Length</th>
<th class="text-right">Amount</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-center"></td>
<td>
<h4><strong><?php echo $row['name']; ?></strong></h4>
</td>
<td class="text-center"><?php echo $row['mbt']; ?> seconds <br>and <br><?php echo $row['concurrents']; ?> concurrents</td>
<td class="text-center"><?php echo $row['length']; ?> <?php echo $row['unit']; ?></td>
<td class="text-right">$<?php echo $row['price']; ?></td>
</tr>
<tr>
<td colspan="4" class="text-right"><span class="h4"><strong>Total Due</strong></span></td>
<td class="text-right"><span class="h4"><strong>$<?php echo $row['price']; ?></strong></span></td>
</tr>
</tbody>
</table>
</div>
<div class="alert alert-success text-center">
<h3><strong>Send the money to simbamixyt@gmail.com</strong> <i class="fa fa-smile-o"></i></h3>
<p>If you bought it Open Ticket and put Skrill Transaction ID, Amount, Package Name and picture.</p>
</div>
</div>
</div>
</div>
</div>
		
<?php
	}
	}
	}
?>
			
                    <? // NO BORRAR LOS TRES DIVS! ?>
               </div>         
          </div>
	</div>

		<?php include("@/script.php"); ?>
    </body>
</html>