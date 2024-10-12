<?php
//Variables de la page
$p="deposer";
$pageTitle="Déposer";
//End
require_once("../require.php");



include("../include/header2.php");
?>
<main class="deposit">
	<h1><i class="fad fa-wallet"></i> Deposit</h1>
	<hr>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<div class="depbox">
			<div class="gatewayselect tabso" id="payment_type_selector">
				<span class="tab active" tabbing="cryptocurrency_depo"><i class="fab fa-bitcoin"></i> Cryptocurrency</span>
				<span class="tab" onclick="other()" tabbing="chineseYuan_depo"><i class="fas fa-question"></i> Other</span>
			</div>
			<tab name="cryptocurrency_depo" class="active"></tab>
			<tab name="chineseYuan_depo" class="">
				<small class="note">
					In development

				</small>
			</tab>
			<label for="payment_method"><i class="fa fa-credit-card"></i> Method of payment</label>
			<select id="payment_method">
				<option value="BTC">Bitcoin</option>
				<option value="LTC">LiteCoin</option>
			</select>
			<div class="amounts-box" id="amounts-box">
				<div class="amounts col-sm">
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>15</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>20</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>25</a>
					</button>
				</div>
				<div class="amounts col-sm">
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>30</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>35</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>40</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>45</a>
					</button>
				</div>
				<div class="amounts col-sm">
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>50</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>55</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>60</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>65</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>70</a>
					</button>
				</div>
				<div class="amounts col-sm">
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>75</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>80</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>85</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>90</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>95</a>
					</button>
					<button class="amount-box">
						<i class="fad fa-usd-circle"></i>
						<a>100</a>
					</button>
				</div>
			</div>
			<div class="padded">
				<input id="deposit_amount" value="15" type="number" min="15" max="100">
				<label for="deposit_amount"><i class="fad fa-coins"></i> Amount</label>
				<button onclick="payBtc()" class="buttona deposit_button" id="depositButton"><i class="fad fa-hands-usd"></i> Deposit</button>
				<small class="note">After completing the payment process, the funds will be automatically added to your account.</small>
			</div>
		</div>
		<modal style="height:300px" id="btcermodal">
			<a onclick="closeBtc()"><i class="close"></i></a>
			<div class="top row">
				<div class="left">
					<img id="btcQR" class="btcQR">
				</div>
				<div class="right col">
					<span id="plzsend" class="plzsend"></span>
					<h2 id="addy" class="addy"></h2>
					<button class="buttonc copybtc">Copy address</button>
					<button class="buttonc copybtc">Copy amount</button>
				</div>
			</div>
		</modal>
		
		<script>
			let generated = false;
			let inGeneration = false;
		
			window.jQuery('.copybtc')[0].onclick = () => {
				copy(document.getElementById("addy").innerHTML);
			};
			window.jQuery('.copybtc')[1].onclick = () => {
				copy(document.getElementById("btcVal").innerHTML);
			};
			
			let amountList = window.jQuery('.amount-box')
			for(let i=0;i<amountList.length;i++){
				amountList[i].onclick = () => {
					document.getElementById("deposit_amount").value = parseInt(amountList[i].innerText);
				}
			}
			
			function other(){
				error("To use another payment method, contact us via Telegram or by support ticket.");
			}
			
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
			
			function closeBtc(){
				document.getElementById("btcermodal").style.display = "none"
			}
			
			setInterval(function() {
				if(inGeneration == true){
					if(generated == false){
						sb("Generation in progress, wait ...")
					}
				}
			}, 1000)
			
			function payBtc(){
				if(generated == false){
					let btcAmount = document.getElementById("deposit_amount").value
					let btcCrypto = document.getElementById("payment_method").value
					if(btcAmount != 0 && btcAmount != ""){
						document.getElementById("imgloader").style.display = "flex";
						document.getElementById("imgloader").style.opacity = 1;
						sb("Generation in progress, wait ...")
						window.jQuery.post("function/createCoinpayment.php", { amount: btcAmount, crypto: btcCrypto }, function(data){
							if(data == "not login"){
								stopLoader()
								error("Authentication error, please reconnect.")
							} else if(data == "invalid params"){
								stopLoader()
								error("Invalid amount")
							} else if(data == "error"){
								stopLoader()
								console.log(data)
								error("An error has occurred");
							} else {
								generated = true;
								inGeneration = false;
								
								document.getElementById("btcermodal").style.display = "block"
								stopLoader()
								let info = JSON.parse(data)["result"]
								
								let montant = info['amount'];
								let adress = info['address'];
								let qrCode = info['qrcode_url'];
								let statusUrl = info['status_url'];
								
								document.getElementById("plzsend").innerHTML = "Please send<b><span id='btcVal'>"+montant+"</span> "+btcCrypto+"</b> at the address:"
								document.getElementById("addy").innerHTML = adress
								document.getElementById("btcQR").src = qrCode
								console.log(statusUrl)
							}
						})
					} else {
						error("You must enter a valid amount.");
					}
				} else {
					document.getElementById("btcermodal").style.display = "block"
				}
			}
		
		</script>


<?php include("../include/footer2.php"); ?>