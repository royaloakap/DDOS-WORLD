<?php
//Variables de la page
$p="Shop";
$pageTitle="Boutique";
//End
require_once("../require.php");



include("../include/header2.php");
?>
<style>
.carousel {
  position: relative;
}

.carousel-inner {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.carousel-item {
  position: relative;
  display: none;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  width: 100%;
  transition: -webkit-transform 0.6s ease;
  transition: transform 0.6s ease;
  transition: transform 0.6s ease, -webkit-transform 0.6s ease;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  -webkit-perspective: 1000px;
  perspective: 1000px;
}

.carousel-item.active,
.carousel-item-next,
.carousel-item-prev {
  display: block;
}

.carousel-item-next,
.carousel-item-prev {
  position: absolute;
  top: 0;
}

.carousel-item-next.carousel-item-left,
.carousel-item-prev.carousel-item-right {
  -webkit-transform: translateX(0);
  transform: translateX(0);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-next.carousel-item-left,
  .carousel-item-prev.carousel-item-right {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}

.carousel-item-next,
.active.carousel-item-right {
  -webkit-transform: translateX(100%);
  transform: translateX(100%);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-next,
  .active.carousel-item-right {
    -webkit-transform: translate3d(100%, 0, 0);
    transform: translate3d(100%, 0, 0);
  }
}

.carousel-item-prev,
.active.carousel-item-left {
  -webkit-transform: translateX(-100%);
  transform: translateX(-100%);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-prev,
  .active.carousel-item-left {
    -webkit-transform: translate3d(-100%, 0, 0);
    transform: translate3d(-100%, 0, 0);
  }
}

.carousel-control-prev,
.carousel-control-next {
  position: absolute;
  top: -85%;
  bottom: 0;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  width: 15%;
  color: #fff;
  text-align: center;
  opacity: 0.5;
}

.carousel-control-prev:hover, .carousel-control-prev:focus,
.carousel-control-next:hover,
.carousel-control-next:focus {
  color: #fff;
  text-decoration: none;
  outline: 0;
  opacity: .9;
}

.carousel-control-prev {
  left: 0;
}

.carousel-control-next {
  right: 0;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  display: inline-block;
  width: 20px;
  height: 20px;
  background: transparent no-repeat center center;
  background-size: 100% 100%;
}

.carousel-control-prev-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E");
}

.carousel-control-next-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E");
}

.carousel-indicators {
  position: absolute;
  right: 0;
  bottom: 10px;
  left: 0;
  z-index: 15;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  padding-left: 0;
  margin-right: 15%;
  margin-left: 15%;
  list-style: none;
}

.carousel-indicators li {
  position: relative;
  -webkit-box-flex: 0;
  -ms-flex: 0 1 auto;
  flex: 0 1 auto;
  width: 30px;
  height: 3px;
  margin-right: 3px;
  margin-left: 3px;
  text-indent: -999px;
  background-color: rgba(255, 255, 255, 0.5);
}

.carousel-indicators li::before {
  position: absolute;
  top: -10px;
  left: 0;
  display: inline-block;
  width: 100%;
  height: 10px;
  content: "";
}

.carousel-indicators li::after {
  position: absolute;
  bottom: -10px;
  left: 0;
  display: inline-block;
  width: 100%;
  height: 10px;
  content: "";
}

.carousel-indicators .active {
  background-color: #fff;
}

