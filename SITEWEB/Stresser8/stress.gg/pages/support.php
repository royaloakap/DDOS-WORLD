<?php
//Variables de la page
$p="support";
$pageTitle="Support";
//End
require_once("../require.php");



include("../include/header2.php");
?>
<style>
	.reponse {
		border-top-right-radius: 15px;
		border-bottom-right-radius: 15px;
		text-align: left;
		margin-top: 10px;
		padding: 5px;
		background: #5597ff;
		width: 70%;
		float: left;
	}
	.reponse.my{
		border-top-left-radius: 15px;
		border-bottom-left-radius: 15px;
		border-top-right-radius: 0px;
		border-bottom-right-radius: 0px;
		background: #494949;
		float: right;
	}
	.reponse .author{
		font-weight: bold;
	}
	.reponse .content{
		padding-left: 5px;
	}
	.answer .statut{
		margin-left: auto;
		margin-right: auto;
		margin-top: 15px;
		max-width: 20%;
		border-radius: 15px;
		padding: 5px;
	}
	.answer .statut.statut-waiting{
		background-color: #fe9c00;
		color: #fff !important;
	}
	.answer .statut.statut-answer{
		background-color: #00a308;
		color: #fff !important;
	}
	.answer .statut.statut-close{
		background-color: #ff0000;
		color: #fff !important;
	}
