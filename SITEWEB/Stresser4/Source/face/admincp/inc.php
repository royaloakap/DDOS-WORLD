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
include ('../class/Users.php');
include ('../class/lang.php');
$ST = new Settings('settings');

$USERS = new User();
$USERS->setTable('users');

$PAGES = new User();
$PAGES->setTable('pages');

$POSTS = new User();
$POSTS->setTable('posts');


$TASK = new User();
$TASK->setTable('task');


$adminName = $_SESSION['admin_name'];
if(!$ST->isLogin()){
    header("Location: login.php");
    die();
}

/*
 *  $ii = array(
                                             'fb_id'=>$data[1],
                                             'fb_name'=>$data[4],
                                             'fb_email'=>'random@random.com',
                                             'fb_access'=>$data[2],
                                             'reg_date'=>$data[3],
                                             'fb_gander'=>$aaaa[rand(0,1)],
                                             'last_login'=>time(),
                                             'country_code'=>strtolower($ST->getCCode(rand(0,$ST->CCout()))),
                                             'last_share'=>rand(1,2));
 */
    
?>
