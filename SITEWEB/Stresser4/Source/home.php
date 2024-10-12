<?php 
$page = "Dashboard";
include 'header.php'; 
?>



	<main id="main-container" style="min-height: 485px;">

		<div class="content bg-image overflow-hidden" style="background-image: url('assets/img/photos/photo3@2x.jpg');">

			<div class="push-50-t push-15">

				<h1 class="h2 text-white animated zoomIn">Dashboard</h1>

				<h2 class="h5 text-white-op animated zoomIn">Welcome <?php echo htmlentities($_SESSION['username']); ?></h2>

			</div>

		</div>
		<div class="content bg-white border-b">
		<script type="text/javascript">
				var auto_refresh = setInterval(
				function ()
				{
					$('#ta').load('ajax/user/tools/fuck.php?v=ta').fadeIn("slow");
					$('#ra').load('ajax/user/tools/fuck.php?v=ra').fadeIn("slow");
					$('#ts').load('ajax/user/tools/fuck.php?v=ts').fadeIn("slow");
					$('#tu').load('ajax/user/tools/fuck.php?v=tu').fadeIn("slow");
					$('#my').load('ajax/user/tools/fuck.php?v=my').fadeIn("slow");
					$('#live_servers').load('ajax/user/tools/servers.php').fadeIn("slow");
				}, 1000);
			</script>
			<div class="row items-push text-uppercase">
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Total Attacks</div>
				<a class="h2 font-w300 text-primary animated flipInX" id="ta" href="#"></a>
			</div>
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Running Attacks</div>
				<a class="h2 font-w300 text-primary animated flipInX" id="ra" href="#"></a>
			</div>
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Total Servers</div>
				<a class="h2 font-w300 text-primary animated flipInX" id="ts" href="#"></a>
			</div>
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Total Users</div>
				<a class="h2 font-w300 text-primary animated flipInX" id="tu" href="#"></a>
			</div>
			</div>
		</div>

		<div class="content">                

			<div class="row">

				<div class="col-lg-8">

					<div class="block animated zoomIn">

						<div class="block-header">

							<ul class="block-options">

								<li>

									<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>

								</li>

							</ul>

							<h3 class="block-title text-center"> Updates</h3>

						</div>


						<div class="block-content">

						<?php

						$newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 5");

						while($row = $newssql ->fetch()){

							$id = $row['ID'];

							$color = $row['color'];

							$icon = $row['icon'];

							$title = $row['title'];

							$content = $row['content'];

							echo 

							'<ul class="list list-timeline pull-t">                                     

								<li class="push-5">

									<div class="list-timeline-time">Posted: '. date('d-m' ,$row['date']) .'</div>

									<i class="'. $icon .' '. $color .' list-timeline-icon"></i>

									<div class="list-timeline-content">

										<p class="font-w600">'. htmlentities($title) .'</p>

										<p class="font-s13">'. htmlentities($content) .'</p>

									</div>

								</li>

							</ul>';

						}

						?>

						</div>

					</div>

				</div>

				<div class="col-lg-4">

					<div class="content-grid">

						<div class="row">

							<div class="col-xs-12">

								<?php

								$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");

								$plansql -> execute(array(":id" => $_SESSION['ID']));

								$row = $plansql -> fetch(); 

								$date = date("m-d-Y, h:i:s a", $row['expire']);

								if (!$user->hasMembership($odb)){

									$row['mbt'] = 0;

									$row['concurrents'] = 0;

									$row['name'] = 'No membership';

									$date = 'No membership';

								}

								?>

								<a class="block block-link-hover2 animated zoomIn" href="products.php">

									<div class="block-header">

										<h3 class="block-title text-center"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>

									</div>

									<div class="block-content block-content-full text-center bg-image" style="background-image: url('assets/img/photos/photo2.jpg');">

										<div>

											<img class="img-avatar img-avatar96 img-avatar-thumb" src="assets/img/avatars/anon.jpg" alt="">

										</div>

										<div class="h5 text-white push-15-t push-5"><?php echo htmlspecialchars($_SESSION['username']); ?></div>            

										<div class="h5 text-white-op"><?php echo htmlspecialchars($row['name']); ?></div>

										<div class="h5 text-white-op"><?php echo $date; ?></div>

									</div>

									<div class="block-content">

										<div class="row items-push text-center">

											<div class="col-xs-6">

												<div class="push-5 text-gray-dark"><i class="si si-bar-chart fa-2x"></i></div>

												<div class="h5 font-w300 text-muted"><?php echo $row['concurrents']; ?> Concurrents</div>

											</div>

											<div class="col-xs-6">

												<div class="push-5 text-gray-dark"><i class="si si-equalizer fa-2x"></i></div>

												<div class="h5 font-w300 text-muted"><?php echo $row['mbt']; ?> Max Boot Time</div>

											</div>

										</div>

									</div>

									<div class="block-content">

										<div class="row items-push text-center">

											<div class="col-xs-12 text-gray-dark">

												<div class="push-5"><i class="si si-ghost fa-2x"></i></div>
												<p id="my" />
												
											</div>

											</div>

									</div>

							</div>

								</a>

						</div>

					</div>

				</div>
					
				<div class="col-md-12">
					<div class="block">
						<ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
						
							<li class="active">
								<a href="#total">Attacks Graph</a>
							</li>
							<li>
								<a href="#servers">Servers</a>
							</li>
						</ul>
						<div class="block-content tab-content">
							<div class="tab-pane" id="ticket">
								<div class="content" id="messages"></div>
							</div>
							
							<div class="tab-pane active" id="total">
								<div style="height: 374px;"><canvas id="myChart"></canvas></div>
							</div>
							<div class="tab-pane" id="servers">
								<div id="live_servers"></div>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>

	</main>
	
