<?php

/**
 * NOTICE: BE CAREFUL WHEN EDIITNG THIS FILE
 * THIS FILE CONTAINS BOTH NAVIGATIONS TO THE
 * USER DASHBOARD AND THE ADMIN PANEL
 */

global $userInfo;

$currentPage = (isset($currentPage) ? $currentPage : "");
$adminPanel = (isset($currentPage) && substr($currentPage, 0, 6) === "admin_");

// User dashboard
if ( $adminPanel === false )
{
?>

			<div class="user-info">
				<br /><br />
					
						<center><img class="media-object" src="img/avatar.png" height="124" width="144" alt="user"></center><br>
					
					<h2 class="user-info__name"><?php echo htmlentities($_SESSION['username']); ?></h2>
					<h3 class="user-info__role"><?php echo ( isset($userInfo['name']) && !empty($userInfo['name']) ? $userInfo['name'] : "No Plan" ); ?></h3>
					<ul class="user-info__numbers">
						<li>
							<i class="pe-7f-user"></i>
							<p>Max Boot</p>
							<p><?php echo $userInfo['mbt']; ?></p>
						</li>
						<li>
							<i class="pe-7f-paper-plane"></i>
							<p>Con.</p>
							<p><?php echo ($userInfo['max_boots']+$userInfo['pboots']); ?></p>
						</li>
						<li>
							<i class="pe-7g-watch"></i>
							<p>Your Boots</p>
							<p><?php echo $stats -> totalBootsForUser($odb, $_SESSION['username']); ?></p>
						</li>
					</ul>
				</div> <!-- /user-info -->
				<ul class="main-nav">
					<li<?php echo ($currentPage === "dashboard" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="index.php">
							<span class="main-nav__icon"><i class="pe-7f-home"></i></span>
							Dashboard
						</a>
					</li>
					
				<li<?php echo ($currentPage === "purchase" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="purchase.php">
							<span class="main-nav__icon"><i class="pe-7f-note"></i></span>
							Purchase
						</a>
					</li>
					
				

					<li<?php echo ($currentPage === "booter" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="hub.php">
							<span class="main-nav__icon"><i class="pe-7f-gleam"></i></span>
							Attack 
						</a>
					</li>


						</a>
					</li>
					<li class="main-nav--collapsible<?php echo ($currentPage === "tools" ? ' main-nav--active' : ''); ?>">
						<a class="main-nav__link" href="tools.php">
							<span class="main-nav__icon"><i class="pe-7f-tools"></i></span>
							Tools <span class="badge badge--line badge--blue">2</span>
						</a>
						<ul class="main-nav__submenu">
						</ul>
					</li>

					<li<?php echo ($currentPage === "<?php echo URL; ?>support" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="support.php">
							<span class="main-nav__icon"><i class="pe-7f-browser"></i></span>
							Support Center 
						</a>
					</li>
					<li<?php echo ($currentPage === "news" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="terms.php">
							<span class="main-nav__icon"><i class="pe-7s-news-paper"></i></span>
							Terms
						</a>
					</li>
					<?PHP	if ($user -> isAdmin($odb))	{
				
				?>


						<li>
						<a class="main-nav__link" href="admin/index.php">
							<span class="main-nav__icon"><i class="pe-7f-note"></i></span>
							Administrator
						</a>
					</li>
                        <?php
						}
						?>
				</ul> <!-- /main-nav -->

<?php
}

// Administrator panel
else
{
?>
			
			<div class="user-info"><br /><br />
					<h2 class="user-info__name"><?php echo htmlentities($_SESSION['username']); ?></h2>
					<h3 class="user-info__role"><?php echo ( isset($userInfo['name']) && !empty($userInfo['name']) ? $userInfo['name'] : "No Plan" ); ?></h3>
					<ul class="user-info__numbers">
						<li>
							<i class="pe-7f-user"></i>
							<p>Max Boot</p>
							<p><?php echo $userInfo['mbt']; ?></p>
						</li>
						<li>
							<i class="pe-7f-paper-plane"></i>
							<p>Con.</p>
							<p><?php echo ($userInfo['max_boots']+$userInfo['pboots']); ?></p>
						</li>
						<li>
							<i class="pe-7g-watch"></i>
							<p>Your Boots</p>
							<p><?php echo $stats -> totalBootsForUser($odb, $_SESSION['username']); ?></p>
						</li>
					</ul>
				</div> <!-- /user-info -->

				<ul class="main-nav">
					<li<?php echo ($currentPage === "admin_index" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="index.php">
							<span class="main-nav__icon"><i class="pe-7f-home"></i></span>
							News
						</a>
					</li>
					<li<?php echo ($currentPage === "admin_user" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="edituser.php">
							<span class="main-nav__icon"><i class="pe-7f-edit"></i></span>
							Manage User
						</a>
					</li>
					<li<?php echo ($currentPage === "admin_support" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="support.php">
							<span class="main-nav__icon"><i class="pe-7f-browser"></i></span>
							Support Center 
						</a>
					</li>
					<li<?php echo ($currentPage === "admin_attacks" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="attacklogs.php">
							<span class="main-nav__icon"><i class="pe-7f-note"></i></span>
							Attack Logs
						</a>
					</li>
					<li<?php echo ($currentPage === "admin_logins" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="logins.php">
							<span class="main-nav__icon"><i class="pe-7f-clock"></i></span>
							Login History
						</a>
					</li>


					<li<?php echo ($currentPage === "admin_servers" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="servers_layer7.php">
							<span class="main-nav__icon"><i class="pe-7s-server"></i></span>
							Servers Layer7
						</a>
					</li>
					
						<li<?php echo ($currentPage === "admin_servers" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="servers_layer4.php">
							<span class="main-nav__icon"><i class="pe-7s-server"></i></span>
							Servers Layer4
						</a>
					</li>

					<li<?php echo ($currentPage === "admin_methods" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="methods.php">
							<span class="main-nav__icon"><i class="pe-7s-menu"></i></span>
							Methods Management
						</a>
					</li>
					
					<li<?php echo ($currentPage === "admin_sm" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="status.php">
							<span class="main-nav__icon"><i class="pe-7s-server"></i></span>
							Server and Methods
						</a>
					</li>
					
					<li<?php echo ($currentPage === "admin_utilities" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="utilities.php">
							<span class="main-nav__icon"><i class="pe-7s-tools"></i></span>
							Utilities
						</a>
					</li>
					

						<li<?php echo ($currentPage === "admin_plans" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="plans.php">
							<span class="main-nav__icon"><i class="pe-7f-note"></i></span>
							Manage Plans
						</a>
						</li>
						<li<?php echo ($currentPage === "admin_payments" ? ' class="main-nav--active"' : ''); ?>>
						<a class="main-nav__link" href="payments.php">
							<span class="main-nav__icon"><i class="pe-7f-config"></i></span>
							Payments
						</a>
						</li>
						<li>
						<a class="main-nav__link" href="../index.php">
							<span class="main-nav__icon"><i class="pe-7f-note"></i></span>
							Return Local
						</a>
					</li>	
				</ul> <!-- /main-nav -->
				
				<!-- i hate this theme ahahhahaha --->
				<div style="height:250px;"></div>
			
<?php
}
