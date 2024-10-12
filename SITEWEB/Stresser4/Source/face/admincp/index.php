<?php 
/*
 * <div class="row box" style="width:1002px;direction: rtl">
                <div class="col-md-6">
                    <h4>الاصدار الثاني</h4>
                    <h4><a href="http://baha2.in/">Baha'a Odeh</a></h4>
                    <h4><a href="http://hloun.com">Hloun.com</a></h4>
                </div>
                <div class="col-md-6">
                    <h4>سكربت ليون بوست</h4>
                    <h4>برمجة</h4>
                    <h4>الموقع الرسمي</h4>
                </div>        
            </div>
 */
include_once 'inc.php'; ?>
<html>
    <head>
       <meta charset="utf-8"/>
       <title><?=$ST->get("title")?> - Admin panel</title>
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap-theme.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/style.css">
              <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/flags.css">

        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style>
            
        </style>
    </head>
    <body>
        
        <div class="container ">
            <div style="direction: rtl;">
                <span style="font-size: 20px;font-weight: bold;"><?=$ln['hello']?> <?=$adminName?></span>
                <a class="text-left pull-left" href="logout.php" style="width:80px;margin-top: 10px;margin-left: 10px;"><?=$ln['logout']?></a>
             <div class="clearfix"></div>
            </div>
           <div class="row box" style="width:1002px;">
               <div class="col-md-3" onclick="window.location='settings.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-settings.png')">
                    <span class="ht-title"><?=$ln['settings']?></span>
                </div>
               <div class="col-md-3" onclick="window.location='fb.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-facebook.png')">
                    <span class="ht-title"><?=$ln['fbappsettings']?></span>
                </div>
               <div class="col-md-3" onclick="window.location='users.php';"  style="background:url('<?=$ST->get("url")?>/dist/img/icon-user.png')">
                    <span class="ht-title"><?=$ln['users']?></span>
                </div>
               <div class="col-md-3" onclick="window.location='pages.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-pages.png')">
                    <span class="ht-title"><?=$ln['pages']?></span>
                </div>
            </div>
            <div class="row box" style="width:1002px;">
                
               <div class="col-md-3" onclick="window.location='posts.php';"  style="background:url('<?=$ST->get("url")?>/dist/img/icon-posts.png')">
                    <span class="ht-title"><?=$ln['posts']?></span>
                </div>
                <div class="col-md-3" onclick="window.location='sharepost.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-share.png')">
                    <span class="ht-title"><?=$ln['sharepost']?></span>
                </div>
               <div class="col-md-3" onclick="window.location='notification.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-notf.png')">
                    <span class="ht-title"><?=$ln['notf']?></span>
                </div>
               <div class="col-md-3" onclick="window.location='scheduler.php';" style="background:url('<?=$ST->get("url")?>/dist/img/icon-skt.png')">
                    <span class="ht-title"><?=$ln['skt']?></span>
                </div>
            </div>
            <br />
			<div class="row box" style="width:1002px;">
							<div class="col-md-12">
								<table style="width:100%;padding:10px;text-align:right">
									<tr>
										<td>الاصدار الثاني</td>
										<td>سكربت ليون بوست</td>
									</tr>
									<tr>
										<td><a href="http://baha2.in/">Baha'a Odeh</a></td>
										<td>برمجة</td>
									</tr>
									<tr>
										<td><a href="http://hloun.com">Hloun.com</a></td>
										<td>الموقع الرسمي</td>
									</tr>
									<tr>
										<td><input type='text' class='form-control' value='wget <?=$ST->get('url')?>/admincp/corn.php'/></td>
										<td>كود الكورن</td>
									</tr>
									<tr>
										<td><input type='text' class='form-control' value='wget <?=$ST->get('url')?>/admincp/autoshare.php'/></td>
										<td>كود النشر الالي</td>
									</tr>
									<tr>
										<td><a href="<?=$ST->get('url')?>/admincp/backup.php">من هنا</a></td>
										<td>نسخة من القاعدة <span>مرة واحدة يوميا</span></</td>
									</tr>
									<tr>
										<td><a  href="http://hloun.org/forum/">من هنا</a></td>
										<td>منتدى الدعم الفني</td>
									</tr>
								</table>
							</div>

			</div>            
             <div class="row box" style="width:1002px;">
             	<div class="col-md-12" style="padding: 20px">
             		<iframe src="http://www.hloun.com/api/hlounpost.php" style="width:100%;" class="ad" frameborder="no" ></iframe>
             	</div>
             </div>
            <br />
            
            <div class="row box" style="width:1002px;">
                <div class="col-md-12" style="padding: 20px">
                    <div id="chart_div" style="width: 100%; height: 500px;"></div>
                </div>
                <div class="col-md-12" style="padding: 20px">
                    <div class="row" style="max-height: 300px;overflow:auto;">
                         <?php 
          $ccCode = $ST->countreCode();
          for($i=0;$i<count($ccCode);$i++){
              $c = strtolower($ccCode[$i]);
              
              $count = $USERS->cCount($c);
              
              if($count){
                 // echo "['".$c."', ".$count."],\n";
                  echo '<div class=" col-md-4"><img src="'.$ST->get("url").'/dist/img/blank.gif" class="flag flag-'.$c.'" alt="" /> '.$ST->getCName($c).' : '.$count.' </div>';
              }
          }
          
          ?>
                       
                    </div>
                </div>
                
            </div>
            <div class="row box" style="width:1002px;">
                <div class="col-md-6">
                     <div id="piechart_3d" style="width: 100%; height: 300px;"></div>
                </div>
                <div class="col-md-6">
                     <div id="piechart_3d2" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
            
            <?=COPYRIGHT?>
        </div>
        
        
          <script src="https://code.jquery.com/jquery.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
          <script type='text/javascript' src='https://www.google.com/jsapi'></script>
          <script src="https://raw.github.com/house9/jquery-iframe-auto-height/master/release/jquery.browser.js"></script>
    <script type='text/javascript'>
    
    $(function(){
    	$('iframe.ad').iframeAutoHeight();
    });
    
     google.load('visualization', '1', {'packages': ['geochart,corechart']});
     google.setOnLoadCallback(drawVisualization);
     
     function drawVisualization() {
               drawRegionsMap();
                drawChart2();
                drawChart3();

            }
      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          ['Country', 'Popularity'],
          <?php 
          $ccCode = $ST->countreCode();
          for($i=0;$i<count($ccCode);$i++){
              $c = strtolower($ccCode[$i]);
              
              $count = $USERS->cCount($c);
              
              if($count){
                  echo "['".$c."', ".$count."],\n";
              }
          }
          
          ?>
          ['Palstinea',500]
        ]);

        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
        chart.draw(data, options);
        
        
        
     // google.setOnLoadCallback(drawChart2);
      
    };
    
    function drawChart2() {
        var data1 = google.visualization.arrayToDataTable([
          ['Task', '<?=$ln['prs_meal_femal']?>'],
          ['<?=$ln['male']?>',     <?=$USERS->cCountMF('m')?>],
          ['<?=$ln['female']?>',       <?=$USERS->cCountMF('f')?>]
         ]);

        var options1 = {
          title: '<?=$ln['prs_meal_femal']?>',
          is3D: true,
        };

        var chart1 = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart1.draw(data1, options1);
      }
      
       function drawChart3() {
        var data1 = google.visualization.arrayToDataTable([
          ['Task', '<?=$ln['prs_meal_femal']?>'],
          ['<?=$ln['stil_active']?>',     <?=$USERS->cCounAcOrNot(1)?>],
          ['<?=$ln['not_active']?>',      <?=($USERS->cCounAcOrNot(2)!='')  ? $USERS->cCounAcOrNot(2) : 0; ?>]
         ]);

        var options1 = {
          title: '<?=$ln['who_still_active_or_not']?>',
          is3D: true,
        };

        var chart1 = new google.visualization.PieChart(document.getElementById('piechart_3d2'));
        chart1.draw(data1, options1);
      }
    </script>
    </body>
    
</html>