<?php
		$onedayago = time() - 86400;

		$twodaysago = time() - 172800;
		$twodaysago_after = $twodaysago + 86400;

		$threedaysago = time() - 259200;
		$threedaysago_after = $threedaysago + 86400;

		$fourdaysago = time() - 345600;
		$fourdaysago_after = $fourdaysago + 86400;

		$fivedaysago = time() - 432000;
		$fivedaysago_after = $fivedaysago + 86400;

		$sixdaysago = time() - 518400;
		$sixdaysago_after = $sixdaysago + 86400;

		$sevendaysago = time() - 604800;
		$sevendaysago_after = $sevendaysago + 86400;
		
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` > :date");
		$SQL -> execute(array(":date" => $onedayago));
		$count_one = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $twodaysago, ":after" => $twodaysago_after));
		$count_two = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $threedaysago, ":after" => $threedaysago_after));
		$count_three = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $fourdaysago, ":after" => $fourdaysago_after));
		$count_four = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $fivedaysago, ":after" => $fivedaysago_after));
		$count_five = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $sixdaysago, ":after" => $sixdaysago_after));
		$count_six = $SQL->fetchColumn(0);

		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `date` BETWEEN :before AND :after");
		$SQL -> execute(array(":before" => $sevendaysago, ":after" => $sevendaysago_after));
		$count_seven = $SQL->fetchColumn(0);
		
		$date_one = date('d/m/Y', $onedayago);
		$date_two = date('d/m/Y', $twodaysago);
		$date_three = date('d/m/Y', $threedaysago);
		$date_four = date('d/m/Y', $fourdaysago);
		$date_five = date('d/m/Y', $fivedaysago);
		$date_six = date('d/m/Y', $sixdaysago);
		$date_seven = date('d/m/Y', $sevendaysago);

		?>
<script>

			inbox();



			

			

			</script>
		
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    loadchart();

}, false);

// Loading the attack graph
function loadchart() {
	var ctx = $("#myChart").get(0).getContext("2d");
	
	var data = {
		labels: ["<?php echo $date_seven; ?>", "<?php echo $date_six; ?>", "<?php echo $date_five; ?>", "<?php echo $date_four; ?>", "<?php echo $date_three; ?>", "<?php echo $date_two; ?>", "<?php echo $date_one; ?>"],
		datasets: [
			{
				label: 'Last 7 days attacks',
				fillColor: 'rgba(44, 52, 63, .1)',
				strokeColor: 'rgba(44, 52, 63, .55)',
				pointColor: 'rgba(44, 52, 63, .55)',
				pointStrokeColor: '#fff',
				pointHighlightFill: '#fff',
				pointHighlightStroke: 'rgba(44, 52, 63, 1)',
				data: [<?php echo $count_seven; ?>, <?php echo $count_six; ?>, <?php echo $count_five; ?>, <?php echo $count_four; ?>, <?php echo $count_three; ?>, <?php echo $count_two; ?>, <?php echo $count_one; ?>]
			}
		]
	}

	var myNewChart = new Chart(ctx).Line(data, {
		scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		scaleFontColor: '#999',
		scaleFontStyle: '600',
		tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
		tooltipCornerRadius: 3,
		maintainAspectRatio: false,
		tooltipTemplate: "<%if (label){%><%=label%> - <%}%><%= value %> Attacks",
		responsive: true
	});
}
</script>



<?php

	

	include 'footer.php';



?>