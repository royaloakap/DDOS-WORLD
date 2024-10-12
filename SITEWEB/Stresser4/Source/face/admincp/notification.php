<?php 
ini_set('max_execution_time', 0); 
include_once 'inc.php'; 
require '../src/facebook.php';
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
?>
<html>
    <head>
       <meta charset="utf-8"/>
       <title><?=$ST->get("title")?> - Admin panel</title>
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap-theme.min.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/style.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/flags.css">
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/bootstrap-select.min.css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style>
            .item {
                width: 220px;
                margin: 10px;
                float: left;
              }
        </style>
            </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-inverse" role="navigation">
                
                <div class="navbar-header pull-right">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                          </button>
                    <a class="navbar-brand" href="index.php"><?=$ln['home']?></a>
                    
                </div>
                 <div class="navbar-collapse collapse pull-right">
                     <ul class="nav navbar-nav">
                        <li class=""><a href="scheduler.php"><?=$ln['skts']?></a></li>
                        <li class="active"><a href="notification.php"><?=$ln['notf']?></a></li>
                        <li class=""><a href="sharepost.php"><?=$ln['sharepost']?></a></li>
                        <li class=""><a href="posts.php"><?=$ln['posts']?></a></li>
                        <li class=""><a href="pages.php"><?=$ln['pages']?></a></li>
                        <li class=""><a href="users.php"><?=$ln['users']?></a></li>
                        <li class=""><a href="fb.php"><?=$ln['fb']?></a></li>
                        <li class=""><a href="settings.php"><?=$ln['settings']?></a></li>
                     </ul>
                </div>
                
                <div class="navbar-header" style='max-width: 100px; '>
                    <ul class="nav navbar-nav">
                        <li class=""><a href="logout.php"><?=$ln['exit']?></a></li>
                    </ul>
                </div>
            </nav>
             <div class='box'>
                 <div class='row' style='padding: 10px;direction: rtl;'>
                     <div class="col-md-12">
                         <div class="alert alert-warning"><?=$ln['notf_msgb']?></div>

                     </div>
                     <div class="col-md-6">
                         <div class="adddatahere">
                         </div>
                     </div>
                     <div class='col-md-6'>
                         <form role="form" id="imageform" method="post" action='notification.php'>
                             <div class="form-group">
                              <h3><?=$ln['notf_msg']?> :</h3>
                              <input type="text" name="notf_msg" class="form-control"/>
                            </div>
                             <div class="form-group">
                              <h3><?=$ln['notf_link']?> :</h3>
                              <input type="text" name="notf_link" class="form-control"/>
                              </div>
                             <div class="form-group" style="margin-right: 30px">
                                  <input type="submit" name="sumb"  value="<?=$ln['share_notf']?>" class="btn btn-primary"/>
                            </div>
                         </form>
                     </div>
                     <div class="col-md-12">
                         <?php 
                         if(isset($_POST['notf_msg']) && isset($_POST['notf_link'])){
                             $config = array();
                                $config['appId'] = $ST->get('app_id');
                                $config['secret'] = $ST->get('app_key');
                                $config['fileUpload'] = true; // optional
                                $facebook = new Facebook($config);
                                $app_access_token  = $ST->get('app_id').'|'.$ST->get('app_key');
                                $usersData = $USERS->getUsersNoLimit();  
                                 for($i=0;$i< count($usersData);$i++){
                                    $u = $usersData[$i];
                                    
                                    try{
                                   $add = $facebook->api('/'.$u['fb_id'].'/notifications','POST',array(
                                        'access_token'=>$app_access_token,
                                        'template'=>$_POST['notf_msg'],
                                        'ref'=>$_POST['notf_link']));
                                    }  catch (FacebookApiException $e) {
                                        $add = false;
                                    }
                                    
                                     if($add){
                                          echo '<div class="alert alert-success">'.  str_replace("{user}", $u['fb_name'], $ln['share_sucess_to_user']).'</a></div>';
                                     }else{
                                          echo '<div class="alert alert-danger">'.  str_replace("{user}", $u['fb_name'], $ln['share_unsuccess_to_user']).'</div>';

                                     }
                                    flush();
                                     ob_flush();

                                 }
                         }
                         ?>
                         </div>
                     </div>
                 </div>
             </div>
    </body>
</html>