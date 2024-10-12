<?php

	$page = "Servers";

	include 'header.php';

	

?>

<main id="main-container" style="min-height: 404px;"> 

	<div class="content bg-gray-lighter">

		<div class="row items-push">

			<div class="col-sm-8">

				<h1 class="page-heading">

					Servers <small><?php echo $sitename; ?> Servers</small>

				</h1>

			</div>

			<div class="col-sm-4 text-right hidden-xs">

				<ol class="breadcrumb push-10-t">

					<li>Home</li>

					<li><a class="link-effect" href="servers.php">Servers</a></li>

				</ol>

			</div>

		</div>

	</div>

	<div class="content content-narrow">

		<div class="row">  

			<div class="col-md-12">

				<div class="block">

					<div class="block-header bg-primary">

						<h3 class="block-title">Servers</h3>

					</div>

					<div class="block-content">
					
													<script type="text/javascript">
													var auto_refresh = setInterval(
													function ()
													{
													$('#live_servers').load('ajax/user/tools/servers.php').fadeIn("slow");
													}, 1000);
													</script>

					<div id="live_servers"></div>

					</div>

				</div>

			</div>
			

		</div>     

	</div>

</main>

<?php



	include 'footer.php';



?>