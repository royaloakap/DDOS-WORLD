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
include ('../class/db.php');
include ('../class/Settings.php');
include ('../class/lang.php');
$ST = new Settings('settings');
if($ST->isLogin()){
    header("Location: index.php");
    die();
}
?>
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
            <div class='box' style='width:330px;padding: 10px;border-radius: 5px;'>
                        <ul class='list-unstyled' style='direction: rtl;width:300px;margin-left: 20px;'>
                            <li>
                                <h3><?=$ln['admin_name']?> :</h3>
                                <input type='text' name='admin_name' value='' class='form-control admin_name'/>  
                            </li>
                            <li>
                                <h3><?=$ln['admin_pass']?> :</h3>
                                <input type='password' name='admin_pass' value='' class='form-control admin_pass'/>  
                            </li>
                            <li style='margin-top: 10px'>
                                <a href='#' class='btn btn-primary login'><?=$ln['login']?></a>
                                <img src='<?=$ST->get('url')?>/dist/img/loader.gif' class='loader'/>
                                <br /><br />
                                <a href='#' class='restpass'><?=$ln['rest_pass']?></a>
                            </li>
                            <li style='margin-top: 10px;display: none;margin-bottom: 5px' class='formrest'>
                                <input type='email' name='email_torest' class='form-control' placeholder="اكتب البريد هنا"/>
                                <br/>
                                <a href='#' class='btn btn-danger login-rest'><?=$ln['restpass']?></a>
                            </li>
                            
                            <li>
                                <div class="alert" style='display: none'></div>
                            </li>
                            
                        </ul>       
                        <?=COPYRIGHT?>
            </div>
        </div>
         <script src="https://code.jquery.com/jquery.js"></script>
          <script src="<?=$ST->get("url")?>/dist/js/bootstrap.min.js"></script>
          <script>
          $(function(){
              $('.login').click(function(){
                  
                  $('input[name=admin_name]').removeClass("alert-danger");
                  $('input[name=admin_pass]').removeClass("alert-danger");
                  $('.alert').hide();
                  $('.alert').removeClass('alert-success');
                  $('.alert').removeClass('alert-danger');
                  var admin_name = $.trim($('input[name=admin_name]').val());
                  var admin_pass = $.trim($('input[name=admin_pass]').val());
                  if(admin_name==""){
                      $('input[name=admin_name]').addClass('alert-danger');
                  }else if(admin_pass==""){
                      $('input[name=admin_pass]').addClass('alert-danger');
                  }else{
                      $('.loader').show();  
                       $.ajax({
                        type: "POST",
                        url: 'loginajax.php?step=login',
                        data: {'admin_name':admin_name,'admin_pass':admin_pass},
                        success: function(data){
                         if(data.st == 'error'){
                             $('.alert').addClass('alert-danger');
                         }else{
                             $('.alert').addClass('alert-success');                           
                         }
                              $('.alert').html(data.msg);
                              $('.alert').show();
                          
                         
                         $('.loader').hide();
                        },
                        dataType: 'json'
                      });

                  }
                  
                  
                  return false;
              });
              
              
              $('.restpass').on('click',function(){
               $('.formrest').show();
               return false;
              });
              
              $('.login-rest').on('click',function(){
                  $emailrest = $.trim($('input[name=email_torest]').val());
                  if($emailrest==""){
                      alert('enter a email');
                  }else{
                       $('.loader').show();
                        $('.alert').removeClass('alert-danger');
                        $('.alert').removeClass('alert-success');
                       $.ajax({
                        type: "POST",
                        url: 'loginajax.php?step=rest',
                        data: {'email':$emailrest},
                        success: function(data){
                         if(data.st == 'error'){
                             $('.alert').addClass('alert-danger');
                         }else{
                             $('.alert').addClass('alert-success');                           
                         }
                              $('.alert').html(data.msg);
                              $('.alert').show();
                          
                         
                         $('.loader').hide();
                        },
                        dataType: 'json'
                      });
                  }
                  return false;
              });
          });
          </script>
    </body>
</html>