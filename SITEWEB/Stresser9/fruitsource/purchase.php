<?php 
include "controls/database.php";
$page = "Purchase";
include ("head.php");
?>
<body>
		<?php include "header.php"; ?>
	
		<div class="container-fluid content">
		<div class="row">
				
			<?php include "side.php"; ?>
			<!-- end: Main Menu -->		
			<!-- start: Content -->
			<div class="col-md-10 col-sm-11 main ">
			

			<div class="row">
				
				<div class="col-md-12 col-sm-12 main " style="min-height: 335px;">

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Fruit -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-7019508263308016"
     data-ad-slot="3001220489"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

<center><iframe width="350" height="215" src="https://www.youtube.com/embed/1dNDCEUS5d8?rel=0&amp;controls=0&amp;showinfo=0" frameborder="3" style="border:4px solid #222;" allowfullscreen=""> </iframe><iframe width="350" height="215" src="https://www.youtube.com/embed/wqf2lQKAsd4?rel=0&amp;controls=0&amp;showinfo=0" frameborder="3" style="border:4px solid #222;" allowfullscreen=""> </iframe><iframe width="350" height="215" src="https://www.youtube.com/embed/FiuEIQERQEw?rel=0&amp;controls=0&amp;showinfo=0" frameborder="3" style="border:4px solid #222;" allowfullscreen=""></iframe></center>
<br>
				<center><b><h3> after purchasing a plan open a ticket if you do not received within 10 minutes </h3></b></center>
<center><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Telegram_logo.svg/100px-Telegram_logo.svg.png" Height="50"; width="50";> <center><b> https://telegram.me/fruitstresser </b></center>
<br>

			
			<div class="row">
				<?php
				  $SQLSelect = $odb -> query("SELECT * FROM `api` ORDER BY `ID` ASC");
				  while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
				  {
					$rowID = $show['ID'];
				?>
				<?php
				  }
				  ?>
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="boxchart-overlay blue">
							<div class="boxchart">5,6,7,2,0,4,2,4,8,2,3,3,2</div>
						</div>
						<span class="value"><?php echo $rowID ?></span>	
						<span class="title">Total Servers Online</span>
							
					</div>
				</div><!--/col-->
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="linechart-overlay red">
							<div class="linechart">1,2,6,4,0,8,2,4,5,3,1,7,5</div>
						</div>
						<span class="value"><?php echo $stats -> totalUsers($odb); ?></span>	
						<span class="title">New Members</span>						
					</div>
				</div><!--/col-->

				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="linechart-overlay green">
							<div class="linechart">1,2,6,4,0,8,2,4,5,3,1,7,5</div>
						</div>
						<span class="value"><?php echo $stats -> totalBoots($odb); ?></span>	
						<span class="title">Global Stress</span>						
					</div>
				</div><!--/col-->
				
				<div class="col-lg-3 col-sm-6 col-xs-6 col-xxs-12">
					<div class="smallstat">
						<div class="linechart-overlay blue">
							<div class="linechart">1,2,6,4,0,8,2,4,5,3,1,7,5</div>
						</div>
						<span class="value"><?php echo $stats -> runningBoots($odb); ?>/<?php echo $maxBootSlots;?></span>	
						<span class="title">Running Attacks</span>						
					</div>
				</div><!--/col-->

			</div><!--/row-->


			<div class="row">
				<!-- start: New Type Table -->
				<div class="price-table-overlay">	

					<div class="price-table type2 four">
						<?php
										$newssql = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
										while($row = $newssql ->fetch()) {
											
									?>	
						<ul>
							<li class="header"><?php echo $row['name'] ?></li>
							<li class="price"><span>$</span><?php echo $row['price'] ?></li>
							<li><span><?php echo $row['mbt'] ?></span> Seconds</li>
							<li><span><?php echo $row['con'] ?></span> Concurrent</li>
							<li><span><?php echo $row['length'] ?></span> <?php echo $row['unit'] ?></li>
							<li>Instant Activation</li>
							<li>Access All Tools</li>
							<li>Available Support</li>
							<li class="select"><a href="http://paypal.me/MrUnknown01">select plan</a></li>
						</ul>
					<?php
					}
					?>
					</div>
				</div>
				<!-- end: New Type Table -->									

			</div><!--/row-->		
			
      
					
			</div>
				</div><!--/col-->	
		</div>
	</div>
	<div class="clearfix"></div>
	
	<?php include "footer.php"; ?>
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="assets/js/jquery-ui.min.js"></script>
	<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/jquery.sparkline.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="assets/js/jquery.flot.min.js"></script>
	<script src="assets/js/jquery.flot.pie.min.js"></script>
	<script src="assets/js/jquery.flot.stack.min.js"></script>
	<script src="assets/js/jquery.flot.resize.min.js"></script>
	<script src="assets/js/jquery.flot.time.min.js"></script>
	<script src="assets/js/jquery.flot.spline.min.js"></script>
	<script src="assets/js/jquery.autosize.min.js"></script>
	<script src="assets/js/jquery.placeholder.min.js"></script>
	<script src="assets/js/moment.min.js"></script>
	<script src="assets/js/daterangepicker.min.js"></script>
	<script src="assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/dataTables.bootstrap.min.js"></script>
	<script src="assets/js/raphael.min.js"></script>
	<script src="assets/js/morris.min.js"></script>
	<script src="assets/js/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="assets/js/uncompressed/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/js/uncompressed/gdp-data.js"></script>
	<script src="assets/js/gauge.min.js"></script>
	
	<!-- theme scripts -->
	<script src="assets/js/custom.min.js"></script>
	<script src="assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/57b8ab1c0934485f5bf65f95/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>