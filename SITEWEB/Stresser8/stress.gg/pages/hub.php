<?php
//Variables de la pages
$p="hub";
$pageTitle="Hub";
//End

require_once("../require.php");

include("../include/header2.php");
?>
	<style>
		@media only(max-width:600px){
			.special-toby{
				margin-top: 25px;
			}
		}
		main .panel .launcherbox .specialform {
			padding-top: 0px !important;
			padding-bottom: 0px !important;
		}
		.swal2-select option {
			background: #000;
		}
		.swal2-select{
			border: 0px;
		}
		.swal2-modal .swal2-confirm{
			background: #181f96;
		}
	</style>
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="assets/js/bootstrap-toggle.min.js"></script>
		<div class="panel row">
			<div class="col">
				<div class="testsbox">
					<h1><i class="fad fa-wifi-1"></i> Attaques en cours <small class="onlineusers"><b id="onlineusers" class="oncount">-</b> Utilisateurs en ligne</small></h1>
					<div class="tests-container">
						<table class="tests" id="liveattacks">
							<tbody id="allAttacks">
								<tr>
									<td>
										<center><i class='fad fa-times fa-2x'></i> Aucune attaque en cours</center>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col">
				<div id="aread">
					<div class="mover fad fa-space-station-moon-alt"></div>
					<div class="mover2 fad fa-star-shooting"></div>
					<div class="mover4 fad fa-planet-ringed"></div>
					<div class="mover3 fad fa-meteor"></div>
					<i class="fad fa-rocket-launch" id="rocket"></i>
				</div>
				<div style="border-radius:20px" class="launcherbox" id="launcherbox" layer="7">
					<div class="layers tabso">
						<span class="tab active" tabbing="pl7"><i class="fad fa-browser"></i> Layer 7</span>
						<span class="tab" onclick="error('Le layer 4 n\'est pas encore disponible.')" tabbing="pl4"><i class="fad fa-wifi-slash"></i> Layer 4</span>
					</div>
					<tab class="active" name="pl7"></tab>
					<tab name="pl4"></tab>
					<div class="form">
						<div class="row">
							<fieldset class="col-8 material addresser">
								<input id="target" autocomplete="off" placeholder="http://..." type="text" spellcheck="false" required="">
								<label for="target"><i class="fal fa-chart-network"></i> URL</label>
								<hr>
							</fieldset>
						</div>
						<label for="dmethod"><i class="fad fa-alien-monster"></i> Méthodes</label>
						<div class="lm-select" id="dmethod">
							<span class="lm-select-ico"></span>
							<?php
								$getMethod=$pdo->prepare("SELECT * FROM `methodes` WHERE `type`=7 ORDER BY `id` ASC LIMIT 1");
								$getMethod->execute();
								$methode=$getMethod->fetch();
								echo "<div id='selectedMethod' data-value='".$methode['name']."' class='lm-select-value'>".$methode['displayname']."</div>";
							?>
							<div style="top:100%" id="methodList" class="lm-select-options">
								<div class="lm-select-list">
								<?php
									$getMethods=$pdo->prepare("SELECT * FROM `methodes` WHERE `type`=7 ORDER BY `id` ASC");
									$getMethods->execute();
									$first="lm-select-selected";
									while($meth=$getMethods->fetch()){
										echo "<div onclick='selectMethod(event)' id='ml7-".$meth['name']."' data-value='".$meth['name']."' class='lm-select-item ".$first."'>".$meth['displayname']."</div>";
										$first="";
									}
								?>
								</div>
							</div>
							<div class="lm-select-overflow"></div>
						</div>
					</div>
					<div style="display:none" id="form-BYPASS-ENGINE">
						<div class="form specialform">
							<button onclick="options()" id="optionsbtn"><i class="fad fa-sliders-h-square"></i> Options</button>
						</div>
					</div>
					<div class="form">
						<label for="durrange"><i class="fad fa-stopwatch"></i> Temps</label>
						<input type="range" id="durrange" min="30" value="30" max="<?php echo $userInfo['secondes']+$userInfo['extra_secondes']; ?>">
						<fieldset class="material durationer">
							<input id="dduration" type="number" value="30" spellcheck="false">
							<hr>
							<label>Secondes</label>
						</fieldset>
					</div>
					<div class="form">
						<label for="nbconcurents"><i class="fas fa-running"></i> Envois simultanés</label>
						<input type="range" id="nbconcurents" min="1" value="1" max="<?php echo $userInfo['concurrents']+$userInfo['extra_concurrents']; ?>">
						<fieldset class="material durationer">
							<input id="dconcurents" type="number" value="1" spellcheck="false">
							<hr>
							<label>Envois simultanés</label>
						</fieldset>
					</div>
				</div>
				<button style="margin-bottom: 10px" id="launcherbtn" class="start"><i class="fad fa-rocket"></i> Initialiser</button>
			</div>
			<div class="special-toby col">
				<div class="testsbox">
					<h1><i class="fad fa-network-wired"></i> Vos attaques en cours</h1>
					<div class="tests-container">
					<table id="tests">
						<tbody id="myAttacks">
							<tr>
								<td><center><i class="fad fa-times fa-2x"></i> Historique des attaques</center></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<style>
			.swal2-popup.swal2-modal.swal2-show{
				border-radius:25px;
				background: #000 !important;
			}
			.swal2-modal .swal2-content{
				color: #fff;
			}
		</style>
		<script>
		var layer = 7;
		
		var globalthreads=1;
		var globaluseragent="default";
		var globalratelimit="true";
		var globalreferer="https://google.com";
		
		//Gestion mes attaques
		setInterval(function(){
			let myAttacks = document.getElementById("myAttacks");
			window.jQuery.get( "function/getAttacks.php",function( data ) {
				myAttacks.innerHTML = data;
			})
			
			let allAttacks = document.getElementById("allAttacks");
			window.jQuery.get( "function/getAllAttacks.php",function( data ) {
				allAttacks.innerHTML = data;
			})
		}, 1000)
		//End
		
		//Gestion stopAttack
		function stopAttack(id){
			window.jQuery.get({ url: "function/stopAttack.php?id="+id}, function(data) {
				if(data == "success"){
					Swal.fire({
						position: 'top-end',
						icon: 'success',
						toast: true,
						html: "Votre attaque a bien été stoppée.",
						showConfirmButton: false,
						timerProgressBar: true,
						timer: 3000
					})
				} else if(data == "already stop"){
					error("Cette attaque est déjà arrêtée")
				} else if(data == "not supported"){
					error("Nous ne pouvons pas stopper cette attaque.")
				} else if(data == "not started"){
					error("Veuillez attendre encore quelques secondes")
				} else if(data == "not you"){
					error("Ce n'est pas votre attaque");
				} else if(data == "invalid params") {
					error("Impossible de trouver votre attaque")
				} else if(data == "not login"){
					error("Impossible de vous authentifier, reconnectez vous");
				} else {
					console.log(data)
					error("Une erreur est survenue");
				}
			}).catch(function(e) {
				if(e.statusText == 'timeout'){
					error("Erreur interne, patientez");
				}
				console.log(e)
			})
		}		
		//End
		
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
		
		//Gestion menu selection Methode
			$("body").click(function(e) {
				let options = document.getElementById("methodList");
				if(e.target.id != "selectedMethod" && options.style.opacity != 0){
					let options = document.getElementById("methodList");
					options.style.opacity = 0;
					options.style.visibility = "hidden"
				}
			})
		
			document.getElementById("selectedMethod").onclick = function() {
				let options = document.getElementById("methodList");
				if(options.style.opacity == 0){
					options.style.opacity = 1;
					options.style.visibility = "visible"
				} else {
					options.style.opacity = 0;
					options.style.visibility = "hidden"
				}
			};
			
			function selectMethod(e){
				let selection = e.target.dataset.value
				
				let selectedMethod = document.getElementById("selectedMethod")
				if(selectedMethod.dataset.value != selection){
					if(selection == "BYPASS-ENGINE"){ document.getElementById("form-BYPASS-ENGINE").style.display = "block" }
					else { document.getElementById("form-BYPASS-ENGINE").style.display = "none" }
					
					let oldMethodData = selectedMethod.dataset.value
					
					selectedMethod.innerHTML = e.target.innerHTML
					selectedMethod.dataset.value = selection
					
					e.target.className = "lm-select-item lm-select-selected"
					document.getElementById("ml7-"+oldMethodData).className = "lm-select-item"
				}
				
				let options = document.getElementById("methodList");
				options.style.opacity = 0;
				options.style.visibility = "hidden"
			}
		//End
		
		//Gestion du scroll Durée
			let durrange = document.getElementById("durrange");
			let dduration = document.getElementById("dduration");
			durrange.oninput = function () {
				dduration.value = durrange.value;
			}
			dduration.oninput = function () {
				durrange.value = dduration.value;
			}
		//End
		
		//Gestion du scroll Concurents
			let nbconcur = document.getElementById("nbconcurents");
			let dconcurents = document.getElementById("dconcurents");
			nbconcur.oninput = function () {
				dconcurents.value = nbconcur.value;
			}
			dconcurents.oninput = function () {
				nbconcur.value = dconcurents.value;
			}
		//End
		
		//Gestion du scroll Threads
			setInterval(function(){
				let nbthread = document.getElementById("threadrange");
				let threadnb = document.getElementById("threadnb");
				if(nbthread && threadnb){
					nbthread.oninput = function () {
						threadnb.value = nbthread.value;
					}
					threadnb.oninput = function () {
						nbthread.value = threadnb.value;
					}
				}
			},500)
		//End
			
		//Gestion lancement attaque
			let erreur = true;
			launcherbtn.onclick = () => {
				launcherbtn.disabled = true;
				anime.remove(launcherbtn);
				anime({
					targets: launcherbtn
					, translateY: -5
					, easing: 'easeInOutQuad'
					, direction: 'alternate'
					, duration: 700
				});
				anime.remove($('#launcherbtn>i')[0]);
				anime({
					targets: $('#launcherbtn>i')[0]
					, translateX: 150
					, translateY: -150
					, rotate: -5
					, scale: 3
					, opacity: -3
					, easing: 'easeInOutQuad'
					, direction: 'alternate'
					, duration: 1050
				});
				anime.remove($('#launcherbox .form')[0]);
				anime({
					targets: $('#launcherbox .form')[0]
					, translateY: -80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .form')[1]);
				anime({
					targets: $('#launcherbox .form')[1]
					, translateY: 80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .form')[2]);
				anime({
					targets: $('#launcherbox .form')[2]
					, translateY: 80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .form')[3]);
				anime({
					targets: $('#launcherbox .form')[3]
					, translateY: 80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .form')[4]);
				anime({
					targets: $('#launcherbox .form')[4]
					, translateY: 80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .form')[5]);
				anime({
					targets: $('#launcherbox .form')[5]
					, translateY: 80
					, opacity: 0
					, duration: 1000
				});
				anime.remove($('#launcherbox .layers.tabso')[0]);
				anime({
					targets: $('#launcherbox .layers.tabso')[0]
					, translateY: -100
					, opacity: 0
					, duration: 1000
				});
				aread.style.display = 'block';
				anime.remove(aread);
				anime({
					targets: aread
					, opacity: 1
					, scale: 1.2
					, duration: 800
				});
				setTimeout(() => {
					requestAnimationFrame(() => {
						anime({
							targets: aread
							, opacity: 0
							, scale: 1
							, duration: 800
						});
						anime({
							targets: $('#launcherbox .form')[0]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .form')[1]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .form')[2]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .form')[3]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .form')[4]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .form')[5]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						anime({
							targets: $('#launcherbox .layers.tabso')[0]
							, translateY: 0
							, opacity: 1
							, duration: 700
						});
						setTimeout(() => {
							requestAnimationFrame(() => {
								anime.remove($('#launcherbox .form')[0])
								$('#launcherbox .form')[0].style = '';
								anime.remove($('#launcherbox .form')[1])
								$('#launcherbox .form')[1].style = '';
								anime.remove($('#launcherbox .form')[2])
								$('#launcherbox .form')[2].style = '';
								anime.remove(aread);
								aread.style.display = 'none';
								launcherbtn.disabled = false;
							});
						}, 870);
					});
				}, 1500);
				anime.remove(rocket);
				var postData = {}
				let target, duree, methode, port, concurent;
				if(layer == 7){
					target = document.getElementById("target").value
					duree = document.getElementById("dduration").value
					methode = document.getElementById("selectedMethod").dataset.value
					concurent = document.getElementById("dconcurents").value
					
					//threads = document.getElementById("threadnb").value
					//referer = document.getElementById("referer").value
					//ratelimit = document.getElementById("ratelimit").checked
					
					threads = globalthreads;
					referer = globalreferer;
					ratelimit = globalratelimit;
					useragent = globaluseragent;
					
					postData = { 'target': target, 'time': duree, 'methode': methode };
					if(methode == "BYPASS-ENGINE"){ postData = { 'target': target, 'time': duree, 'methode': methode, 'threads': threads, 'referer': referer, 'ratelimit': ratelimit, 'useragent': useragent }; }
				}
				if(target != null && target.startsWith("http") && target.includes("://") && target.length >= 15){
					if(duree >= 10){
						if(methode != null && methode != ""){
							window.jQuery.post( "function/startL7.php", postData,function( data ) {
								if(data == "success"){
									erreur=false;
									Swal.fire({
										position: 'top-end',
										icon: 'success',
										toast: true,
										html: "Votre attaque a bien été lancée !",
										showConfirmButton: false,
										timerProgressBar: true,
										timer: 3000
									})
									startRocket();
									erreur=true;
									if(concurent != 1){
										let nbStart=1
										while(nbStart < concurent){
											nbStart++;
											window.jQuery.post( "function/startL7.php", postData,function( data ) {
												if(data == "success"){
													erreur=false;
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
												} else if(data == "blacklist"){
													error("Cette cible est protégée car elle a payé pour cela ;)");
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
									}
								} else if(data.includes("not nooder")){
									error("Vous ne pouvez pas utiliser cette méthode sur ce site");
								} else if(data == "maxthread"){
									error("Vous ne pouvez pas envoyer une attaque de "+threads+" threads"); startRocket();
								} else if(data == "blacklist"){
									error("Cette cible est protégée car elle a payé pour cela ;)"); startRocket();
								} else if(data == "noserver"){
									error("Aucun serveur n'est disponnible"); startRocket();
								} else if(data == "maxconcurent"){
									error("Vous avez atteint votre nombre maximum d'attaque, patientez ..."); startRocket();
								} else if(data == "maxtime"){
									error("Vous ne pouvez pas lancer une attaque de "+duree+"s"); startRocket();
								} else if(data == "not login"){
									error("Problème de connexion, veuillez vous reconnecter"); startRocket();
								} else {
									error("Une erreur est survenue."); startRocket();
									console.log(data)
								}
							})
						} else {
							error("La méthode demandée est introuvable"); startRocket();
						}
					} else {
						error("Vous devez indiquer une durée d'attaque de plus de  10s"); startRocket();
					}
				} else {
					error("Veuillez saisir une URL valide"); startRocket();
				}
			}
			function startRocket(){
				if(erreur == true){
					anime({
						targets: rocket
						, color: "#9f2c2c"
						, easing: 'easeInOutQuad'
						, direction: 'alternate'
						, opacity: 1
						, duration: 700
					});
				} else {
					anime({
						targets: rocket
						, color: "#2c9f3a"
						, easing: 'easeInOutQuad'
						, direction: 'alternate'
						, opacity: 1
						, duration: 700
					});
				}
			}
		//End
		
		//Gestion des options
		function options(){
			Swal.fire({
				html: `
				<h2>
					<h2><i class='fad fa-ufo'></i> Paramètres</h2>
					<fieldset style='display: flex;' class="material addresser">
						<input name="referer" id="referer" autocomplete="off" value="${globalreferer}" placeholder="http://google.com" type="text" spellcheck="false" required="">
						<label for="referer"><i class="fal fa-asterisk"></i> Referer</label>
						<hr>
					</fieldset>
					<br>
					<label for="useragent"><i class="fad fa-robot"></i> User Agent</label>
					<select class="swal2-select" id="useragent" style="display: flex;">
						<option value="default">Par défaut</option>
						<option value="spider">Spider</option>
						<option value="bot">Bot</option>
					</select>
					<br>
					<label for="threadrange"><i class="fad fa-microchip"></i> Nombre de threads</label>
					<?php
					if($userInfo['network'] == "free"){ $maxThreads=10; }
					if($userInfo['network'] == "basic"){ $maxThreads=20; }
					if($userInfo['network'] == "vip"){ $maxThreads=30; }
					?>
					<input type="range" id="threadrange" min="1" value="1" max="<?php echo $maxThreads; ?>">
					<fieldset class="material durationer">
						<input name="thread" id="threadnb" type="number" value="1" spellcheck="false">
						<hr>
						<label>Threads</label>
					</fieldset>
					<br>
					<label for="ratelimit"><i class="fad fa-hourglass"></i> Ratelimit</label>
					<select class="swal2-select" id="ratelimit" style="display: flex;">
						<option value="true">Actif</option>
						<option value="false">Désactivé</option>
					</select>
				</div>`
				, focusConfirm: false
				, preConfirm: () => {
					return [ratelimit.value, referer.value, useragent.value, threadnb.value]
				}
			}).then((result) => {
				if (result.dismiss) return;
				let rt = result.value[0]
				let referer = result.value[1]
				let useragent = result.value[2]
				let threads = result.value[3]
				
				globalrt=rt;
				if(referer && referer != null && referer != ""){ globalreferer=referer; } else { globalreferer = "https://google.com"; }
				if(useragent && useragent != null && useragent != ""){ globaluseragent=useragent } else { globaluseragent="default"; };
				globalthreads=threads;
				
				Swal.fire({
					position: 'top-end',
					icon: 'success',
					toast: true,
					html: "Vos paramètres ont bien été enregistrés !",
					showConfirmButton: false,
					timerProgressBar: true,
					timer: 3000
				})
			});
		}		
		//End
		
		</script>
	</main>
<?php include("../include/footer2.php"); ?>