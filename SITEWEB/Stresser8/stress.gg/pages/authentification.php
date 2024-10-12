<?php
//Variables de pages
$p="auth";
$pageTitle="Authentification";
//End

include("../require.php");

$req = $pdo->query('SELECT * FROM users');
if ($req->rowCount() >= 0) {
    $stats = $req->rowCount();
}

$req = $pdo->query('SELECT * FROM attaques');
if ($req->rowCount() >= 0) {
    $statsattaque = $req->rowCount();
}

$req = $pdo->query('SELECT * FROM attaques WHERE statut = 0');
if ($req->rowCount() >= 0) {
    $statsattaquelive = $req->rowCount();
}

$req = $pdo->query('SELECT * FROM users WHERE plan != "Free"');
if ($req->rowCount() >= 0) {
    $statsuser = $req->rowCount();
}
?>
<html lang="en">
<head>
	<style id="stndz-style"></style>
	<title>WebStress - The best IP Stresser / Booter</title>
	<meta charset="UTF-8">
	<meta name="revisit-after" content="3 days">
	<meta name="robots" content="index, follow">
	<meta name="description" content="DDoS stress test service (web stresser), Cloudflare bypass, DDoS-Guard, Blazingfast and many more bypasses, see if your tested targets can handle stress in real-time. We send huge amounts of network data to your target for your own stress-testing purposes">
	<meta name="keywords" content="stress testers, network testing, test servers, web stresser, test protections, stressers, stresser, booter, ip stresser, website stresser, network load, real-time stress, stress test servers, networking tests, internet killer, boot off,free ip stresser,free ip booter,booter,ip stresser,stresser,booter,ip booter, ip stresser, botnet, stresser, booter, ddos, ddos attack, ddos tool, gbps, layer 7, layer 4, cloudflare, bypass, free ip stresser, free ip booter, booter, ip stresser, stresser, booter">
	<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
	<meta name="theme-color" content="#2266af">
	<meta name="msapplication-navbutton-color" content="#2266af">
	<meta name="apple-mobile-web-app-status-bar-style" content="#2266af">
	<meta property="og:image" content="assets/images/logo.gif">
	<meta property="og:title" content="IP (Network) / Website ddos* stress-testing service">
	<meta property="og:description" content="DDoS stress test service and IP booter, Cloudflare bypass, DDoS-Guard, Blazingfast and many more bypasses, see if your tested targets can handle stress in real-time. We send huge amounts of network data to your target for your own stress-testing purposes">
	<meta property="og:site_name" content="Stresser">
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="stylesheet" href="/assets/css/main.css" type="text/css">
	<script src="assets/js/anime.min.js" type="text/javascript"></script>
	<script src="assets/js/msgpack-1.12.0.min.js" type="text/javascript"></script>
	<script src="assets/js/sweetalert2.all.min.js" type="text/javascript"></script>
	
	<script>
		function stopLoader(){
			document.getElementById("imgloader").style.opacity = 0;
			setTimeout(function() {
				document.getElementById("imgloader").remove();
			}, 1000)
		}
	</script>
