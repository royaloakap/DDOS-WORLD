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
 */?>
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
            .item.w2 { width: 460px }
            .bootstrap-filestyle > input[type=text]{
                height:32px;
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
                        <li class=""><a href="notification.php"><?=$ln['notf']?></a></li>
                        <li class=""><a href="sharepost.php"><?=$ln['sharepost']?></a></li>
                        <li class="active"><a href="posts.php"><?=$ln['posts']?></a></li>
                        <li class=""><a href="pages.php"><?=$ln['pages']?></a></li>
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
                 <div class='row' style='padding: 10px;direction: rtl;'>
                     <div class="col-md-6">
                         <div class="adddatahere">
                             
                         </div>
                     </div>
                     <div class='col-md-6'>
                         <form role="form" id="imageform" method="post" enctype="multipart/form-data" action='ajax.php?step=addpost'>
                            <div class="form-group">
                              <h3><?=$ln['post_type']?> :</h3>
                              <select  class="form-control" name="posttype" style='height: 40px'>
                                  <option value="1" selected><?=$ln['text']?></option>
                                  <option value="2"><?=$ln['link']?></option>
                                  <option value="3"><?=$ln['image']?></option>
                              </select>
                            </div>
                            <div class="form-group">
                              <h3><?=$ln['post_text']?> :</h3>
                              <textarea  class="form-control" name="posttext" style="height:100px"></textarea>
                            </div>
                            <div class="form-group addtype ">                              
                            </div>
                             <div class="form-group">
                                 <input type="submit" value="<?=$ln['add_post']?>"   class="btn btn-primary btnsubmit" />
                                 <div id='imageloadstatus' style='display:none'><img src='<?=$ST->get('url')?>/dist/img/bigloader.gif'></div>
                             </div>
                          </form>

                     </div>
            </div>
        </div>
            <div class='box'>
                <div class='row' style='padding: 10px;direction: rtl'>
                    <div class='col-md-12'>
                        <?php 
                          if(isset($_GET['step']) && $_GET['step'] == 'deluser' && isset($_GET['id'])){
                             $id = abs(intval($_GET['id']));
                             $POSTS->setUid($id);
                            if($POSTS->Delete())
                               echo '<div class="alert alert-success">'.$ln['del_post_success'].'</div>';
                         }
                        ?>
                         <h3><?=$ln['posts_count']?> :  <?=$ST->count('posts');?></h3>
                         
                         
                         <br />
                    </div>
                </div>
                <div id="container">
                       <?php 
                        $usersData = $POSTS->getUsers(0,20);
                        if($usersData){
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                echo '<div class="panel panel-default item w2 user-item" id="'.$i.'">';
                                            if($u['type']==3)
                                                echo ' <img src="'.$ST->get('url').'/admincp/upload/'.$u['link'].'" style="width:100%;max-height:300px;"/>';
                                            echo '
                                                 
                                             <div class="panel-body">
                                                 <div class="well text-right" style="direction: rtl">'.stripslashes($u['text']).'</div>
                                             </div>
                                            <ul class="list-group" >';
                                            if($u['type']==2)
                                                echo ' <li class="list-group-item text-right"><a href="'.$u['link'].'">'.$u['link'].'</a></li>';
                                            echo'   <li class="list-group-item text-right" style="direction: rtl">'.cptime($u['date']).'</li>
                                                <li class="list-group-item text-right">'.$ln['id_to_share'].' : '.$u['id'].'</li>
                                                <li class="list-group-item text-center">
                                                
                                                    <div class="btn-group">
                                                      <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                      <a type="button" class="btn btn-default" href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/directshare.php?id='.$u['id'].'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\'>'.$ln['direct_share'].'</a>
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
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap-filestyle.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/jquery.form.min.js"></script>
          <script>
              
              function postLink(){
                  var data = "";
                  data +='<h3><?=$ln['post_link']?> :</h3>';
                  data +='<input type="text"  class="form-control" name="postlink"/>';
                              
                  return data;
              }
              function postImage(){
                  var data = "";
                  data +=' <div style="direction: ltr;width:100%" class="text-right">';
                  data +='<input type="file" class="filestyle" name="postimage" data-classButton="btn btn-primary " data-input="true" >';
                  data +='</div>';
                  return data;
              }
          $(function(){              
              $('select[name=posttype]').on('change',function(){
                 var $type = parseInt($(this).val());
                 $('.addtype').html("");
                 if($type == 2){
                      $('.addtype').html(postLink());
                 }else if($type == 3){
                      $('.addtype').html(postImage());
                      $(":file").filestyle({icon: true,classButton: "btn btn-primary",dataInput:true,buttonText:"<?=$ln['chose_img']?>"});
                 }
                 
              });
              
              $('.btnsubmit').on('click',function(){
              
       //             $('#imageloadstatus').show();
                    $('.alert-danger').removeClass('alert-danger');
                    var $posttype = parseInt($.trim($('select[name=posttype]').val()));
                    var $posttext = $.trim($('textarea[name=posttext]').val());
                        
                        if($posttype==""){
                            $('select[name=posttype]').addClass("alert-danger");
                             return false;
                        }else if($posttext==""){
                             $('textarea[name=posttext]').addClass("alert-danger");
                              return false;
                        }else{
                                if($posttype==2){
                                    var $input3 = $.trim($('input[name=postlink]').val()); 
                                    if($input3 == ""){
                                        $('input[name=postlink]').addClass("alert-danger");
                                         return false;
                                    }
                                }else if($posttype==3){
                                    var $input3 = $.trim($('input[name=postimage]').val());
                                    if($input3==""){
                                        $('input[class=input-large]').addClass("alert-danger");
                                         return false;
                                    }
                                }
                                
                                
                              submit();  
                                
                    }
                    
                    return false;
              
              });
              
              
              
              
              
          
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
                        url: ajaxFile+'?step=getposts',
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
          
          function submit(){
          			$("#imageform")	.ajaxForm({target: '.adddatahere',
				     beforeSubmit:function(e){ 
					$('#imageloadstatus').show();
					 }, 
					success:function(e){ 
					$('#imageloadstatus').hide();
                                       //  $('.adddatahere').html(e);
					}, 
					error:function(e){ 
					$('#imageloadstatus').hide();
					} }).submit();
		 
	
			
          }
          </script>
          </body>
</html>