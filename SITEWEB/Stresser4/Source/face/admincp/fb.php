<?php include_once 'inc.php'; /*
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
       <link rel="stylesheet" href="<?=$ST->get("url")?>/dist/css/jquery-te-1.4.0.css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
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
                        <li class=""><a href="pages.php"><?=$ln['pages']?></a></li>
                        <li class=""><a href="users.php"><?=$ln['users']?></a></li>
                        <li class="active"><a href="fb.php"><?=$ln['fb']?></a></li>
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
                <div class='row' style='direction: rtl;padding: 10px;'>
                    <div class='col-md-5 col-md-offset-1'>
                        <ul class='list-unstyled'>
                         <li>
                                <h3><?=$ln['user_page']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader user_page'/></h3>
                                <select class='form-control updateOnChange' name='user_page' style='height: 40px'>
                                    <option value='1' <?=($ST->get('user_page')==1) ? 'selected' : ''?>><?=$ln['open']?></option>
                                    <option value='2' <?=($ST->get('user_page')==2) ? 'selected' : ''?>><?=$ln['close']?></option>
                                </select>
                        </li>

                        <li>
                                <h3><?=$ln['reg_msg']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader reg_msg'/></h3>
                                <select class='form-control updateOnChange' name='reg_msg' style='height: 40px'>
                                    <option value='1' <?=($ST->get('reg_msg')==1) ? 'selected' : ''?>><?=$ln['open']?></option>
                                    <option value='2' <?=($ST->get('reg_msg')==2) ? 'selected' : ''?>><?=$ln['close']?></option>
                                </select>
                        </li>
                        <li>
                                <h3><?=$ln['reg_text']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader reg_text'/></h3>
                                <textarea  class='form-control updateOnChange' name='reg_text'><?=$ST->get('reg_text')?></textarea>
                                <span><?=$ln['regtext_help']?></span>
                            </li>
                        </ul>
                    </div>
                    <div class='col-md-5 col-md-offset-1'>
                        <ul class='list-unstyled'>
                            <li>
                                <h3><?=$ln['app_id']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader app_id'/></h3>
                                <input type='text' class='form-control updateOnChange' name='app_id'  value='<?=$ST->get('app_id')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['app_key']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader app_key'/></h3>
                                <input type='text' class='form-control updateOnChange' name='app_key'  value='<?=$ST->get('app_key')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['fb_page']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader fb_page'/></h3>
                                <input type='text' class='form-control updateOnChange' name='fb_page' dir="ltr" value='<?=$ST->get('fb_page')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['user_mail']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader user_mail'/></h3>
                                <select class='form-control updateOnChange' name='user_mail' style='height: 40px'>
                                    <option value='1' <?=($ST->get('user_mail')==1) ? 'selected' : ''?>><?=$ln['open']?></option>
                                    <option value='2' <?=($ST->get('user_mail')==2) ? 'selected' : ''?>><?=$ln['close']?></option>
                                </select>
                        </li>

                        </ul>
                    </div>
                    
                </div>
            </div>
                        <div class='box'>
                 <nav class="navbar navbar-inverse" role="navigation" style='margin-bottom: -10px;width: 102%;margin-left: -1%'>
                    <div class="navbar-header pull-right" style='width:150px'>
                        <ul class="nav navbar-nav" style='width:150px'>
                            <li style='width:150px'><a href='#' style='width:150px'><?=$ln['policies_privacy']?></a></li>
                        </ul>
                    </div>
                 </nav>
                 <div class='row' style='direction: rtl;padding: 10px;'>
                    <div class='col-md-11 col-md-offset-1'>
                                 <h3 class=' '><?=$ln['privacy']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader privacy'/></h3>
                                <div class='pil alert '>
                                 <textarea  class='form-control updateOnChange' name='privacy'><?=$ST->get('privacy')?></textarea>
                                </div>
                    </div>
                 </div>
            </div>

        </div>
        <script>ajaxFile='<?=$ST->get("url")?>/admincp/ajax.php';</script>
          <script src="https://code.jquery.com/jquery.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/jquery-te-1.4.0.min.js"></script>
          <script>
          $(function(){
              $('.updateOnChange').on('change',function(){
                 var value = $.trim($(this).val());
                 var option = $.trim($(this).attr('name'));
                 if(value !=''){
                 $('.'+option).show();
                  $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=update',
                        data: {'option':option,'value':value},
                        success: function(data){
                         if(data.st=='ok'){
                             $('.updateOnChange[name='+option+']').addClass('alert-success');
                         }
                          $('.'+option).hide();
                        },
                        dataType: 'json'
                      });
                }
              });
              $('.updateOnChange[name=privacy]').jqte({placeholder: "Please, write your biography",blur: function(){ 
              
                 var value = $.trim($('.updateOnChange[name=privacy]').val());
                 var option = $.trim($('.updateOnChange[name=privacy]').attr('name'));
                 if(value !=''){
                 $('.'+option).show();
                  $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=update',
                        data: {'option':option,'value':value},
                        success: function(data){
                         if(data.st=='ok'){
                             $('.pil').addClass('alert-success');
                         }
                          $('.'+option).hide();
                        },
                        dataType: 'json'
                      });
                }
               }});
             
          });
         </script>
    </body>
    
</html>