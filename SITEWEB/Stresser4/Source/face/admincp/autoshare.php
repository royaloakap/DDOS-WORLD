<?php
ini_set('max_execution_time', 0); 
include ('../class/db.php');
include ('../class/Settings.php');
include ('../class/Users.php');
include ('../class/lang.php');
require '../src/facebook.php';

$ST = new Settings('settings');
$USERS = new User();
$USERS->setTable('users');

$PAGES = new User();
$PAGES->setTable('pages');

$POSTS = new User();
$POSTS->setTable('posts');



$config = array();
$config['appId'] = $ST->get('app_id');
$config['secret'] = $ST->get('app_key');
$config['fileUpload'] = true; // optional
$facebook = new Facebook($config);
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
	/* $sql = mysql_query("select * from posts where is_shared='0' ") or die(mysql_error());
	 if(mysql_num_rows($sql)>=1){
	 	
	 }*/
	 $sql = mysql_query("select * from posts where is_shared='0'   ORDER BY RAND() LIMIT 0,1 ") or die('sorry no posts');
		if(mysql_num_rows($sql)>=1){
		 $postData = mysql_fetch_object($sql);
		 
		 /***
		  * select * users and share post to every user
		  */
		 $usersData = $USERS->getUsersNoLimit('');
		 if(count($usersData)>=1){
		 	for($i=0;$i<count($usersData);$i++){
		 		$u = $usersData[$i];
				$ID = $u['fb_id'];
                $acess = $user_access;
                if($postData->type == 3)  $acess = $u['fb_access'];
                $NAME = $u['fb_name'];
				$post_array['access_token'] = $acess;
                    $post_array['message'] = stripslashes($postData->text);
                 if($postData->type == 2)
                    $post_array['link'] = stripslashes($postData->link);
                 else if($postData->type == 3)
                    $post_array['source'] = '@upload/'.  html_entity_decode(stripslashes($postData->link));
				
				try{                 
                 if($postData->type == 2 || $postData->type == 1)
                     $add= $facebook->api('/'.$ID.'/feed','POST',$post_array);
                 else if($postData->type == 3)
                    $add= $facebook->api('/'.$ID.'/photos','POST',$post_array);
                    echo 'send<br />';
                 }  catch (FacebookApiException $e) {
                    $notsendmsg =  $e;
                    echo 'not send<br />';
                     $add = false;
                 }
				 
				 
				
		 	}
		 }else{
		 	echo 'not users';
		 }
		   
		  /**
		   * create list from pages and share post to page
		   */  
		   $usersData = $PAGES->getUsersNoLimit('');
		 if(count($usersData)>=1){
		 	for($i=0;$i<count($usersData);$i++){
		 		$u = $usersData[$i];
				//$ID = $u['fb_id'];
                //$acess = $user_access;
                //if($postData->type == 3)  $acess = $u['fb_access'];
               // $NAME = $u['fb_name'];
                $ID = $u['page_id'];
                $acess = getPageAccess($u['user_id'],$u['page_id']);
                $NAME = $u['page_name'];
				$post_array['access_token'] = $acess;
                    $post_array['message'] = stripslashes($postData->text);
                 if($postData->type == 2)
                    $post_array['link'] = stripslashes($postData->link);
                 else if($postData->type == 3)
                    $post_array['source'] = '@upload/'.  html_entity_decode(stripslashes($postData->link));
				
				try{                 
                 if($postData->type == 2 || $postData->type == 1)
                     $add= $facebook->api('/'.$ID.'/feed','POST',$post_array);
                 else if($postData->type == 3)
                    $add= $facebook->api('/'.$ID.'/photos','POST',$post_array);
                    echo 'send<br />';
                 }  catch (FacebookApiException $e) {
                    $notsendmsg =  $e;
                    echo 'not send<br />';
                     $add = false;
                 }
				 
				 
				
		 	}
		 }else{
		 	echo 'not pages';
		 }
		 
		 
		 mysql_query("update `posts` set `is_shared`='1' where id='$postData->id'");
		 
	 }else{
	 	echo 'not posts';
	 }

?>
