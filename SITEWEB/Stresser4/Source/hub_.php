<?php


	$page = "Panel";
	include 'header.php';



	if (!($user->hasMembership($odb)) && $testboots == 0) {

		header('location: index.php');

		exit;

	}

	

?>
			
		
			<main id="main-container" style="min-height: 404px;">
			<div class="content bg-white border-b">
			<script type="text/javascript">
				var auto_refresh = setInterval(
				function ()
				{
					$('#ta').load('ajax/user/tools/fuck.php?v=ta').fadeIn("slow");
					$('#ra').load('ajax/user/tools/fuck.php?v=ra').fadeIn("slow");
					$('#ts').load('ajax/user/tools/fuck.php?v=ts').fadeIn("slow");
					$('#tu').load('ajax/user/tools/fuck.php?v=my').fadeIn("slow");
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
					<div class="font-w700 text-gray-darker animated fadeIn">Your Attacks</div>
					<a class="h2 font-w300 text-primary animated flipInX" id="tu" href="#"></a>
				</div>
				</div>
		</div>

                <div class="content bg-gray-lighter">

                    <div class="row items-push">

                        <div class="col-sm-7">

                            <h1 class="page-heading">Panel <small> Launch and manage attacks</small>

                            </h1>

                        </div>

                        <div class="col-sm-5 text-right hidden-xs">

                            <ol class="breadcrumb push-10-t">

                                <li>Home</li>

                                <li><a class="link-effect" href="hub.php">Panel</a></li>

                            </ol>

                        </div> 

                    </div>

                </div>
				

                <div class="content content-narrow">    

                    <div class="row">  
					<?php
					if ($hub_status == 0) {
						
						$hub_x = $hub_rtime + $hub_time;
				
						
						if($hub_x < time())
						{
							$SQLinsert = $odb -> prepare("UPDATE `settings` SET `hub_status` = '1', `hub_reason` = '', `hub_time` = '' WHERE 1");
							$SQLinsert -> execute(array());
							die('
							<div class="modal-dialog modal-dialog-popout">
						<div class="modal-content">
							<div class="block block-themed block-transparent remove-margin-b">
								<div class="block-header bg-primary-dark">
									<ul class="block-options">
										<li>
											<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
										</li>
									</ul>
									<h3 class="block-title">Stresser.club <i style="display: none;" id="manage2" class="fa fa-cog fa-spin"></i></h3>
								</div>
							<div class="block-content">
							<center>
							<h1>Hub is Enabled</h1>
							<h5>Refresh Page!</h5>
							</center>

							
							</div>
							</div>
						</div>
					</div>
							
							');
						}
						?>
					<meta http-equiv="refresh" content="<?php echo $hub_time; ?>">
					<div class="modal-dialog modal-dialog-popout">
						<div class="modal-content">
							<div class="block block-themed block-transparent remove-margin-b">
								<div class="block-header bg-primary-dark">
									<ul class="block-options">
										<li>
											<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
										</li>
									</ul>
									<h3 class="block-title">Stresser.club <i style="display: none;" id="manage2" class="fa fa-cog fa-spin"></i></h3>
								</div>
							<div class="block-content">
							<center>
							<h1>Hub is disabled</h1>
							<h5><?php echo $hub_reason; ?></h5>
							<h5>Please wait <?php  echo $hub_time; ?> seconds</h5>
							</center>

							
							</div>
							</div>
						</div>
					</div>
					<?php
					} else {
						header('location: hub.php');
					}
					
					?>
					

			
<?php



	include 'footer.php';



?>