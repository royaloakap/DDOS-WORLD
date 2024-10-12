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
}else{
    if($_GET['step'] == 'login'){
        if(isset($_POST['admin_name']) && isset($_POST['admin_pass'])){
            echo json_encode($ST->login($_POST['admin_name'], $_POST['admin_pass']));
        }else{
            echo json_encode( array('st'=>'error','msg'=>'جميع الحقول مطلوبة'));
        }
    }else if($_GET['step'] == 'rest'){
        $email = addslashes(strip_tags($_POST['email']));
        if($email == $ST->get('admin_email')){
            $hush = md5(time().''.$email.''.rand(0,time()));
            $ST->updateOption('restcode', $hush);
            $ST->update();
            $message = "
                 <center>
                     <h2>".$ST->get('title')."</h2>
                     <a href='".$ST->get('url')."/admincp/rest.php?code=".$hush."'>انقر هنا لتغير كلمة السر</a>
                       <p>قم بنقل الرابط ازا لم يعمل بشكل مباشر</p>
                       <p>".$ST->get('url')."/admincp/rest.php?code=".$hush."</p>
                </center>
                ";
            $ST->sendMail($ST->get('admin_email'),"Hloun Post Version 2 RestPassword","Rest Admin Password", $message);
            echo json_encode( array('st'=>'ok','msg'=>'تم ارسالة رسالة الى البريد'));
        }else{
            echo json_encode( array('st'=>'error','msg'=>'البريد غير صحيح'));
            
        }
        
    }
}


?>
