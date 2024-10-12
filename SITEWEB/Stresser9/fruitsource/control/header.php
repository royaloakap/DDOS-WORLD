<!-- start: Header -->
	<div class="navbar" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar">a</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="../index.php"><img src="../assets/img/logo.png"> <?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['bootername'];
				}
			?></a>
	        </div>
	        <ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
	        		<a href="index.html#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-comments-o"></i><span class="badge"><?php echo getUsersOpenedTickets(); ?></span></a>
	      		</li>
				<li><a href="../accountinfo.php"><i class="fa fa-cog"></i></a></li>
				<li><a href="../logout.php"><i class="fa fa-power-off"></i></a></li>
				<li class="dropdown">
	        		<a href="#" class="dropdown-toggle avatar" data-toggle="dropdown"><img src="../assets/img/avatar.jpg"></a>
	      		</li>
			</ul>
		</div>
	</div>
	<!-- end: Header -->