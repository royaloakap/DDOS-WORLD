<?php 
ini_set('max_execution_time', 0); 
include_once 'inc.php'; 
require '../src/facebook.php';
$TASKINC = true;
$scriptMsg = "شكرا لستخدامك ليون بوست الاصدار الثاني";
$config = array();
$config['appId'] = $ST->get('app_id');
$config['secret'] = $ST->get('app_key');
$config['fileUpload'] = true; // optional
$facebook = new Facebook($config);
$facebook->setFileUploadSupport(true);
 
$user_access = str_replace("access_token=","",getLink("https://graph.facebook.com/oauth/access_token?client_id=".$ST->get('app_id')."&client_secret=".$ST->get('app_key')."&grant_type=client_credentials"));
 

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

if(isset($_GET['id'])){
    $TASK->setUid($_GET['id']);
    $TASKINFO = $TASK->getUser();
    
    if(isset($TASKINFO) && $TASKINFO->isfinish == 0){
    		include 'TaskHeader.php';
    
             $POSTSArray = split(",",$TASKINFO->posts);
             mysql_query("update `posts` set `is_shared`='1' where `id` IN ('$TASKINFO->posts')");
             $where = "where `id`>".$TASKINFO->idnow." "; 
             if($TASKINFO->taskfor == 'pages'){
              $usersData = $PAGES->getUsersNoLimit($where);  
             }else{
               if($TASKINFO->gander =="male")
                   $where .= " and `fb_gander` = 'm' ";
               else if($TASKINFO->gander =="female")
                   $where .= " and `fb_gander` = 'f' ";
               
               if($TASKINFO->count!="all"){
                   $cc = str_replace(",", "','", $TASKINFO->count);
                    $where .= " and `country_code` IN  ('$cc') ";
               }
               
              $usersData = $USERS->getUsersNoLimit($where);   
             }
             $TASK->UpdateUser('totalcount', count($usersData));
             
             $sccessCount = $TASKINFO->successed;
             $unsccessCount = $TASKINFO->failed;
             
             for($i=0;$i< count($usersData);$i++){
                 $u = $usersData[$i];
                 $POSTS = new User();
                 $POSTS->setTable('posts');
                 $POSTS->setUid($POSTSArray[rand(0,count($POSTSArray)-1)]);
                 $postData = $POSTS->getUser();
                 
                 if($TASKINFO->taskfor == 'pages'){
                     $ID = $u['page_id'];
                     $acess = getPageAccess($u['user_id'],$u['page_id']);
                     $NAME = $u['page_name'];
                 }else{
                     $ID = $u['fb_id'];
                     $acess = $user_access;
                     if($postData->type == 3)
                     	 $acess = $u['fb_access'];
                     $NAME = $u['fb_name'];
                 }
                    $post_array['access_token'] = $acess;
                    $post_array['message'] = stripslashes($postData->text);
                 if($postData->type == 2)
                    $post_array['link'] = stripslashes($postData->link);
                 else if($postData->type == 3)
                    $post_array['source'] = '@upload/'.  html_entity_decode(stripslashes($postData->link));
                 
                  /*echo '<pre>';
                     print_r($post_array);
                     die();*/
					  //$post_array['actions']=array('name'=>$ST->get('title'),'link'=>$ST->get("url"));
					 
                 try{                 
                 if($postData->type == 2 || $postData->type == 1)
                     $add= $facebook->api('/'.$ID.'/feed','POST',$post_array);
                 else if($postData->type == 3)
                    $add= $facebook->api('/'.$ID.'/photos','POST',$post_array);
                   
                
                 }  catch (FacebookApiException $e) {
                    $notsendmsg =  $e;
                    // die();
                     $add = false;
                 }
                 $subUser = new User();
                     if($TASKINFO->taskfor == 'pages')
                         $subUser->setTable ('pages');
                     else
                         $subUser->setTable('users');
                 
                  $TASK->UpdateUser('idnow', $u['id']);     
                  
                 $subUser->setUid($u['id']);
                 if($add){
                     $subUser->UpdateUser("last_share", 1);
                     $sccessCount++;
                    $TASK->UpdateUser('successed', $sccessCount);                
                     echo '<div class="alert alert-success">'.  str_replace("{user}", $NAME, $ln['share_sucess_to_user']).' - <a href="//facebook.com/'.$add['id'].'">'.$add['id'].'</a></div>';
                     
                 }else{
                     $unsccessCount++;
                     $subUser->UpdateUser("last_share", 2);
                     $TASK->UpdateUser('failed', $unsccessCount);                
                     echo '<div class="alert alert-danger">'.  str_replace("{user}", $NAME, $ln['share_unsuccess_to_user']).'<br>'.$notsendmsg.'</div>';
                 }
                  $TASK->UpdateUser('idnow', $u['id']);                
                 echo '<script>toEnd();</script>';
                 
                 flush();
                ob_flush();
                //sleep(1);
                
                if( $i== (count($usersData)-1) )
                {
                     $TASK->UpdateUser('isfinish', 1);
                     echo '<center><a href="" class="btn btn-primary btnclose">'.$ln['share_done'].'</a></center>';
                }
             }
             
           include 'TaskFooter.php';       
    }else{
          echo '<script>
            window.close();
            window.opener.location.reload();
            </script> 
';
    }
}else{
    echo '<script>
            window.close();
            window.opener.location.reload();
            </script> 
';
}
?>
