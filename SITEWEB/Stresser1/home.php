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
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	
	

?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?> <i style="display: none;" id="alerts" class="fa fa-cog fa-spin"></i></h4>
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
      <div class="row">
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Всего пользователей</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash"></div>
              </li>
              <li class="text-right"><i class="ti-arrow-up text-success"></i> <span class="counter text-success"><?php echo $TotalUsers; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Всего запусков</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash2"></div>
              </li>
              <li class="text-right"><i class="ti-arrow-up text-purple"></i> <span class="counter text-purple"><?php echo $TotalAttacks; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Запущенные атаки</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash3"></div>
              </li>
              <li class="text-right"><i class="ti-arrow-up text-info"></i> <span class="counter text-info"><?php echo $RunningAttacks; ?></span></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-sm-3 col-xs-12">
          <div class="white-box analytics-info">
            <h3 class="box-title">Всего апи с методами</h3>
            <ul class="list-inline two-part">
              <li>
                <div id="sparklinedash4"></div>
              </li>
              <li class="text-right"><i class="ti-arrow-down text-danger"></i> <span class="text-danger"><?php echo $TotalPools; ?></span></li>
            </ul>
          </div>
        </div>
      </div>
      <!--/.row -->
      <!-- .row -->
    <div id="alertsdiv" style="display:inline-block;width:100%"></div>
      <!--/.row -->
      <!-- .row -->

	   <div class="row">
	   
	          <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Новости</h3>
            <div class="steamline">
			<?php
						$newssql = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC LIMIT 5");
						while($row = $newssql ->fetch()){
							$ID = $row['ID'];
							$title = $row['title'];
							$content = $row['content'];
							echo 
							' <div class="sl-item">
                <div class="sl-right">
                  <div><a href="#">'.$title.'</a> </div>
                  <p>'.$content.'</p>
                </div>
              </div>';
						}
						?>
            </div>
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
		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Статистика аккаунта</h3>
            <div class="weather-box">
              <div class="weather-info">
                <div class="row">
                  <div class="col-xs-12 p-r-10">
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Никнейм</p>
                        <p class="pull-right font-bold"><?php echo $_SESSION['username']; ?></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Текущий тариф</p>
                        <p class="pull-right font-bold"><?php echo $row['name']; ?></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Максимальное время атаки</p>
                        <p class="pull-right font-bold"><?php echo $row['mbt']; ?></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Concurrents</p>
                        <p class="pull-right font-bold"><?php echo $row['concurrents']; ?></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <p class="pull-left">Время окончания тарифа</p>
                        <p class="pull-right font-bold"><?php echo $date; ?></p>
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
	  <script>

		alerts();

		function alerts() {
			document.getElementById("alertsdiv").style.display = "none";
			document.getElementById("alerts").style.display = "inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			}
			else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById("alertsdiv").innerHTML = xmlhttp.responseText;
					document.getElementById("alerts").style.display = "none";
					document.getElementById("alertsdiv").style.display = "inline-block";
					document.getElementById("alertsdiv").style.width = "100%";
					eval(document.getElementById("ajax").innerHTML);
				}
			}
			xmlhttp.open("GET","includes/ajax/user/alerts.php",true);
			xmlhttp.send();
		}
		</script>
<?php

	require_once 'footer.php';
	
?>