</style>
<br><br><br>
<main class="support">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<h1><i class="fad fa-question"></i> Support</h1>
		<div class="tabs">
			<div style="border-radius:25px" class="tabs_wrap">
				<div onclick="supportTab('faq')" style="border-top-left-radius:25px;border-bottom-left-radius:25px" class="tab" id="btnfaq" tabbing="faq"><i class="fad fa-question-square"></i> FAQ</div>
				<div onclick="supportTab('tickets')" style="border-top-right-radius:25px;border-bottom-right-radius:25px" class="tab active" id="btntickets" tabbing="tickets"><i class="fad fa-hands-helping"></i> Tickets</div>
			</div>
		</div>
		<tab class="body" id="faq" name="faq">
			<div class="group"><b class="question">What are the payment methods accepted for purchasing a subscription?</b>
				<div class="answer">The methods available to purchase a stressing.cc subscription currently consist of Bitcoin only. You can purchase a subscription by navigating to the shop section (at the top of the web page), by clicking on the "Buy" button under the desired plan, with the credit of your account, the possibility of adding additional options like the number of silmutaneous sends or the additional number of seconds.</div>
			</div>
			<div class="group"><b class="question">What is the difference between layer 4 and layer 7?</b>
				<div class="answer">Since HTTP is the predominant Layer 7 protocol for website traffic, Layer 7 load balancers route network traffic in a superior manner than Layer 4 load balancers, making stress testing layer 7 (more) effective against websites. Layer 4 should be used for other stress tests that are not inclusive of websites.</div>
			</div>
			<div class="group"><b class="question">How much power are you able to deliver?</b>
				<div class="answer">
					<table>
						<thead>
							<tr>
								<th><i class="fad fa-network-wired"></i> Network</th>
								<th><i class="fad fa-wifi-slash"></i> Layer 4</th>
								<th><i class="fad fa-browser"></i> Layer 7</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Free Network</td>
								<td>10 Gbps+</td>
								<td>100,000+ requests/second</td>
							</tr>
							<tr>
								<td>Basic Network</td>
								<td>40 Gbps+</td>
								<td>200,000+ requests/second ( Bypass Included )</td>
							</tr>
							<tr>
								<td>VIP Network</td>
								<td>100 Gbps+</td>
								<td>400,000+ requests/second ( Bypass Included )</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="group"><b class="question">Quelle méthode dois-je utiliser pour tester mon réseau ?</b>
				<div class="answer">Si vous n'êtes pas sûr de la méthode à utiliser, n'hésitez pas à contacter le support ou un administrateur.
					<br>Veuillez lire les petites descriptions de chaque méthode.</div>
			</div>
			<div class="group"><b class="question">Quels sont les contournements disponibles pour Layer7 ?</b>
				<div class="answer">
					<ul style="color: #46c366;">
						<li><i class="fas fa-clouds"></i> Cloudflare
							<br><small>UAM<br>Captcha</small></li>
						<li>DDoS-Guard
							<br><small>RateLimit<br>5-sec</small></li>
						<li>Sucuri
							<br><small>JS Challenge<br>Captcha</small></li>
						<li>OVH UAM</li>
						<li>Universal Javascript Bypass (bientôt disponible)</li>
					</ul>
				</div>
			</div>
			<div class="group"><b class="question">Pourquoi mon compte est-il bloqué ?</b>
				<div class="answer">Les comptes sont verrouillés en raison d'une violation (ou de plusieurs violations) de notre <a href="/terms.txt" target="_blank">Conditions de service</a>.
					<br> Les raisons les plus courantes pour lesquelles un compte est bloqué sont les suivantes :
					<br>
					<ul style="color: red;">
						<li>Détenir et/ou utiliser des comptes multiples</li>
						<li>Abus et/ou spam des attaques de test</li>
						<li>Usage abusif des tests (Attaquer sans les permissions/le consentement de la cible)</li>
						<li>Partage de l'API/accès à votre compte</li>
					</ul>
				</div>
			</div>
			<div class="group"><b class="question">Quelle est la garantie que mon attaque échouera ou réussira ?</b>
				<div class="answer">
					Nous ne garantissons pas que votre test échouera, et le site web/réseau que vous testiez sera hors ligne et ne passera pas nos tests (La sécurité de la cible testée n'était pas assez bonne pour supporter nos tests de stress).
					<br>Il en va de même pour les tests réussis. Nous ne garantissons pas que votre test sera réussi, et le site web/réseau que vous testiez restera en ligne, alors que le système de sécurité cible que vous testiez était suffisamment bon pour réussir nos tests.
				</div>
			</div>
			<div class="group"><b class="question">Les ports communs de la couche 4 ?</b>
				<div class="answer">
					<table>
						<thead>
							<tr>
								<th>Port</th>
								<th>Service</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>53/80</td>
								<td>Résidentiel</td>
								<td>Connexions à la maison</td>
							</tr>
							<tr>
								<td>80</td>
								<td>HTTP</td>
								<td>Trafic web par défaut</td>
							</tr>
							<tr>
								<td>443</td>
								<td>HTTPS</td>
								<td>Sécuriser le trafic web</td>
							</tr>
							<tr>
								<td>22</td>
								<td>SSH/SFTP</td>
								<td>Secure Shell (SSH)</td>
							</tr>
							<tr>
								<td>3306</td>
								<td>MySQL</td>
								<td>Gestion de la base de données</td>
							</tr>
							<tr>
								<td>3074/3076</td>
								<td>Xbox/Psn</td>
								<td>Xbox/Psn par défaut</td>
							</tr>
							<tr>
								<td>9987</td>
								<td>TeamSpeak 3</td>
								<td>Voix/IP par défaut</td>
							</tr>
							<tr>
								<td>25565</td>
								<td>Minecraft</td>
								<td>Serveur par défaut</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="group active"><b class="question">Quelle est la différence entre les bits et les octets ?</b>
				<div class="answer">Habituellement, les bits (b minuscules) sont utilisés pour décrire le transfert de données et les octets (B majuscules) pour le stockage des données. La seule différence physique entre les deux est que chaque octet contient huit bits de données. Sur notre site, vous rencontrerez trois unités communes : Mbps (mégabits par seconde) - taux moyen de transfert de données B (octets) - taille des paquets kB (kilooctets) - taille des paquets (1024 octets = 1 kilooctet).</div>
			</div>
		</tab>
		<tab class="body active" id="tickets" name="tickets">
			<button style="border-radius: 15px" onclick="createTicket()" class="buttona">
				<i class="fad fa-plus-circle"></i> Créer un Ticket
			</button>
			<div style="margin-top: 15px" id="ticketsList">
			<?php
				$getTickets=$pdo->prepare("SELECT * FROM `tickets` WHERE `userid`=? AND `statut`!=2 ORDER BY `timestamp` DESC");
				$getTickets->execute(array($_SESSION['id']));
				while($ticket=$getTickets->fetch()){
					$statut="En cours de traitement";
					$statutColor="waiting";
					if($ticket['statut'] == 1){ $statut="Répondu"; $statutColor="answer"; }
					if($ticket['statut'] == 2){ $statut="Fermé"; $statutColor="close"; }
					
					$messages=json_decode($ticket['content']);
					
					echo "
					<div class='group'>
						<b class='question'>".htmlspecialchars($ticket['objet'])."</b>
						<div class='answer'>
							<div class='row'>";
							foreach($messages as $msg){
								$msg=get_object_vars($msg);
								$time=gmdate("d/m/Y H:i", $msg['time']);
								$author=$msg['author'];
								$msgContent=$msg['content'];
								
								$name="Support"; $isMe="";
								if($author==$_SESSION['id']){ $isMe=" my"; $name="Moi"; }
								
								echo "
								<div class='col-sm-12'>
									<div class='reponse".$isMe."'>
										<div class='author'>
											".$name." <i>(".$time.")</i>
										</div>
										<div class='content'>
											".htmlspecialchars($msgContent)."
										</div>
									</div>
								</div>
								";
							}
					echo "</div>
							<div class='statut statut-".$statutColor."'>".$statut."</div>
						</div>
					</div>";
				}
			?>
			</div>
		</tab>
	</main>
	
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
	
		let questions = window.jQuery('.question')
		for(let i=0;i<questions.length;i++){
			let question = questions[i];
			if (!question.onclick) question.onclick = () => {
				if (question.parentElement.className.indexOf(' active') > -1) {
					question.parentElement.className = question.parentElement.className.replace(' active', '');
				} else {
					question.parentElement.className += " active";
				}
			}
		};
		
		async function createTicket(){
			const { value: text } = await Swal.fire({
				title: 'Nous sommes là pour vous aider ;)',
				html:
					'<input id="ticket-object" placeholder="Titre du ticket" class="swal2-input">' +
					'<textarea id="ticket-content" placeholder="Décrivez nous la situation..." class="swal2-textarea"></textarea>',
				
				showCancelButton: true,
				cancelButtonText: 'Annuler',
				focusConfirm: true,
				preConfirm: () => {
					return [
						document.getElementById('ticket-object').value,
						document.getElementById('ticket-content').value
					]
				}
			});
			if (text) {
				
				let answer = JSON.stringify(text)
				answer = JSON.parse(answer)
				let ticketObject = answer[0]
				let ticketContent = answer[1]
				
				window.jQuery.post( "function/createTicket.php", { object: ticketObject, content: ticketContent } ,function( data ) {
					if(data == "success"){
						Swal.fire('Félicitations !', 'Le ticket a bien été créé, nous reviendrons vers vous dès que possible!', 'success');
					} else if(data == "error") {
						error("Une erreur est survenue.");
					} else if(data == "invalid content"){
						error("Le contenu de votre ticket est trop court");
					} else if(data == "invalid object"){
						error("L'objet de votre ticket est trop court");
					} else if(data == "not login"){
						error("Erreur d'authentification, reconnectez vous");
					} else {
						console.log(data)
						error("Une erreur est survenue");
					}
				})
				
			}
		}
		
		function supportTab(id){
			let btnFaq=document.getElementById("btnfaq")
			let btnTickets=document.getElementById("btntickets")
			
			let tabFaq=document.getElementById("faq")
			let tabTickets=document.getElementById("tickets")
			if(id=="tickets"){
				tabFaq.className="data body";
				tabTickets.className="body active";
				
				btnFaq.className="tab"
				btnTickets.className="tab active"
			} else {
				tabFaq.className="body active";
				tabTickets.className="data body";
				
				btnFaq.className="tab active"
				btnTickets.className="tab"
			}
		}
	</script>

<?php include("../include/footer2.php"); ?>