<!DOCTYPE html>
<html lang="en">
	<head>


<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-7019508263308016",
    enable_page_level_ads: true
  });
</script>



    	<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="description" content="CleverAdmin - Bootstrap Admin Template">
		<meta name="author" content="Lukasz Holeczek">
		<meta name="keyword" content="CleverAdmin, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
	    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="assets/ico/favicon.png">

	    <title><?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['bootername'];
				}
			?> - <?php echo $page ?></title>

	    <!-- Bootstrap core CSS -->
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//cdn.rawgit.com/hilios/jQuery.countdown/2.0.4/dist/jquery.countdown.min.js"></script>
	    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- page css files -->
		<link href="assets/css/font-awesome.min.css" rel="stylesheet">
		<link href="assets/css/jquery-ui.min.css" rel="stylesheet">
		<link href="assets/css/fullcalendar.css" rel="stylesheet">
		<link href="assets/css/morris.css" rel="stylesheet">
		<link href="assets/css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
		<link href="assets/css/climacons-font.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="assets/css/style.min.css" rel="stylesheet">
	</head>
</head>
