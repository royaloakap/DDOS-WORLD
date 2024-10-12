<?php 
//ini_set('max_execution_time', 0); 
include_once 'inc.php'; 
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
            .item.w2 { width: 50%; }

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
                        <li class=""><a href="notification.php"><?=$ln['notf']?></a></li>
                        <li class=""><a href="sharepost.php"><?=$ln['sharepost']?></a></li>
                        <li class=""><a href="posts.php"><?=$ln['posts']?></a></li>
                        <li class="active"><a href="pages.php"><?=$ln['pages']?></a></li>
                        <li class=""><a href="users.php"><?=$ln['users']?></a></li>
                        <li class=""><a href="fb.php"><?=$ln['fb']?></a></li>
                        <li class=""><a href="settings.php"><?=$ln['settings']?></a></li>
                     </ul>
                </div>
                
                <div class="navbar-header" style='max-width: 100px;'>
                    <ul class="nav navbar-nav">
                        <li class=""><a href="logout.php"><?=$ln['exit']?></a></li>
                    </ul>
                </div>
            </nav>
            <div class='box'>
                <div class='row' style='padding: 10px;direction: rtl'>
                    <div class='col-md-12'>
                        <?php 
                         if(isset($_GET['step']) && $_GET['step']=='delnotactive'){
                             if($PAGES->delNotActive()){
                                 echo '<div class="alert alert-success">'.$ln['del_success'].'</div>';
                             }
                         }else if(isset($_GET['step']) && $_GET['step'] == 'deluser' && isset($_GET['id'])){
                             $id = abs(intval($_GET['id']));
                             $PAGES->setUid($id);
                            if($PAGES->Delete())
                               echo '<div class="alert alert-success">'.$ln['del_user_success'].'</div>';
                         }
                        ?>
                         <h3><?=$ln['user_count']?> :  <?=$ST->count('pages');?>
                             <span class="pull-left"><a class="btn btn-warning btn-del" href="?step=delnotactive"><?=$ln['del_not_active_user']?></a></span>
                         <div class="clearfix"></div>
                         </h3>
                         
                         
                         <br />
                         
                          
                    </div>
                </div>
                <div id="container">
                       <?php 
                        $usersData = $PAGES->getUsers(0,20);
                        if($usersData){
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                //$USERS->setPrimary('fb_id');
                                $USERS = new User();
                                $USERS->setTable('users');
                                $USERS->setUid($u['user_id']);
                                 
                                
                                $last_share = ($u['last_share'] == '1') ? $ln['last_success'] : $ln['last_error'];
                                echo '<div class="panel panel-default item user-item" id="'.$i.'">
                                            <img src="https://graph.facebook.com/'.trim($u['page_id']).'/picture?type=large" style="width:100%;"/>
                                             <ul class="list-group" >
                                                <li class="list-group-item text-right">'.trim($u['page_name']).'</li>
                                                <li class="list-group-item text-right">'.$USERS->getValue('fb_name').'</li>
                                                <li class="list-group-item text-right">'.$last_share.'</li>
                                                <li class="list-group-item text-center">
                                                    <div class="btn-group">
                                                      <a type="button" class="btn btn-default" href="http://facebook.com/'.trim($u['page_id']).'" target="_blank">'.$ln['facebook'].'</a>
                                                      <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                    </div>
                                                 </li>    
                                              </ul>
                                       </div>';
                            }
                        }
                        ?>
                    
                     <div class="clearfix"></div>
                     
                    
                  </div>
                <center style="padding: 10px"><a class="btn btn-primary btnload-more" href="#"><?=$ln['load_more']?> <img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader loadmore-images' style="margin-top:3px"/></a></center>
                
                
        </div>
        <script>ajaxFile='<?=$ST->get("url")?>/admincp/ajax.php';</script>
          <script src="https://code.jquery.com/jquery.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/jquery.masonry.min.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/imagesloaded.pkgd.min.js"></script>
          <script>
          $(function(){
              imagesLoaded( '#container', function() {
                 $('#container').masonry({
                    itemSelector : '.item',
                    columnWidth : 240
                });
              });  
              
              
              $('.btnload-more').click(function(){
                  $('.loadmore-images').show();
                  
                  var $ID = parseInt($('.user-item:last').attr("id")) + 1;
                   $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=getpages',
                        data: {'user_start':$ID},
                        success: function(data){
                         if(data.st=='ok'){
                             $('.user-item:last').after(data.text);
                             imagesLoaded( '#container', function() {
                                    $('#container').masonry('reload');
                                 });  
                         }else{
                              $('.btnload-more').remove();
                         }
                          $('.loadmore-images').hide();
                        },
                        dataType: 'json'
                      });
                  
                  
                  return false;
              });
              
              $('.btn-del').click(function(){
              
                var r=confirm("<?=$ln['are_you_sure_del_not_active']?>");
                if (r==true)
                  {
                      return true;
                  }
                else
                  {
                      return false;
                  }
                
                
              });
          });
          </script>
          </body>
</html>