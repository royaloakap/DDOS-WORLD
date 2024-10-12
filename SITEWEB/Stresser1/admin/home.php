<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Home";
	require_once 'header.php'; 
	
	/// Querys for the stats below
	$TotalUsers = $odb->query("SELECT COUNT(*) FROM `users`")->fetchColumn(0);
	$TodayAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()")->fetchColumn(0);
	$MonthAttack = $odb->query("SELECT COUNT(*) FROM `logs` WHERE date >= CURDATE()  - INTERVAL 30 DAY")->fetchColumn(0);
	$TotalAttacks = $odb->query("SELECT COUNT(*) FROM `logs`")->fetchColumn(0);
	$TotalPools = $odb->query("SELECT COUNT(*) FROM `api`")->fetchColumn(0);
	$TotalPayments = $odb->query("SELECT COUNT(*) FROM `payments`")->fetchColumn(0);
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$TotalYesBoots = $odb->query("SELECT COUNT(id) FROM `logs` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-2' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$totlalUsersauth = $odb->query("SELECT SUM(2auth) FROM `users` WHERE `2auth` = '1'")->fetchColumn(0);
	// Income Results
	$TodayIN = $odb->query("SELECT SUM(paid) FROM `payments` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-1' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$WeekIN = $odb->query("SELECT SUM(paid) FROM `payments` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-7' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$MonthIN = $odb->query("SELECT SUM(paid) FROM `payments` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-30' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$TotalIN = $odb->query("SELECT SUM(paid) FROM `payments`")->fetchColumn(0);
?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?></h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
          <ol class="breadcrumb">
            <li><a href="#"><?php echo $sitename; ?></a></li>
            <li class="active"><?php echo $page; ?></li>
          </ol>
        </div>
        <!-- /.col-lg-12 -->
      </div>
      <!-- .row -->
      
      <!--/.row -->
      <!-- .row -->
     
      <!--/.row -->
      <!-- .row -->
      <div class="row">
												<tbody>
													<?php
													$salesQuery = $odb->query("SELECT * FROM `payments` ORDER BY `id` DESC LIMIT 6");
													while($sales = $salesQuery->fetch(PDO::FETCH_BOTH)){
														$checkPlan = $odb->query("SELECT `name` FROM `plans` WHERE `ID` = '{$sales['plan']}'")->fetchColumn(0);
														$checkUser = $odb->query("SELECT `username` FROM `users` WHERE `ID` = '{$sales['user']}'")->fetchColumn(0);
														echo'
														<tr>
															<td class="font-w600">
																<a href="#">'. $checkPlan .' ('.$checkUser.')</a>
															</td>
															<td class="hidden-xs text-muted text-right" style="width: 200px;">'. date('d-m h:i:s a', $sales['date']) .'</td>
															<td class="font-w600 text-success text-right" style="width: 70px;">+ $'. $sales['paid'] .'</td>
														</tr>';
													}
													?>
												</tbody>
		


          </div>
        </div>
		<?php
			$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
			$plansql -> execute(array(":id" => $_SESSION['ID']));
			$row = $plansql -> fetch(); 
			$date = date("m-d-Y, h:i:s a", $row['expire']);
			if (!$user->hasMembership($odb)){
				$row['mbt'] = 0;
				$row['concurrents'] = 0;
				$row['name'] = 'No membership';
				$date = '0-0-0';
			}
		?>
		<div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Статистика стрессера</h3>
            <div class="weather-box">
              <div class="weather-info">
                <div class="row">
                  <div class="col-xs-12 p-r-10">
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Всего юзеров</p>
                        <p class="pull-right font-bold"><?php echo $TotalUsers; ?></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Всего запусков атак</p>
                        <p class="pull-right font-bold"><?php echo $TotalAttacks; ?></p>
                      </div>
                    </div>
					<div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Всего апи с методами</p>
                        <p class="pull-right font-bold"><?php echo $TotalPools; ?></p>
                      </div>
                    </div>
					
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>