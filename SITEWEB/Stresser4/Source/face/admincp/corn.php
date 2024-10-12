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
ini_set('max_execution_time', 0); 
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

require '../src/facebook.php';

$scriptMsg = "شكرا لستخدامك ليون بوست الاصدار الثاني";
$config = array();
$config['appId'] = $ST->get('app_id');
$config['secret'] = $ST->get('app_key');
$config['fileUpload'] = true; // optional
$facebook = new Facebook($config);
$APPACCESS = str_replace("access_token=","",getLink("https://graph.facebook.com/oauth/access_token?client_id=".$ST->get('app_id')."&client_secret=".$ST->get('app_key')."&grant_type=client_credentials"));
function getPageAccess($userid,$pageid){
    global $facebook;
    $USERS = new User();
    $USERS->setTable('users');

    $USERS->setUid($userid);
    $INFO = $USERS->getUser();
        try {
        $pageinfo = $facebook->api('/'.$pageid.'/?fields=access_token','GET',array('access_token'=>$INFO->fb_access));
        }catch(FacebookApiException $e){
                error_log('Could not get page access.'.$e);
        }
        if($pageinfo){
            return $pageinfo['access_token'];
        }
        return $INFO->fb_access;
}
echo '<meta charset="utf-8"/>';
$ti = time();
$TASKINFO = $TASK->getUsersNoLimit("where `type`='2' and `time`<='$ti'  and `isfinish`='0' ");
if(count($TASKINFO) && $TASKINFO !=''){
    for($c=0;$c<count($TASKINFO);$c++){            
        $TASKONE = $TASKINFO[$c];
        
         $POSTSArray = split(",",$TASKONE['posts']);
             $where = "where `id`>".$TASKONE['idnow']." "; 
             if($TASKONE['taskfor'] == 'pages'){
              $usersData = $PAGES->getUsersNoLimit($where);  
             }else{
               if($TASKONE['gander'] =="male")
                   $where .= " and `fb_gander` = 'm' ";
               else if($TASKONE['gander'] =="female")
                   $where .= " and `fb_gander` = 'f' ";
               
               if($TASKONE['count']!="all"){
                   $cc = str_replace(",", "','", $TASKONE['count']);
                   echo $cc;
                   if($cc!="")
                    $where .= " and `country_code` IN  ('$cc') ";
               }
               
              $usersData = $USERS->getUsersNoLimit($where);   
             }
             $TASKCFORUPDATE = new User();
             $TASKCFORUPDATE->setTable('task');
             $TASKCFORUPDATE->setUid($TASKONE['id']);
             
             $TASKCFORUPDATE->UpdateUser('totalcount', count($usersData));
             $singeTask = $TASKCFORUPDATE->getUser();
             $sccessCount = $TASKONE['successed'];
             $unsccessCount = $TASKONE['failed'];
             
             echo '<h2>'.$TASKONE['id'].'</h2>';
            for($i=0;$i< count($usersData);$i++){
                echo '<br />start send to user<br />';
                 $u = $usersData[$i];
                 $POSTS = new User();
                 $POSTS->setTable('posts');
                 $POSTS->setUid($POSTSArray[rand(0,count($POSTSArray)-1)]);
                 $postData = $POSTS->getUser();
                 
                 if($singeTask->taskfor == 'pages'){
                     $ID = $u['page_id'];
                     $acess = getPageAccess($u['user_id'],$u['page_id']);
                     $NAME = $u['page_name'];
                 }else{
                     $ID = $u['fb_id'];
                     $acess = $APPACCESS;
                     if($postData->type == 3)
                     	 $acess = $u['fb_access'];
                     $NAME = $u['fb_name'];
                 }
                    $post_array['access_token'] = $acess;
                    $post_array['message'] = stripslashes($postData->text);
                 if($postData->type == 2)
                    $post_array['link'] = stripslashes($postData->link);
                 else if($postData->type == 3)
                    $post_array['source'] = '@../'.stripslashes($postData->link);
                 
                 
                 try{
                     
                 if($postData->type == 2 || $postData->type == 1)
                     $add= $facebook->api('/'.$ID.'/feed','post',$post_array);
                 else if($postData->type == 3)
                    $add= $facebook->api('/'.$ID.'/photos/','post',$post_array);
                  
                     
                 }  catch (FacebookApiException $e) {
                     $add = false;
                 }
                    $subUser = new User();
                     if($singeTask->taskfor == 'pages')
                         $subUser->setTable ('pages');
                     else
                         $subUser->setTable('users');
                 
                  $TASKCFORUPDATE->UpdateUser('idnow', $u['id']);     
                 
                 $subUser->setUid($u['id']);
                 if($add){
                     $subUser->UpdateUser("last_share", 1);
                     $sccessCount++;
                    $TASKCFORUPDATE->UpdateUser('successed', $sccessCount);                
                     echo '<div class="alert alert-success">'.  str_replace("{user}", $NAME, $ln['share_sucess_to_user']).' - <a href="//facebook.com/'.$add['id'].'">'.$add['id'].'</a></div>';
                     
                 }else{
                     $unsccessCount++;
                     $subUser->UpdateUser("last_share", 2);
                     $TASKCFORUPDATE->UpdateUser('failed', $unsccessCount);                
                     echo '<div class="alert alert-danger">'.  str_replace("{user}", $NAME, $ln['share_unsuccess_to_user']).'</div>';
                 }
                  $TASKCFORUPDATE->UpdateUser('idnow', $u['id']);                
                 flush();
                ob_flush();
                sleep(1);
                
                if( $i== (count($usersData)-1) )
                {    echo 'update finish';
                     $TASKCFORUPDATE->UpdateUser('isfinish', 1);
                     echo 'update finish done';
                }
             }
             sleep(5);
    }
    
}

?>