</head>
<body onload="stopLoader()" class="landing">
<loader style="transition-duration: 1s" id="imgloader" class="0">
	<svg id="loadersvg" viewBox="0 0 999.1 951.5">
	<defs>
		<style>.cls-1{stroke-miterlimit:25;stroke-width:50px;fill:url(#linear-gradient);stroke:url(#linear-gradient-2);}.cls-2{opacity:0.14;}.cls-3{fill:url(#linear-gradient-3);}.cls-4{fill:url(#linear-gradient-4);}.cls-5{fill:url(#linear-gradient-5);}.cls-6{fill:url(#linear-gradient-6);}.cls-7{fill:url(#linear-gradient-7);}.cls-8{fill:url(#linear-gradient-8);}.cls-9{fill:url(#linear-gradient-9);}</style>
		<linearGradient id="linear-gradient" x1="281.44" y1="81.56" x2="600.81" y2="767.76" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#fff"></stop><stop offset="1" stop-color="#bfbfbf"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-2" x1="0.47" y1="453.34" x2="999.61" y2="453.34" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#016c85"></stop><stop offset="0.1" stop-color="#026a89"></stop>
			<stop offset="0.19" stop-color="#076594"></stop><stop offset="0.27" stop-color="#0e5da6"></stop>
			<stop offset="0.35" stop-color="#1951c0"></stop><stop offset="0.43" stop-color="#2642e1"></stop>
			<stop offset="0.45" stop-color="#2a3eea"></stop><stop offset="0.54" stop-color="#3239e7"></stop>
			<stop offset="0.69" stop-color="#492be0"></stop><stop offset="0.88" stop-color="#6e15d3"></stop>
			<stop offset="1" stop-color="#8a04ca"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-3" x1="58.27" y1="940.39" x2="476.21" y2="602.26" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#01303f"></stop><stop offset="0.18" stop-color="#0f3456"></stop>
			<stop offset="0.56" stop-color="#343d91"></stop><stop offset="1" stop-color="#6449de"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-4" x1="614.34" y1="361.66" x2="338.77" y2="1060.25" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#6824d5"></stop><stop offset="0.26" stop-color="#5138cd"></stop>
			<stop offset="0.81" stop-color="#176cb7"></stop><stop offset="1" stop-color="#017faf"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-5" x1="726.77" y1="380.53" x2="678.83" y2="467.53" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#5e08c3"></stop><stop offset="0.42" stop-color="#621dcf"></stop>
			<stop offset="1" stop-color="#6940e2"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-6" x1="411.69" y1="479.69" x2="403.98" y2="516.93" gradientTransform="matrix(1.01, -0.02, 0.01, 0.96, -18.16, 5.96)" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#3b2dc0"></stop><stop offset="0.65" stop-color="#4344cf"></stop>
			<stop offset="1" stop-color="#4852d8"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-7" x1="745.51" y1="420.78" x2="399.86" y2="438.24" gradientUnits="userSpaceOnUse">
			<stop offset="0.4" stop-color="#3d1ab8"></stop><stop offset="0.69" stop-color="#4631c8"></stop>
			<stop offset="1" stop-color="#524ddb"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-8" x1="632.26" y1="324.39" x2="586.6" y2="114.27" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#413cca"></stop><stop offset="0.29" stop-color="#5b34d5"></stop>
			<stop offset="0.92" stop-color="#9e20f0"></stop><stop offset="1" stop-color="#a71df4"></stop>
		</linearGradient>
		<linearGradient id="linear-gradient-9" x1="522.23" y1="429.45" x2="471.85" y2="69.89" gradientUnits="userSpaceOnUse">
			<stop offset="0" stop-color="#2c38bc"></stop><stop offset="0.23" stop-color="#4333c7"></stop>
			<stop offset="0.72" stop-color="#7d27e2"></stop><stop offset="1" stop-color="#a21ff3"></stop>
		</linearGradient>
	</defs>
		<g id="">
			<path class="cls-1" d="M158.4,711H831.7c90.5,0,163.9-74.1,163.9-165.4h0c0-82.5-66.2-149.4-148-149.4H791v-17c0-75.9-60.9-137.4-136-137.4,0,0-33.2-7-96.4,34.1h0S500,203.7,419.3,195.7H389.8c-104.2,0-188.7,85.3-188.7,190.5v10H152.4C70.7,396.2,4.5,463.1,4.5,545.6v10C4.5,641.4,73.4,711,158.4,711Z" transform="translate(-0.5 -20.1)" style="stroke-dasharray: 2595, 2597; stroke-dashoffset: 0;"></path>
		</g>
		<g>
			<path class="cls-2" d="M79.4,951.5L905.3,497.5L522.6,407.3L848.6,48.5L145.3,471.7L511.5,561.1L79.4,951.5Z" style="stroke-dasharray: 3601, 3603; stroke-dashoffset: 0;"></path>
			<path class="cls-3" d="M67.5,918.7L681.9,488.5L508.1,523.8L67.5,918.7Z" style="stroke-dasharray: 1520, 1522; stroke-dashoffset: 0;"></path>
			<path class="cls-4" d="M902.9,459.4L677,488.5L67.5,918.7L902.9,459.4Z" style="stroke-dasharray: 1928, 1930; stroke-dashoffset: 0;"></path>
			<path class="cls-5" d="M511.4,368.2L675.4,489.1L905.3,459.9L511.4,368.2Z" style="stroke-dasharray: 840, 842; stroke-dashoffset: 0;"></path>
			<path class="cls-6" d="M126.4,432.3L687.8,486.7L510.4,529.6L126.4,432.3Z" style="stroke-dasharray: 1143, 1145; stroke-dashoffset: 0;"></path>
			<path class="cls-7" d="M677,488.5L515.8,368.2L134.2,433.3L677,488.5Z" style="stroke-dasharray: 1134, 1136; stroke-dashoffset: 0;"></path>
			<path class="cls-8" d="M845.6,5.3L365.5,397.9L511.2,373.1L845.6,5.3Z" style="stroke-dasharray: 1266, 1268; stroke-dashoffset: 0;"></path>
			<path class="cls-9" d="M134.2,437.7L851.3,0L374.1,396.1L134.2,437.7Z" style="stroke-dasharray: 1704, 1706; stroke-dashoffset: 0;"></path>
		</g>

	</svg>
</loader>

<div id="snackbar"></div>
<div id="blurred"></div>
<main>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<div class="yoo">
        <i class="x-logo land"></i>
        <div class="authing">
            <h1>Welcome to Webstress.gg !</h1>
            <small id="progress">Enter a nickname to login / create an account.</small><br>
            <input id="usernamei" onkeypress="nextPress(event)" placeholder="Pseudonyme" type="text" spellcheck="false" required="">
            <input id="passwordi" onkeypress="nextPress(event)" placeholder="Mot de passe" type="password" required="">
            <button class="buttona" id="next"><i class="fad fa-arrow-right"></i></button>
        </div>
        <div id="landingstats">
            <h1><b><?= $statsuser ?></b><i class="fad fa-users-crown"></i> Clients</h1>
            <h1><b><?= $statsattaque ?></b><i class="fad fa-comet"></i> Total attacks</h1>
            <h1><b><?= $statsattaquelive ?></b><i class="fad fa-circle-notch fa-spin"></i> Ongoing attacks</h1>
            <h1><b><?= $stats ?></b><i class="fad fa-user-visor"></i> Users</h1>
            <h1><b id="onlineusers" class="oncount">-</b><i class="fad fa-user-astronaut"></i> Online Customers</h1>
        </div></div>
    <img class="concept" src="/images/concept.png">

    <div class="footer">
        <div class="footer-text">

            <div class="footer-urls">
                <div class="footer-url-header"><i class="fad fa-id-card-alt"></i> Contact</div>
                <div class="footer-url-content"><a href="https://t.me/Stressingcc"><i class="fab fa-telegram"></i> Telegram</a></div>
            </div>

            <div class="footer-urls">
                <div class="footer-url-header"><i class="fad fa-link"></i> Link</div>
                <div class="footer-url-content"><a href="/terms.txt" target="_blank"><i class="fad fa-book"></i> Terms and conditions</a></div>
            </div>

            <div class="footer-copyright-header"><i class="x-logo small"></i> Webstress.gg</div>
            <div class="footer-copyright"><i class="fad fa-copyright"></i> Copyright © Webstress.gg 2020. <i class="fad fa-registered"></i> All rights reserved.</div>
        </div>
        <br>
	</div>
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
	
	setInterval(function() {
		window.jQuery.get( "function/getOnline.php", function( data ) {
			let online = document.getElementById("onlineusers");
			if(online != null){
				online.innerHTML = data;
			}
		})
	}, 1000)

    function nextPress(e){
        if(e.charCode == 13){
            authNext();
        }
    }
	
	var myusername, mypassword, stage = 0;
	let next = document.getElementById("next")
	next.onclick = () => {
		authNext();
	}
	
	let action = "";
	function authNext() {
		switch (stage) {
		case 0:
			myusername = usernamei.value;
			myusername = myusername.replace(/ /g, '');
			usernamei.value = myusername;
			anime({
				targets: usernamei
				, translateY: -100
				, opacity: 0
				, scale: 1.2
			});
			usernamei.blur();
			if(myusername.length >= 4){
				$.post( "authentification.php", {"username": myusername},function( data ) {
					anime({
						targets: progress
						, scale: 1.2
						, duration: 1500
						, easing: 'easeInOutQuad'
						, direction: 'alternate'
					});
					console.log(data)
					if(data == "yes"){
						action="login";
						progress.innerText = `Log in as ${myusername}`;
						passwordi.placeholder = "Your password";
					} else {
						action="register"
						progress.innerText = `Register as ${myusername}`;
						passwordi.placeholder = "New Password";
					}
					setTimeout(() => {
						usernamei.style.display = 'none';
						passwordi.style.display = 'inline-block';
						setTimeout(() => {
							passwordi.focus()
						}, 100);
						anime({
							targets: next
							, translateX: 0
						});
					}, 500);
				})
				anime({
					targets: next
					, translateX: 80
				});
				stage = 1;
				break;
			} else {
				setTimeout(function() {
					error("Your username must be at least 4 characters long")
					anime({
						targets: usernamei
						, translateY: 0
						, opacity: 1
						, scale: 1
					});
				}, 500)
				break;
			}
		case 1:
			anime({
				targets: next
				, translateY: -50
			});
			mypassword = passwordi.value;
			anime({
				targets: passwordi
				, translateY: -100
				, opacity: 0
				, scale: 1.2
			});
			passwordi.blur();
			$.post( "authentification.php", { 'action': action, 'username': myusername, 'password': mypassword },function( data ) {
				let actionName = "connecté";
				if(action == "register"){ actionName= "inscrit"; }
				if(data == "success"){
					Swal.fire({
						position: 'top-end',
						icon: 'success',
						toast: true,
						html: "And there you are "+actionName+" !",
						showConfirmButton: false,
						timerProgressBar: true,
						timer: 3000
					})
					setTimeout(function() {
						window.location.reload()
					}, 1000)
				} else if(data == "dupli"){
					error("This IP address is already registered.")
				} else if(data == "suspend"){
					error("Your account is suspended.")
				} else if(data == "ban"){
					error("Your account is banned.")
				} else {
					error("incorrect password")
					console.log(data)
				}
				if(data != "success"){
					action = "";
					setTimeout(() => {
						usernamei.style = '';
						passwordi.style = '';
						anime.remove(usernamei);
						anime.remove(passwordi);
						anime({
							targets: next
							, translateY: 0
						});
						anime({
							targets: progress
							, scale: 1.3
							, duration: 1500
							, easing: 'easeInOutQuad'
							, direction: 'alternate'
						});
						progress.innerHTML = 'Enter a nickname to login / create an account.';
						usernamei.focus();
					}, 300);
					next.innerHTML = '<i class="fad fa-arrow-right"></i>';
					next.disabled = false;
				}
			})
			
			next.innerHTML = `<i class="fad fa-sync fa-spin"></i>`;
			next.disabled = true;
			stage = 0;
			break;
		default:
			stage = 0;
			break;
		}
	}
</script>
</body>
</html>