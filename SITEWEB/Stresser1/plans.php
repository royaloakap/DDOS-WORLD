<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Тарифные планы";
	require_once 'header.php'; 
	
	/// Querys for the stats below
	if(isset($_POST['buyNow']))
	{
		$id = $_POST['buyNow'];
		$concs = $_POST['concurrents'];
		$api = $_POST['api'];
		header('Location: buy.php?id='.$id.'&concurrents='.$concs.'&api='.$api.'');
	}
	
	if(isset($_POST['buyNowCC']))
	{
		
		$_SESSION['CC_planID'] = $_POST['buyNowCC'];
		$_SESSION['CC_concurrents'] = $_POST['concurrents'];
		$_SESSION['CC_api'] = $_POST['api'];
		
		header('Location: cc/process.php');
	}
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
      <div class="row">
	     <div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title">Тарифы</h3>
            <table class="table table-striped table-vcenter">
								<thead>
									<tr>
										<th class="text-center" style="font-size: 12px;">Название</th>
										<th class="text-center" style="font-size: 12px;">Цена</th>
										<th class="text-center" style="font-size: 12px;">Время атаки</th>
										<th class="text-center" style="font-size: 12px;">Время действия</th>
										<th class="text-center" style="font-size: 12px;">Concurrents</th>
                                      	<th class="text-center" style="font-size: 12px;">Инструменты</th>
										<th class="text-center" style="font-size: 12px;">Покупка</th>
									</tr>
								</thead>
								<tbody style="font-size: 12px;" class="text-center">
								<?php
								$SQLGetPlans = $odb -> query("SELECT * FROM `plans` WHERE `private` = 0 ORDER BY `ID` ASC");
								while ($getInfo = $SQLGetPlans -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['ID'];
									$name = $getInfo['name'];
									$price = $getInfo['price'];
									$length = $getInfo['length'];
									$unit = $getInfo['unit'];
									$concurrents = $getInfo['concurrents'];
									$mbt = $getInfo['mbt'];
									$vip = $getInfo['vip'];
									
									
									echo '<h2><tr>
											<td>'.htmlspecialchars($name).'</td>
											<td>'.htmlentities($price).'₽</td>
											<td>'.htmlentities($mbt).'sec</td>
											<td>'.htmlentities($length).' '.htmlspecialchars($unit).'</td></h2>
											<td>'.htmlentities($concurrents).'</td>
                                            <td><button type="button" class="btn btn-outline btn-success btn-circle"><i class="fa fa-check"></i> </button></td>
											<td>
												<button type="button" class="btn btn-outline btn-warning btn-circle"><a href="https://t.me/maostress_bot"><i class="fa fa-shopping-basket"></i> </button></a>
											</td>
										  </tr>';
								
								?>
								<?php
									} 
								?>
								</tbody>
							</table>
          </div>
        </div>
		
		 <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
           <h3 class="box-title">Что вам нужно знать</h3>
                <dl>
                  <dt>Что такое время атаки?</dt>
                  <dd>Время атаки – это время, в течение которого вы можете атаковать одну цель. Вы можете послать флуд на X секунд</dd>
<br/>
                  <dt>Что такое время действия?</dt>
                  <dd>После того как вы приобрели план, вы сможете использовать его в течение определенного периода времени.</dd>
<br/>
                  <dt>Что такое параллелизм?</dt>
                  <dd>Параллельные атаки — это количество атак, которые вы можете запустить одновременно</dd>
<br/>
                  <dt>Что такое VIP?</dt>
                  <dd>VIP — это система на сайте, которая использует VIP-серверы вместо обычных серверов, что может повысить скорость вашей атаки.</dd>
<br/>
                  <dt>Сколько времени потребуется, чтобы получить мой план?</dt>
                  <dd>Среднее время ожидания плана составляет около 30 минут.</dd>
<br/>
                  <dt>Будет ли с меня списана плата по окончании периода подписки?</dt>
                  <dd>Нет. Вам придется вручную повторно приобрести тариф, если вы заинтересованы в сохранении вашего тарифного плана на сайте.</dd>
                </dl>
          </div>
        </div>
      </div>
									

      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>