.carousel-caption {
  position: absolute;
  right: 15%;
  bottom: 20px;
  left: 15%;
  z-index: 10;
  padding-top: 20px;
  padding-bottom: 20px;
  color: #fff;
  text-align: center;
}
</style>
<main class="shop">
	<h1><i class="fad fa-box"></i> Shop</h1>
	<hr>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<div class="box">
			<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner">
					<div class="carousel-item active">
						
						<div class="selector">
							<h1 class="packer"><b id="packname">Free</b>
									<div class="detail" id="pricentime">0€ <b>/</b> <span>month</span></div>
								</h1>
						</div>
						<div class="packageinfo">
							<div class="row">
								<div class="col">
									<h1><i class="fal fa-user-clock"></i> Max attack time</h1>
									<h2 id="dmaxdur">60s</h2>
								</div>
								<div class="col">
									<h1><i class="fal fa-layer-group"></i> Concurent</h1>
									<h2 id="dconcur">1</h2>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<h1><i class="fal fa-network-wired"></i> Network</h1>
									<h2 id="dnetwork">Free</h2>
								</div>
							</div>
						</div>
						<button class="purchase" id="purchase_plan"><i class="fad fa-shopping-bag"></i> Already owned</button>
						
					</div>
				<?php
					$getPlans=$pdo->prepare("SELECT * FROM `plans` WHERE `name`!='free' ORDER BY `price` ASC");
					$getPlans->execute();
					while($plan=$getPlans->fetch()){
						echo "
						<div class='carousel-item'>
						
							<div class='selector'>
								<h1 class='packer'><b id='packname'>".ucfirst($plan['name'])."</b>
										<div class='detail' id='pricentime'>".$plan['price']."€ <b>/</b> <span>month</span></div>
									</h1>
							</div>
							<div class='packageinfo'>
								<div class='row'>
									<div class='col'>
										<h1><i class='fal fa-user-clock'></i> Max attack time</h1>
										<h2 id='dmaxdur'>".$plan['maxtime']."s</h2>
									</div>
									<div class='col'>
										<h1><i class='fal fa-layer-group'></i> Concurent</h1>
										<h2 id='dconcur'>".$plan['concurents']."</h2>
									</div>
								</div>
								<div class='row'>
									<div class='col'>
										<h1><i class='fal fa-network-wired'></i> Network</h1>
										<h2 id='dnetwork'>".ucfirst($plan['network'])."</h2>
									</div>
								</div>
							</div>
							<button class='purchase' onclick='purchase(event)' id='purchase_plan'><i class='fad fa-shopping-bag'></i> Buy</button>
							
						</div>
						
						";
					}
				?>
				</div>
				<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
					<div class="arrow left myarrow" id="packleft"></div>
				</a>
				<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
					<div class="arrow right myarrow" id="packright"></div>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h1><i class="fad fa-layer-group"></i> Concurent</h1>
		<hr>
		<div class="box">
			<div class="selector">
				<h1 class="packer"><b>+1</b>
					<div class="detail">30€</div>
				</h1>
			</div>
			<button class="purchase" id="purchase_concurrents"><i class="fad fa-plus-octagon"></i> Unavailable</button>
		</div>
		</div>
		<div class="col">
		<h1><i class="fad fa-user-clock"></i> Max attack time</h1>
		<hr>
		<div class="box">
			<div class="selector">
				<h1 class="packer"><b>+200s</b>
						<div class="detail">15€</div>
					</h1>
			</div>
			<button class="purchase" id="purchase_duration"><i class="fad fa-plus-octagon"></i> Unavailable</button>
		</div>
		</div>
		</div>
		<h1><i class="fad fa-brackets-curly"></i> API</h1>
		<hr>
		<div class="box">
			<div class="selector">
				<h1 class="packer"><b>Accès API</b>
						<div class="detail">60€<b>/</b><span>month</span></div>
					</h1>
			</div>
			<button id="purchase_api" class="purchase"><i class="fad fa-layer-plus"></i> Unavailable</button>
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
		
			function purchase(e){
				let item = e.path[1]
				let plan = item.getElementsByTagName("b")[0].innerText
				console.log(plan)
				window.jQuery.post("function/buyPlan.php", { name: plan}, function (data) {
					if(data == "invalid params"){
						error("An error has occurred");
					} else if(data == "not login"){
						error("Authentication error, please reconnect.")
					} else if(data == "invalid plan"){
						error("We could not find this plan in our database.")
					} else if(data == "not better"){
						error("You already have a plan with more performance");
					} else if(data == "no money"){
						error("You don't have enough points to purchase this plan");
					} else if(data == "error"){
						error("An error occurred while modifying your profile");
					} else if(data == "success"){
						
					} else {
						console.log(data)
						error("An error has occurred")
					}
				})
			}
		</script>

<?php include("../include/footer2.php"); ?>