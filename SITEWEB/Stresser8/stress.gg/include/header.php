<html>
	<head>
		<title>Stressing - Meilleur stresseur du web 2020. Bon marché et percutant !</title>
		<link rel="shortcut icon" href="https://imgur.com/EBR0Z4p.png"/>
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="/assets/css/style.css">
		
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-Bfad6CLCknfcloXFOyFnlgtENryhrpZCe29RTifKEixXQZ38WheV+i/6YWSzkz3V" crossorigin="anonymous">
		<script src="https://raw.githubusercontent.com/juliangarnier/anime/master/lib/anime.min.js"></script>
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	</head>
	<body>
	<nav class="navbar navbar-expand-lg">
		<a class="navbar-brand" href="#">
			<img src="https://imgur.com/EBR0Z4p.png" alt="Stressing">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

        <?php
            if(isset($_SESSION['id'])){
        ?>
		<div class="collapse navbar-collapse mr-5" id="navbarSupportedContent">
			<ul class="navbar-nav ml-auto">
				<li class="nav-item <?php if($p=="hub") { echo "active"; } ?>">
					<a class="nav-link" href="hub">Hub</span></a>
				</li>
				<li class="nav-item <?php if($p=="ticket") { echo "active"; } ?>">
					<a class="nav-link" href="ticket">Ticket</a>
				</li>
				<li class="nav-item <?php if($p=="doc") { echo "active"; } ?>">
					<a class="nav-link" href="doc">Doc</a>
				</li>
				<li class="nav-item <?php if($p=="plans") { echo "active"; } ?>">
					<a class="nav-link" href="plans">Plans</a>
				</li>
				<li class="nav-item <?php if($p=="boutique") { echo "active"; } ?>">
					<a class="nav-link" href="boutique">Boutique</a>
				</li>
				<div class="nav-profil">
					<div class="row dropdown">
						<a class="nav-link dropdown-toggle" <!--href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"!-->>
							<div class="col-sm-12 col-md-6">
								<img class="rounded-circle" src="https://wired.me/wp-content/uploads/2019/09/DDoS-Hacking.jpg">
							</div>
							<div class="col-sm-12 col-md-6">
								<h5><?php echo $_SESSION['username']; ?></h5>
							</div>
							<!--<div class="dropdown-menu" aria-labelledby="userDropdown">
								<a class="dropdown-item" href="#">Action</a>
								<a class="dropdown-item" href="#">Another action</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#">Something else here</a>
							</div>!-->
						</a>
					</div>
				</div>
		<?php } ?>
			</ul>
		</div>
	</nav>
<?php

if(isset($err) && !empty($err)){
	echo "
	<script>
		Swal.fire({
			position: 'top-end',
			icon: 'error',
			toast: true,
			html: '".addslashes($err)."',
			showConfirmButton: false,
			timerProgressBar: true,
			timer: 5000
		})
	</script>
	";
}

if(isset($succ) && !empty($err)){
	echo "
	<script>
		Swal.fire({
			position: 'top-end',
			icon: 'success',
			toast: true,
			html: '".addslashes($err)."',
			showConfirmButton: false,
			timerProgressBar: true,
			timer: 5000
		})
	</script>
	";
}

?>