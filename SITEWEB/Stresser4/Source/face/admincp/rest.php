<?php
include ('../class/db.php');
include ('../class/Settings.php');
include ('../class/lang.php');
$ST = new Settings('settings');
if(isset($_GET['code']) && $_GET['code'] != ""){
    
    $code = trim(addslashes(strip_tags($_GET['code'])));
    if($ST->get("restcode") == $code && $code != ""){
        $ST->updateOption('restcode', "");
        $ST->update();
        $_SESSION['allow_change_pass'] = true;
        $_SESSION['code'] = $code;
        echo '<meta charset="utf-8"/>
                <center><form method="post">
                    <input type="password" name="pass" placeholder="اكتب كلمة السر هنا" />
                    <input type="submit" value="تغير" name="change"/>
                    <input type="hidden" name="code" value="'.$code.'"/>
                </form></center>';
    }else if($_SESSION['allow_change_pass']){
        if(isset($_POST['pass']) && isset($_POST['code'])){
            
            $CODE = trim($_POST['code']);
            $pass = trim($_POST['pass']);
            if($_SESSION['code'] == $CODE){
                if($pass !=""){
                    
                    $ST->updateOption('admin_pass', md5($pass));
                    $ST->update();
                    $_SESSION['allow_change_pass'] = false;
                    $_SESSION['code'] = false;
                    header("Location: login.php");
                }
            }
        }
    }
}

?>