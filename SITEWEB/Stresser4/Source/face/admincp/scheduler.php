<?php 
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
                        <li class="active"><a href="scheduler.php"><?=$ln['skts']?></a></li>
                        <li class=""><a href="notification.php"><?=$ln['notf']?></a></li>
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
                     <div class="col-md-6">
                         <div class="adddatahere">
                         </div>
                     </div>
                     <div class='col-md-6'>
                         <form role="form" id="imageform" method="post" action='ajax.php?step=addtask2'>
                            <div class="form-group">
                              <h3><?=$ln['posts_ids']?> :</h3>
                              <input type="text" name="posts" class="form-control"/>
                            </div>
                             <div class="form-group">
                              <h3><?=$ln['share_time']?> :</h3>
                              <input type="number" name="share_time" class="form-control"/>
                            </div>
                            <div class="form-group">
                              <h3><?=$ln['count_chose']?> :</h3>
                              <p><a href="#" class="selectall"><?=$ln['select_all']?></a>
                                  <span>|</span>
                                  <span><a href="#"  class="unselectall"><?=$ln['unselect']?></a></span></p>
                              <select name="count" class="form-control selectpicker dropup show-menu-arrow text-left" multiple data-live-search="true"  style="direction: ltr;">
                                  <?php
                                            $ccCode = $ST->countreCode();
                                            for($i=0;$i<count($ccCode);$i++){
                                                $c = strtolower($ccCode[$i]);
                                                    echo '<option value="'.$c.'" data-content="<img src=\''.$ST->get("url").'/dist/img/blank.gif\' class=\'flag flag-'.$c.'\'  style=\'direction: ltr;\' />'.$ST->getCName($c).'"></option>';
                                            }
                                  ?>
                              </select>
                            </div>
                             <div class="form-group">
                                <h3><?=$ln['gander']?></h3>
                                <select name="gander" class="form-control selectpicker2 dropup">
                                    <option value="male"><?=$ln['male']?></option>
                                    <option value="female"><?=$ln['female']?></option>
                                    <option value="two"><?=$ln['two']?></option>
                                </select>
                            </div>
                             <div class="form-group">
                                <h3><?=$ln['taskfor']?></h3>
                                <select name="taskfor" class="form-control selectpicker2 dropup">
                                    <option value="users"><?=$ln['users']?></option>
                                    <option value="pages"><?=$ln['pages']?></option>
                                </select>
                                 
                            </div>
                             <div class="form-group">
                                 <div class="alert alert-danger"><?=$ln['if_chose_page']?></div>  
                             </div>
                             <div class="form-group">
                                 <input type="submit" value="<?=$ln['add_share_task']?>"   class="btn btn-primary btnsubmit" />
                                 <div id='imageloadstatus' style='display:none'><img src='<?=$ST->get('url')?>/dist/img/bigloader.gif'></div>
                             </div>
                          </form>
                     </div>
            </div>
        </div>
            <div class="box">
                
                <div>
                    <?php
                    if(isset($_GET['step']) && $_GET['step'] == 'deluser' && isset($_GET['id'])){
                             $id = abs(intval($_GET['id']));
                             $TASK->setUid($id);
                            if($TASK->Delete())
                               echo '<div class="alert alert-success">'.$ln['del_user_success'].'</div>';
                         }
                    ?>
                </div>
                
                <div id="container">
                       <?php 
                       
                      

                       
                        $usersData = $TASK->getUsers(0,20,"where `type`='2' ");
                        if($usersData){
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                            $countMsg = ($u['count']=="all") ? $ln['all'] : $u['count'];
                                            if($u['gander']=="male")
                                                $ganderMsg = $ln['male'];
                                                elseif ($u['gander']=="female")
                                                $ganderMsg = $ln['female'];
                                            else {
                                                $ganderMsg = $ln['two'];
                                            }

                                            $taskforMsg = ($u['taskfor']=="users") ? $ln['users'] : $ln['pages'];
                                                
                                            if($u['isfinish']==0){
                                                $isFinshMsg = $ln['is_finish_not'];
                                                 $lbtn = ' <a href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/doTask.php?id='.$u['id'].'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\' class="btn btn-primary">'.$ln['cont_share'].'</a>';
                                            }else{
                                                 $isFinshMsg = $ln['finsihed'];
                                                 $lbtn = "";
                                            }
                                echo '<div class="panel panel-default item  user-item" id="'.$i.'">
                                        <ul class="list-group" >
                                             <li class="list-group-item text-right">#'.$u['id'].'</li>
                                             <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$u['posts'].'</li>
                                             <li class="list-group-item text-right" style="direction: rtl">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                             <li class="list-group-item text-right">'.date("Y-m-d H:i:s",$u['time']).'</li>
                                             <li class="list-group-item text-right">'.$ln['totalcount'].' : '.$u['totalcount'].'</li>
                                             <li class="list-group-item text-right">'.$ln['successed'].' : '.$u['successed'].'</li>
                                             <li class="list-group-item text-right">'.$ln['failed'].' : '.$u['failed'].'</li>
                                             <li class="list-group-item text-right">'.$isFinshMsg.'</li>
                                                <li class="list-group-item text-center">
                                                
                                                    <div class="btn-group">
                                                            <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                        '.$lbtn.'
                                                       
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
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap-select.min.js"></script>
                    <script src="<?=$ST->get("url")?>/dist/js/jquery.masonry.min.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/imagesloaded.pkgd.min.js"></script>

          <script>$(function(){
              
             imagesLoaded( '#container', function() {
                 $('#container').masonry({
                    itemSelector : '.item',
                    columnWidth : 240
                });
              });  
              
          
               $('.selectpicker').selectpicker();
               $('.selectpicker2').selectpicker();
               var selectedall = false;
               $(".selectall").click(function(){
                   $('.selectpicker').selectpicker('selectAll');
                   //alert($('select[name=count]').val());
                   selectedall = true;
               });
               $('.unselectall').click(function(){
                   $('.selectpicker').selectpicker('deselectAll'); 
                   //  alert($('select[name=count]').val());
                   selectedall = false;
               });
               $('input[type=submit]').on('click',function(){
                   $('#imageloadstatus').show();
                   
                   var $posts = $.trim($('input[name=posts]').val());
                   var $count = $.trim($('select[name=count]').val());
                   
                   var $gander = $.trim($('select[name=gander]').val());
                   var $taskfor = $.trim($('select[name=taskfor]').val());
                   
                   var $share_time = $.trim($('input[name=share_time]').val());
                   
                   if(selectedall)
                       $count = "all";
                   
                   if($posts == "" || $count == "" || $gander == "" || $taskfor == "" || $share_time == ""){
                       alert("<?=$ln['all_important']?>");
                   }else{
                       $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=addtask2',
                        data: {'type':2,'posts':$posts,'count':$count,'gander':$gander,'taskfor':$taskfor,'share_time':$share_time},
                        success: function(data){
                         //if(data.st=='ok'){
                             $('.adddatahere').html(data);
                         //}else{
                         //}
                          $('#imageloadstatus').hide();
                        }/*,
                        dataType: 'json'*/
                      });
                   }
                   return false;
               });
                             $('.btnload-more').click(function(){
                  $('.loadmore-images').show();
                  
                  var $ID = parseInt($('.user-item:last').attr("id")) + 1;
                   $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=gettasks2',
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

          });</script>
          </body>
</html>
