<!-- start: Main Menu -->
			<div class="sidebar col-md-2 col-sm-1 ">
								
				<div class="sidebar-collapse collapse">
					<?php
						if ($user->hasMembership($odb))
						{
					?>
					<div class="nav-sidebar title"><span>Account</span></div>
					
					<ul class="nav nav-sidebar">
						
						<li><a href="index.php"><i class="fa fa-dashboard"></i><span class="hidden-sm text"> Dashboard</span></a></li>	
						<li><a href="accountinfo.php"><i class="fa fa-lock"></i><span class="hidden-sm text"> Account Information</span></a></li>
						
					</ul>
					
					<div class="nav-sidebar title"><span>THE HUB</span></div>
					
					<ul class="nav nav-sidebar">
						<li><a href="boot.php"><i class="fa fa-hdd-o"></i><span class="hidden-sm text"> Attack Hub [FREE]</span></a></li>
						<li><a href="bootv.php"><i class="fa fa-hdd-o"></i><span class="hidden-sm text"> Attack Hub [VIP]</span></a></li>
						<!--<li><a href="iplogger.php"><i class="fa fa-flash"></i><span class="hidden-sm text"> IP Logger</span></a></li>-->
						<!--<li><a href="portscan.php"><i class="fa fa-check"></i><span class="hidden-sm text"> Port Scan</span></a></li>-->
						<li><a href="isitup.php"><i class="fa fa-fire"></i><span class="hidden-sm text"> IsItUp?</span></a></li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-cogs"></i><span class="hidden-sm text"> Tools</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="skype.php"><i class="fa fa-skype"></i><span class="hidden-sm text"> Skype Bomber</span></a></li>
                                                                <li><a class="submenu" href="email.php"><i class="fa fa-asterisk"></i><span class="hidden-sm text"> Email Bomber</span></a></li>
								<li><a class="submenu" href="cloudflare.php"><i class="fa fa-cloud"></i><span class="hidden-sm text"> Cloudflare Resolver</span></a></li>
								<li><a class="submenu" href="geolocation.php"><i class="fa fa-globe"></i><span class="hidden-sm text"> Geolocation</span></a></li>
								<li><a class="submenu" href="fe.php"><i class="fa fa-asterisk"></i><span class="hidden-sm text"> Friends & Enemies</span></a></li>
							</ul>
							</li>
					</ul>
					<?php
						}
					?>
					<div class="nav-sidebar title"><span>Support</span></div>
					
					<ul class="nav nav-sidebar">
						
						<li><a href="support.php"><i class="fa fa-ticket"></i><span class="hidden-sm text"> Support Center</span></a></li>
						<li><a class="submenu" href="purchase.php"><i class="fa fa-money"></i><span class="hidden-sm text"> Purchase</span></a></li>
						<?php
							if ($user -> isAdmin($odb))
							{
						?>
						<li><a href="control/index.php"><i class="fa fa-user"></i><span class="hidden-sm text"> Admin Panel</span></a></li>
						<?php 
							}
						?>
					</ul>
				</div>
									
							</div>