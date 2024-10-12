<?php
$p="auth";

include("../require.php");

include("../include/header.php");
?>
	<div class="container-fluid">
		<div class="authing">
			<h1>Bienvenue sur Stressing</h1>
			<small id="progress">Tapez un pseudonyme pour vous connecter ou vous créer un compte.</small><br>
			<div id="pseudo" style="opacity:1" class="fade">
				<input id="username" onkeypress="nextPress(event)" autocomplete="off" placeholder="Pseudonyme" type="text">
			</div>
			<div id="pwd" style="opacity:0" class="fade">
				<input id="password" onkeypress="nextPress(event)" autocomplete="off" placeholder="Mot de passe" type="password">
			</div>
			<button class="buttona" onclick="next()" id="next"><i class="fas fa-arrow-right fa-2x"></i></button>
		</div>
	</div>
	
	<script>
		function error(text){
			Swal.fire({
				position: 'top-end',
				icon: 'error',
				toast: true,
				html: text,
				showConfirmButton: false,
				timerProgressBar: true,
				timer: 3000
			})
		}
		
		document.onkeydown = function(evt) {
			evt = evt || window.event;
			if (evt.ctrlKey && evt.keyCode == 90) {
				alert("Ctrl-Z");
			}
		};
		
		function nextPress(e){
			if(e.charCode == 13){
				next();
			}			
		}
		
		function next(e){
			let user = document.getElementById("pseudo");
			let pwd = document.getElementById("pwd");
			let progress = document.getElementById("progress");
			
			let username = document.getElementById("username").value
			username = username.replace(' ', '');
			let password = document.getElementById("password").value
			
			if(username != null && user.style.opacity != 0){ //Si l'user n'est qu'au moment de taper son pseudo
				if(username != null && username != ""){
					$.post( "authentification.php", {"username": username},function( data ) {
						if(data == "yes"){ //Le compte existe
							user.style.opacity = 0
							pwd.style.opacity = 1
							progress.innerHTML = "Veuillez taper votre mot de passe pour vous connecter.";
						} else if(data == "nope"){ //Le compte n'existe pas
							user.style.opacity = 0
							pwd.style.opacity = 1
							progress.innerHTML = "Veuillez taper le futur mot de passe de votre compte.";
						} else { //Bah ça marche tout simplement pas ^^
							console.log(data)
							error("Une erreur est survenue")
						}
					});
				} else {
					error("Veuillez remplir le champ pseudonyme")
				}
			} else {
				if(username != null && username != ""){
					if(password != null && password != ""){
						if(progress.innerHTML.includes("Veuillez taper le futur mot de passe de votre compte")){ //On créé le compte
							console.log("create")
							if(password.length >= 8){
								$.post( "authentification.php", { 'action': 'register', 'username': username, 'password': password },function( data ) {
									if(data == "success"){
										Swal.fire({
											position: 'top-end',
											icon: 'success',
											toast: true,
											html: "Et voilà, vous êtes inscrit !",
											showConfirmButton: false,
											timerProgressBar: true,
											timer: 3000
										})
										setTimeout(function() {
											window.location.reload()
										}, 1000)
									} else if(data == "dupli"){
										error("Cette adresse IP est déjà inscrite.")
									} else {
										error("Mot de passe incorrect")
										console.log(data)
									}
								})
							} else {
								error("Votre mot de passe doit faire minimum 8 caractères");
							}
						} else { //On se connecte
							console.log("connect")
							$.post( "authentification.php", { 'action': 'login', 'username': username, 'password': password },function( data ) {
								if(data == "success"){
									Swal.fire({
										position: 'top-end',
										icon: 'success',
										toast: true,
										html: "Et voilà, vous êtes connecté !",
										showConfirmButton: false,
										timerProgressBar: true,
										timer: 3000
									})
									setTimeout(function() {
										window.location.reload()
									}, 1000)
								} else if(data == "suspend"){
									error("Votre compte est suspendu.")
								} else if(data == "ban"){
									error("Votre compte est bannis.")
								} else if(data == "invalid"){
									error("Mot de passe invalide")
								} else {
									error("Une erreur est survenue");
									console.log(data)
								}
							})
						}
					} else {
						error("Veuillez remplir le champ mot de passe")
					}
				} else {
					user.style.opacity = 1
					pwd.style.opacity = 0
					progress.innerHTML = "Tapez un pseudonyme pour vous connecter ou vous créer un compte.";
				}
			}
		}
	
	</script>

<?php include("../include/footer.php"); ?>