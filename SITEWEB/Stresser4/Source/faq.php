<?php $page = "FAQ";include 'header.php'; ?>

<main id="main-container" style="min-height: 404px;">
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-7">
				<h1 class="page-heading">FAQ <small>Check out answers to the most common questions</small></h1>
			</div>
			<div class="col-sm-5 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Home</li><li><a class="link-effect" href="faq.php">FAQ</a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="content content-boxed">
		<div class="block animated zoomInLeft">
			<div class="block-content block-content-full block-content-narrow">
				<h2 class="h3 font-w600 push-30-t push">Still need an answer? <a href="support.php">Click here</a></h2>
				<?php
				
				$i = 1;
				$SQLGetFAQ = $odb -> query("SELECT * FROM `faq` ORDER BY `id` DESC");
				while ($getInfo = $SQLGetFAQ -> fetch(PDO::FETCH_ASSOC)){
					$question = $getInfo['question'];
					$answer = $getInfo['answer'];
	
				?>
					<div id="faq<?php echo $i; ?>" class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#faq<?php echo $i; ?>" href="#faq<?php echo $i; ?>_q<?php echo $i; ?>" aria-expanded="false"><?php echo $question; ?></a>
								</h3>
							</div>
							<div id="faq<?php echo $i; ?>_q<?php echo $i; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
								<div class="panel-body">
									<?php echo $answer; ?>
								</div>
							</div>
						</div>                               
					</div>   
				<?php
				
				$i++;
				}		
				
				?>
			</div>
		</div>
	</div>
</main>		
	
<?php include 'footer.php'; ?>