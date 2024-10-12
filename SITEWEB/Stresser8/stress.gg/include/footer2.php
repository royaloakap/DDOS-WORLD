	<app>
		<modal style="border-radius:15px" id="settingsmodal">
			<a onclick="settingsModal()"><i class="close"></i></a>
			<div class="settingsselect tabso">
				<span class="tab active" onclick="tab('accounting')" id="btnaccounting" tabbing="accounting"><i class="fas fa-user-circle"></i> Compte</span>
				<span class="tab" onclick="tab('packaging')" id="btnpackaging" tabbing="packaging"><i class="fas fa-box"></i> Plan</span>
			</div>
			<tab class="active" id="accounting" name="accounting">
				<label for="mypass"><i class="fad fa-key"></i> Changer le mot de passe</label>
				
				<fieldset class="material passchange">
					<input id="mypass" placeholder="" type="password" spellcheck="false" autocomplete="off" required="" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAUBJREFUOBGVVE2ORUAQLvIS4gwzEysHkHgnkMiEc4zEJXCMNwtWTmDh3UGcYoaFhZUFCzFVnu4wIaiE+vvq6+6qTgthGH6O4/jA7x1OiCAIPwj7CoLgSXDxSjEVzAt9k01CBKdWfsFf/2WNuEwc2YqigKZpK9glAlVVwTTNbQJZlnlCkiTAZnF/mePB2biRdhwHdF2HJEmgaRrwPA+qqoI4jle5/8XkXzrCFoHg+/5ICdpm13UTho7Q9/0WnsfwiL/ouHwHrJgQR8WEwVG+oXpMPaDAkdzvd7AsC8qyhCiKJjiRnCKwbRsMw9hcQ5zv9maSBeu6hjRNYRgGFuKaCNwjkjzPoSiK1d1gDDecQobOBwswzabD/D3Np7AHOIrvNpHmPI+Kc2RZBm3bcp8wuwSIot7QQ0PznoR6wYSK0Xb/AGVLcWwc7Ng3AAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
					<hr>
					<label>Nouveau mot de passe</label>
				</fieldset>
				<button class="confirmer" onclick="changePass()" id="updateacc"><i class="fad fa-user-edit"></i> Changer le mot de passe</button>
			</tab>
			<tab class="packageinfo" id="packaging" name="packaging">
				<div class="row"><div class="col">
				<h1><i class="fad fa-user-clock"></i> Durée d'attaque maximale</h1>
				<h2><?php echo $userInfo['secondes']; ?>s</h2>
				</div>
				<div class="col">
				<h1><i class="fad fa-plug"></i> Envois simultanés</h1>
				<h2><?php echo $userInfo['concurrents']; ?></h2>
				</div></div>
				<div class="row">
				<div class="col">
				<h1><i class="fad fa-network-wired"></i> Network</h1>
				<h2>
					<?php
						$color="success";
						if($userInfo['network'] == "basic"){ $color="warning"; }
						if($userInfo['network'] == "vip"){ $color="danger"; }
						echo "<span class='badge badge-".$color."'>".strtoupper($userInfo['network'])."</span>";						
					?>
				</h2>
				</div>
				<div class="col">
				<h1><i class="fad fa-hand-holding-water"></i> Date d'expiration</h1>
				<h2>
				<?php
					if($userInfo['endplan'] != 0){
						echo gmdate("d/m/Y \à H\hi", $userInfo['endplan']+7200);
					} else {
						echo "Jamais";
					}
				?>
				</h2>
				</div>
				</div>
			</tab>
		</modal>
		<style>
			.badge-danger {
				color: #fff;
				background-color: #e7493b7c;
			}
			.badge-warning {
				color: #fff;
				background-color: #f6c23e85;
			}
			.badge-success {
				color: #fff;
				background-color: #1cc8898c;
			}
			.badge {
				display: inline-block;
				padding: .25em .4em;
				font-size: 75%;
				font-weight: 700;
				line-height: 1;
				text-align: center;
				white-space: nowrap;
				vertical-align: baseline;
				border-radius: .35rem;
				transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
			}
		</style>
	</app>
	<script src="assets/js/site.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script>
		function changePass(){
			let pwd = document.getElementById("mypass");
			document.getElementById("imgloader").style.display = "flex";
			document.getElementById("imgloader").style.opacity = 1;
			if(pwd.value.length >= 8){				
				window.jQuery.post("function/changePassword.php", { password: pwd.value }, function(data){
					if(data == "success"){
						sb("Mot de passe modifié avec succès !")
				
						setTimeout(function(){
							document.getElementById("imgloader").style.opacity = 0;
							setTimeout(function(){
								document.getElementById("imgloader").style.display = "none";
							}, 500)
						}, 500)
						pwd.value="";
						document.location.reload(true);
					} else if(data == "too short"){
						sb("Mot de passe invalide !")
						error("Votre mot de passe doit faire au moins 8 caractères.")
						
						setTimeout(function(){
							document.getElementById("imgloader").style.opacity = 0;
							setTimeout(function(){
								document.getElementById("imgloader").style.display = "none";
							}, 500)
						}, 500)
					} else if(data == "not login"){
						sb("Une erreur est survenue !")
						error("Erreur d'authentification, reconnectez vous.")
						
						setTimeout(function(){
							document.getElementById("imgloader").style.opacity = 0;
							setTimeout(function(){
								document.getElementById("imgloader").style.display = "none";
							}, 500)
						}, 500)
					} else {
						console.log(data)
						sb("Une erreur est survenue !")
						
						setTimeout(function(){
							document.getElementById("imgloader").style.opacity = 0;
							setTimeout(function(){
								document.getElementById("imgloader").style.display = "none";
							}, 500)
						}, 500)
					}
				})				
			} else {				
				sb("Mot de passe invalide !")
				error("Votre mot de passe doit faire au moins 8 caractères.")
				
				setTimeout(function(){
					document.getElementById("imgloader").style.opacity = 0;
					setTimeout(function(){
						document.getElementById("imgloader").style.display = "none";
					}, 500)
				}, 500)
			}
		}
		
		function tab(id){
			let btnAccount=document.getElementById("btnaccounting")
			let btnPackage=document.getElementById("btnpackaging")
			
			let tabAccount=document.getElementById("accounting")
			let tabPackage=document.getElementById("packaging")
			if(id=="packaging"){
				tabAccount.className="packageinfo";
				tabPackage.className="packageinfo active";
				
				btnAccount.className="tab"
				btnPackage.className="tab active"
			} else {
				tabAccount.className="active";
				tabPackage.className="packageinfo";
				
				btnAccount.className="tab active"
				btnPackage.className="tab"
			}
		}
		
		function settingsModal(){
			let modal = document.getElementById("settingsmodal");
			if(modal.style.display == "none" || !modal.style.display){
				modal.style.display = "block";
			} else {
				modal.style.display = "none";
			}
		}
		
		setInterval(function() {
			window.jQuery.get( "function/online.php", function( data ) {
				//console.log(data)
			})
			window.jQuery.get( "function/getOnline.php", function( data ) {
				let online = document.getElementById("onlineusers");
				if(online != null){
					online.innerHTML = data;
				}
			})
		}, 1000)
	</script>
</body>
</html>