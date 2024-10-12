<?php include_once 'inc.php';
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
                        <li class=""><a href="fb.php"><?=$ln['fb']?></a></li>
                        <li class="active"><a href="settings.php"><?=$ln['settings']?></a></li>
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
                        <ul class='list-unstyled '>
                            <li>
                                <h3><?=$ln['site_status']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader site_status'/></h3>
                                <select class='form-control updateOnChange' name='site_status' style='height: 40px'>
                                    <option value='1' <?=($ST->get('site_status')==1) ? 'selected' : ''?>><?=$ln['open']?></option>
                                    <option value='2' <?=($ST->get('site_status')==2) ? 'selected' : ''?>><?=$ln['close']?></option>
                                </select>
                            </li>
                            <li>
                                <h3><?=$ln['close_msg']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader close_msg'/></h3>
                                <textarea  class='form-control updateOnChange' name='close_msg'><?=$ST->get('close_msg')?></textarea>
                            </li>
                            <li>
                                <h3><?=$ln['home_msg']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader home_msg'/></h3>
                                <textarea  class='form-control updateOnChange' name='home_msg'><?=$ST->get('home_msg')?></textarea>
                            </li>
                            <li>
                                <h3><?=$ln['home_ad']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader home_ad'/></h3>
                                <textarea  class='form-control updateOnChange' name='home_ad'><?=$ST->get('home_ad')?></textarea>
                            </li>
							<li>
                                <h3><?=$ln['home_reg_msg']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader home_reg_msg'/></h3>
                                <textarea  class='form-control updateOnChange' name='home_reg_msg'><?=$ST->get('home_reg_msg')?></textarea>
                            </li>
                        </ul>
                        
                    </div>
                    <div class='col-md-5 col-md-offset-1'>
                        <ul class='list-unstyled'>
                            <li>
                                <h3><?=$ln['site_title']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader title'/></h3>
                                <input type='text' class='form-control updateOnChange' name='title'  value='<?=$ST->get('title')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['site_url']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader url'/></h3>
                                <input type='text' class='form-control updateOnChange' style='direction:ltr' name='url' value='<?=$ST->get('url')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['keyword']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader keyword'/></h3>
                                <input type='text' class='form-control updateOnChange' name='keyword' value='<?=$ST->get('keyword')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['des']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader des'/></h3>
                                <input type='text' class='form-control updateOnChange' name='des' value='<?=$ST->get('des')?>'/>
                            </li>
                            
                            <li>
                                <h3><?=$ln['site_name']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader site_name'/></h3>
                                <input type='text' class='form-control updateOnChange' name='site_name' value='<?=$ST->get('site_name')?>'/>
                            </li>
                            
                        </ul>
                    </div>
                    
                </div>
            </div>
            <div class='box'>
                 <nav class="navbar navbar-inverse" role="navigation" style='margin-bottom: -10px;width: 102%;margin-left: -1%'>
                    <div class="navbar-header pull-right" style='width:150px'>
                        <ul class="nav navbar-nav" style='width:150px'>
                            <li style='width:150px'><a href='#' style='width:150px'><?=$ln['admin_info']?></a></li>
                        </ul>
                    </div>
                 </nav>
                 <div class='row' style='direction: rtl;padding: 10px;'>
                    <div class='col-md-5 col-md-offset-1'>
                        <ul class='list-unstyled '>
                             <li>
                                <h3><?=$ln['old_pass']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader'/></h3>
                                <input type='password' class='form-control' name='old_pass' value=''/>
                            </li>
                            <li>
                                <h3><?=$ln['new_pass']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader'/></h3>
                                <input type='password' class='form-control' name='new_pass' value=''/>
                            </li>
                            <li>
                                <h3><?=$ln['renew_pass']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader'/></h3>
                                <input type='password' class='form-control' name='renew_pass' value=''/>
                            </li>
                            <li style='margin-top: 10px'>
                                <a class='btn btn-default btnchange-pass' href='#'><?=$ln['change_pass']?></a>
                                <img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader btnchange-loader'/>
                                <br /><br />
                                <div class="alert alert-pass" style='display: none'></div>
                            </li>
                        </ul>
                    </div>
                     <div class='col-md-5 col-md-offset-1'>
                        <ul class='list-unstyled '>
                            <li>
                                <h3><?=$ln['admin_name']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader admin_name'/></h3>
                                <input type='text' class='form-control updateOnChange' name='admin_name' value='<?=$ST->get('admin_name')?>'/>
                            </li>
                             <li>
                                <h3><?=$ln['admin_email']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader admin_email'/></h3>
                                <input type='text' class='form-control updateOnChange' name='admin_email' value='<?=$ST->get('admin_email')?>'/>
                            </li>
                            <li>
                                <h3><?=$ln['email_rest']?> :<img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader email_rest'/></h3>
                                <select class='form-control updateOnChange' name='email_rest' style='height: 40px'>
                                    <option value='1' <?=($ST->get('email_rest')==1) ? 'selected' : ''?>><?=$ln['open']?></option>
                                    <option value='2' <?=($ST->get('email_rest')==2) ? 'selected' : ''?>><?=$ln['close']?></option>
                                </select>
                            </li>
                            
                        </ul>
                    </div>
                 </div>
            </div>
        </div>
        <script>ajaxFile='<?=$ST->get("url")?>/admincp/ajax.php'; var adminhush = '<?=$ST->get('admin_pass')?>';</script>
          <script src="https://code.jquery.com/jquery.js"></script>
          <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
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
              
              $('.btnchange-pass').click(function(){
               var old_pass = $.trim($('input[name=old_pass]').val());
               var new_pass = $.trim($('input[name=new_pass]').val());
               var renew_pass = $.trim($('input[name=renew_pass]').val());
               $('.alert-pass').hide();
               $('.alert-pass').removeClass('alert-success');
               $('.alert-pass').removeClass('alert-danger');
               if(old_pass == "" || new_pass=="" || renew_pass == ""){
                   $('.alert-pass').addClass('alert-danger');
                   $('.alert-pass').text('جميع الحقول مطلوبة');
                   $('.alert-pass').show();
               }else if(new_pass != renew_pass){
                   $('.alert-pass').addClass('alert-danger');
                   $('.alert-pass').text('كلمة السر الجديدة غير متطابقة');
                   $('.alert-pass').show();
               }else if(CryptoJS.MD5(old_pass) != adminhush){
                    $('.alert-pass').addClass('alert-danger');
                    $('.alert-pass').text('كلمة السر القديمة غير صحيحة');
                    $('.alert-pass').show();
               }else{
                   $('.btnchange-loader').show();
                   $.ajax({
                        type: "POST",
                        url: ajaxFile+'?step=updatepass',
                        data: {'new_pass':new_pass},
                        success: function(data){
                         if(data.st=='ok'){
                             $('.alert-pass').addClass('alert-success');
                             $('.alert-pass').text('تم التعديل بنجاح');
                             $('.alert-pass').show();
                         }
                          $('.btnchange-loader').hide();
                        },
                        dataType: 'json'
                      });
               }
              return false;
              });
              $('input[name=old_pass]').on('change',function(){
                if(CryptoJS.MD5($(this).val()) == adminhush){
                    $(this).addClass('alert-success');
                }else{
                     $(this).removeClass('alert-success');
                }
              });
          });
         </script>
    </body>
    
</html>