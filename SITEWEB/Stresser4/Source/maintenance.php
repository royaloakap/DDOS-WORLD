<?php

	require_once 'inc/config.php';
	require_once 'inc/init.php';
	
	if (empty($maintaince)) {
		header('Location: home.php');
		exit;
	}

?>
<html class="no-focus">
	<head>
        <meta charset="utf-8">
        <title><?php echo htmlentities($sitename); ?> - 503</title>
		<meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">       
        <link rel="shortcut icon" href="assets/img/favicons/favicon.png">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" id="css-main" href="assets/css/oneui.css">   
		<link rel="stylesheet" id="css-theme" href="assets/css/themes/<?php echo htmlentities($theme); ?>">
    </head>
    <body> 
        <div class="content bg-white text-center pulldown overflow-hidden">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <h1 class="font-s128 font-w300 text-smooth animated rollIn">503</h1>
                    <h2 class="h3 font-w300 push-50 animated fadeInUp">We are sorry but our service is currently not available..</h2>
                </div>
            </div>
        </div>
        <div class="content pulldown text-muted text-center"><?php echo $maintaince; ?><br>
            <a class="link-effect" href="home.php">Retry to see if we're available</a>
        </div>     
        <script src="assets/js/core/jquery.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/core/jquery.slimscroll.min.js"></script>
        <script src="assets/js/core/jquery.scrollLock.min.js"></script>
        <script src="assets/js/core/jquery.appear.min.js"></script>
        <script src="assets/js/core/jquery.countTo.min.js"></script>
        <script src="assets/js/core/jquery.placeholder.min.js"></script>
        <script src="assets/js/core/js.cookie.min.js"></script>
        <script src="assets/js/app.js"></script>
    
</body></html>