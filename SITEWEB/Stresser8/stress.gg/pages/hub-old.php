<?php
//Variables de la page
$p="hub";
$pageTitle="Hub";
//End
require_once("../require.php");



include("../include/header.php");
?>

<div class="container-fluid mt-3">
	<div class="row">
		<div class="col-sm-12 col-md-4">
			<div class="card shadow bg-transparent">
				<div class="card-header bg-stressing">
					<h5>Toutes les attaques en cours</h5>
				</div>
				<div class="card-body bg-secondary">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Cible</th>
								<th scope="col">Layer</th>
								<th scope="col">Durée</th>
								<th scope="col">Date</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>https://cybe...</th>
								<td>Layer 7</td>
								<td>150s</td>
								<td>15h20</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-12 col-md-4">
			<div class="card shadow bg-transparent">
				<div class="card-header bg-transparent">
					<div class="row">
						<div class="col-6 text-center">
							<a id="layer7-btn" class="hub-btn active" onclick="select('layer7')">
								Layer 7
							</a>
						</div>
						<div class="col-6 text-center">
							<a id="layer4-btn" class="hub-btn" onclick="select('layer4')">
								Layer 4
							</a>
						</div>						
					</div>
				</div>
				<div class="card-body bg-stressing">
					<div id="layer7-content" class="layer-content" style="display:block">
						
						<div class="form-group">
							<label>URL</label>
							<input class="form-control" type="text" id="targetL7" placeholder="https://google.com">
						</div>
						<div class="form-group">
							<label>Durée</label>
							<input class="form-control" type="number" id="dureeL7" placeholder="30 (en secondes)">
						</div>
						<div class="form-group">
							<label>Methode</label>
							<select class="custom-select" id="methodeL7">
								<optgroup label="Layer 7">
								<?php
									$getMeth=$pdo->prepare("SELECT * FROM `methodes` WHERE `type`=7");
									$getMeth->execute();
									while($meth=$getMeth->fetch()){
										echo "<option value='".$meth['name']."'>".$meth['displayname']."</option>";
									}
								?>
								</optgroup>
							</select>
						</div>
						<div class="form-group">
							<button class="btn btn-block btn-stressing" onclick="startL7()">Attaquer</button>
						</div>
						
					</div>
					<div id="layer4-content" class="layer-content" style="display:none">
						<form method="post">
							<div class="form-group">
								<label>Adresse IP</label>
								<input class="form-control" type="text" name="target" placeholder="1.1.1.1">
							</div>
							<div class="form-group">
								<label>Port</label>
								<input class="form-control" type="number" name="port" placeholder="80">
							</div>
							<div class="form-group">
								<label>Methode</label>
								<select class="custom-select" name="method">
									<optgroup label="Layer 4">
									<?php
										$getMeth=$pdo->prepare("SELECT * FROM `methodes` WHERE `type`=4");
										$getMeth->execute();
										while($meth=$getMeth->fetch()){
											echo "<option value='".$meth['name']."'>".$meth['displayname']."</option>";
										}
									?>
									</optgroup>
								</select>
							</div>
							<div class="form-group">
								<button class="btn btn-block btn-stressing" type="submit">Attaquer</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12 col-md-4">
			<div class="card shadow bg-transparent">
				<div class="card-header bg-stressing">
					<h5>Vos attaques en cours</h5>
				</div>
				<div class="card-body bg-secondary">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Cible</th>
								<th scope="col">Layer</th>
								<th scope="col">Durée</th>
								<th class="text-center" scope="col">Date</th>
							</tr>
						</thead>
						<tbody id="myAttacks">
							<tr>
								<th>-</th>
								<td class="text-center">-</td>
								<td class="text-center">-</td>
								<td class="text-center">-</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
		
		function select(layer){
			if(layer == "layer7"){
				document.getElementById(layer+"-content").style.display = "block"
				document.getElementById(layer+"-btn").className = "hub-btn active"
				
				document.getElementById("layer4-content").style.display = "none"
				document.getElementById("layer4-btn").className = "hub-btn"
			} else {
				document.getElementById(layer+"-content").style.display = "block"
				document.getElementById(layer+"-btn").className = "hub-btn active"
				
				document.getElementById("layer7-content").style.display = "none"
				document.getElementById("layer7-btn").className = "hub-btn"
			}
		}
		
		setInterval(function() {
			let myattacks = document.getElementById("myAttacks").innerHTML
			$.get("function/getAttacks.php", function(data){
				if(myattacks != data){
					document.getElementById("myAttacks").innerHTML = data;
				}
			})
		}, 1000)
		
		function startL7(){
			let target = document.getElementById("targetL7").value
			let duree = document.getElementById("dureeL7").value
			let methode = document.getElementById("methodeL7").value
			
			if(target != null && target.startsWith("http") && target.includes("://") && target.length >= 15){
				if(duree >= 10){
					if(methode != null && methode != ""){
						$.post( "function/startL7.php", { 'target': target, 'time': duree, 'methode': methode },function( data ) {
							if(data == "success"){
								Swal.fire({
									position: 'top-end',
									icon: 'success',
									toast: true,
									html: "Votre attaque a bien été lancée !",
									showConfirmButton: false,
									timerProgressBar: true,
									timer: 3000
								})
							} else if(data.includes("Your attack has been sent to")){
								Swal.fire({
									position: 'top-end',
									icon: 'success',
									toast: true,
									html: "Votre attaque a bien été lancée !",
									showConfirmButton: false,
									timerProgressBar: true,
									timer: 3000
								})
							} else if(data == "noserver"){
								error("Aucun serveur n'est disponnible");
							} else if(data == "maxconcurent"){
								error("Vous avez atteint votre nombre maximum d'attaque, patientez ...");
							} else if(data == "maxtime"){
								error("Vous ne pouvez pas lancer une attaque de "+duree+"s");
							} else if(data == "not login"){
								error("Problème de connexion, veuillez vous reconnecter");
							} else {
								error("Une erreur est survenue.");
								console.log(data)
							}
						})
					}
				} else {
					error("Vous devez indiquer une durée d'attaque de plus de  10s");
				}
			} else {
				error("Veuillez saisir une URL valide");
			}
		}
	</script>
</div>

<?php include("../include/footer.php"); ?>