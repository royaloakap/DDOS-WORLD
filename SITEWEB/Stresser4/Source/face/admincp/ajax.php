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
 */
if(isset($_GET['step'])){
    switch ($_GET['step']){
        case 'update':
              update($_POST['option'],$_POST['value']);
            break;
        case 'updatepass':
            update('admin_pass',  md5($_POST['new_pass']) );
            break;
        case 'getusers':
            getUsers(abs(intval($_POST['user_start'])));
            break;
        case 'getpages':
            getpages(abs(intval($_POST['user_start'])));
            break;
        case 'addpost':
            addPost();
            break;
        case 'getposts':
            getPOSTS(abs(intval($_POST['user_start'])));
            break;
        case 'addtask':
            addtask();
            break;
        case 'gettasks':
             getTasks(abs(intval($_POST['user_start'])));
            break;
        case 'addtask2':
             addtask2();
            break;
        default :
            break;
    }
}


function update($op,$value){
        global $ST;
        
        if(isset($op) && $op !="" && isset($value) && $value !=""){
            $ST->updateOption($op,$value);
            $v = $ST->update();
            if($v['sucess']){
                echo json_encode(array('st'=>'ok'));
            }
        }
    }
    
    

function getUsers($start){
    global  $USERS;
    global $ST;
    global $ln;
    $usersData = $USERS->getUsers($start,20);
                        if($usersData){
                            $data = "";
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                $fm = trim($u['fb_gander']=='m') ? $ln['male']: $ln['female'];
                                $last_share = ($u['last_share'] == '1') ? $ln['last_success'] : $ln['last_error'];
                                $UDD = $i+$start;
                                $data .='<div class="panel panel-default item user-item" id="'.$UDD.'">
                                            <img src="https://graph.facebook.com/'.trim($u['fb_id']).'/picture?type=large" style="width:100%;"/>
                                             <ul class="list-group" >
                                                <li class="list-group-item text-right">'.trim($u['fb_name']).'</li>
                                                <li class="list-group-item text-right">'.trim($u['fb_email']).'</li>
                                                <li class="list-group-item text-right">'.$u['id'].':'.$fm.'</li>
                                                <li class="list-group-item text-right">'.$ln['regmsg'].cptime($u['reg_date']).'</li>
                                                <li class="list-group-item text-right">'.$ST->getCName($u['country_code']).'  <img src="'.$ST->get("url").'/dist/img/blank.gif" class="flag flag-'.$u['country_code'].'" alt="" /></li>
                                                <li class="list-group-item text-right">'.$last_share.'</li>
                                                <li class="list-group-item text-center">
                                                   <div class="btn-group">
                                                      <a type="button" class="btn btn-default" href="http://facebook.com/'.trim($u['fb_id']).'" target="_blank">'.$ln['facebook'].'</a>
                                                      <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                    </div>
                                                 </li>    

                                              </ul>
                                       </div>';
                            }
                            echo json_encode(array('st'=>'ok','text'=>$data));
                        }else{
                              echo json_encode(array('st'=>'no more users'));
                        }
}    
    
function getpages($start){
    global  $USERS;
    global $ST;
    global $ln;
    global $PAGES;
    $usersData = $PAGES->getUsers($start,20);
                        if($usersData){
                            $data = "";
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                //$USERS->setPrimary('fb_id');
                                $USERS = new User();
                                $USERS->setTable('users');
                                $USERS->setUid($u['user_id']);
                                 
                                $UDD = $i+$start;
                                
                                $last_share = ($u['last_share'] == '1') ? $ln['last_success'] : $ln['last_error'];
                                 $data .= '<div class="panel panel-default item user-item" id="'.$UDD.'">
                                            <img src="https://graph.facebook.com/'.trim($u['page_id']).'/picture?type=large" style="width:100%;"/>
                                             <ul class="list-group" >
                                                <li class="list-group-item text-right">'.trim($u['page_name']).'</li>
                                                <li class="list-group-item text-right">'.$USERS->getValue('fb_name').'</li>
                                                <li class="list-group-item text-right">'.$last_share.'</li>
                                                <li class="list-group-item text-center">
                                                    <div class="btn-group">
                                                      <a type="button" class="btn btn-default" href="http://facebook.com/'.trim($u['page_id']).'" target="_blank">'.$ln['facebook'].'</a>
                                                      <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                    </div>
                                                 </li>    
                                              </ul>
                                       </div>';
                            }
                         echo json_encode(array('st'=>'ok','text'=>$data));
                        }else{
                              echo json_encode(array('st'=>'no more users'));
                        }
}

function addPost(){
    global $ln,$_POST,$_FILES,$ST,$POSTS;
    $valid_formats = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");

    
    $posttype= abs(intval($_POST['posttype']));
    $posttext = trim($_POST['posttext']);
    $postlink = trim($_POST['postlink']);
    //echo $posttype."\n".$posttext."\n".$postlink;
    
    $name = $_FILES['postimage']['name'];
    $size = $_FILES['postimage']['size'];
    $ext = getExtension($name);

    
    if(empty($posttype) || empty($posttext) || (($posttype==2) && empty($postlink))  || (($posttype==3) && empty($name))){
     echo '<div class="alert alert-danger">'.$ln['all_important'].'</div>'; 
      return;
    }else{ 
        if($posttype==3){
            if(!in_array($ext,$valid_formats)){
              echo '<div class="alert alert-danger">'.$ln['not_valid_image_type'].'</div>';  
              return;
            }else if($size>(1024*1024)){
               echo '<div class="alert alert-danger">'.$ln['not_valid_image_size'].'</div>';  
                return;
            }else{
                $actual_image_name = 'hlounimg'.time().'.'.$ext;
                $tmp = $_FILES['postimage']['tmp_name'];
                if(move_uploaded_file($tmp, "upload/".$actual_image_name))
                {
                    $postlink = $actual_image_name;
                }else{
                    echo '<div class="alert alert-danger">'.$ln['error_while_copy'].'</div>';
                    return;
                }  
            }
        }
        
        if( ($posttype != 3) && ($posttype != 2) )
            $postlink = "HlounPostVersion2";
        
        
        if($POSTS->AddUser(array('date'=>time(),'type'=>$posttype,'text'=>$posttext,'link'=>$postlink))){
		$iD = mysql_insert_id();
        echo '<div class="panel panel-default">
                 <div class="panel-heading"><h3 class="panel-title">#'.$iD.'</h3></div>
                     <div class="panel-body"><div class="well">'.$posttext.'</div>';
              if($posttype==2){
                  echo '<a href="'.$postlink.'">'.$postlink.'</a>';
              }else if($posttype==3){
                  echo '<img src="'.$ST->get('url').'/admincp/upload/'.$postlink.'"  style="max-width:100%"/>';
              }
			  echo '<a type="button" class="btn btn-default" href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/directshare.php?id='.$iD.'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\'>'.$ln['direct_share'].'</a>
';
        
        echo '</div>
             </div>';
        }else
        echo '<div class="alert alert-danger">'.$ln['error_while_insert'].'</div>';   
    }
    
}



function getExtension($str)
{
$i = strrpos($str,".");
if (!$i)
{
return "";
}
$l = strlen($str) - $i;
$ext = substr($str,$i+1,$l);
return $ext;
}

 function getPOSTS($start){
    global  $POSTS;
    global $ST;
    global $ln;
    $usersData = $POSTS->getUsers($start,20);
                        if($usersData){
                            $data = "";
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                $UDD = $i+$start;                                
                                $data.= '<div class="panel panel-default item w2 user-item" id="'.$UDD.'">';
                                            if($u['type']==3)
                                                $data.=  ' <img src="'.$ST->get('url').'/admincp/upload/'.$u['link'].'" style="width:100%;max-height:300px;"/>';
                                            $data.=  '
                                                 
                                             <div class="panel-body">
                                                 <div class="well text-right" style="direction: rtl">'.stripslashes($u['text']).'</div>
                                             </div>
                                            <ul class="list-group" >';
                                            if($u['type']==2)
                                                $data.=  ' <li class="list-group-item text-right"><a href="'.$u['link'].'">'.$u['link'].'</a></li>';
                                            $data.= '   <li class="list-group-item text-right" style="direction: rtl">'.cptime($u['date']).'</li>
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
                            echo json_encode(array('st'=>'ok','text'=>$data));
                        }else{
                              echo json_encode(array('st'=>'no more users'));
                        }
}   


function addtask(){
    global  $TASK,$_POST,$ST,$ln;
    
    $type = abs(intval($_POST['type']));
    
    $posts = trim($_POST['posts']);
    
    $count = trim($_POST['count']);
    
    $gander = trim($_POST['gander']);
    
    $taskfor = trim($_POST['taskfor']);
    
    
    if(empty($type) || empty($posts) || empty($count) || empty($gander) || empty($taskfor)){
         echo '<div class="alert alert-danger">'.$ln['all_important'].'</div>'; 
      return;
    }else{
        if($TASK->AddUser(array(
                'type'=>$type,
                'time'=>time(),
                'posts'=>$posts,
                'count'=>$count,
                'gander'=>$gander,
                'isfinish'=>0,
                'idnow'=>0,
                "taskfor"=>$taskfor
                ))
                ){
            
            $countMsg = ($count=="all") ? $ln['all'] : $count;
            if($gander=="male")
                $ganderMsg = $ln['male'];
                elseif ($gander=="female")
                $ganderMsg = $ln['female'];
            else {
                $ganderMsg = $ln['two'];
            }
            
            $taskforMsg = ($taskfor=="users") ? $ln['users'] : $ln['pages'];
            
            $ID = mysql_insert_id();
            echo ' <div class="panel panel-default">
                        <ul class="list-group" >
                                         <li class="list-group-item text-right">#'.$ID.'</li>
                                         <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$posts.'</li>
                                         <li class="list-group-item text-right">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                         <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                         <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                         <li class="list-group-item text-right"><a href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/doTask.php?id='.$ID.'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\' class="btn btn-primary">'.$ln['start_share'].'</a></li>
                                     </ul>
                                 
                             </div>
                             <div class="alert alert-info">'.$ln['start_share_msg'].'</div>
';
            
            
        }else{
             echo '<div class="alert alert-danger">'.$ln['error_while_insert'].'</div>';   
        }
    }
}

function getTasks($start){
    global  $TASK;
    global $ST;
    global $ln;
    $usersData = $TASK->getUsers($start,20,"where `type`='1' ");
                        if($usersData){
                            $data = "";
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                $UDD = $i+$start;                                
                                            $countMsg = ($u['count']=="all") ? $ln['all'] : $u['count'];
                                            if($u['gander']=="male")
                                                $ganderMsg = $ln['male'];
                                                elseif ($u['gander']=="female")
                                                $ganderMsg = $ln['female'];
                                            else {
                                                $ganderMsg = $ln['two'];
                                            }

                                            $taskforMsg = ($u['taskfor']=="users") ? $ln['users'] : $ln['pages'];
                                                
                                            if($u['isfinish']==0){
                                                $isFinshMsg = $ln['is_finish_not'];
                                                 $lbtn = ' <a href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/doTask.php?id='.$u['id'].'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\' class="btn btn-primary">'.$ln['cont_share'].'</a>';

                                            }else{
                                                 $isFinshMsg = $ln['finsihed'];
                                                 $lbtn = "";
                                            }
                                $data .= '<div class="panel panel-default item  user-item" id="'.$UDD.'">
                                        <ul class="list-group" >
                                             <li class="list-group-item text-right">#'.$u['id'].'</li>
                                             <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$u['posts'].'</li>
                                             <li class="list-group-item text-right" style="direction: rtl">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                             <li class="list-group-item text-right">'.cptime($u['time']).'</li>
                                             <li class="list-group-item text-right">'.$ln['totalcount'].' : '.$u['totalcount'].'</li>
                                             <li class="list-group-item text-right">'.$ln['successed'].' : '.$u['successed'].'</li>
                                             <li class="list-group-item text-right">'.$ln['failed'].' : '.$u['failed'].'</li>
                                             <li class="list-group-item text-right">'.$isFinshMsg.'</li>
                                                <li class="list-group-item text-center">
                                                
                                                    <div class="btn-group">
                                                            <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                        '.$lbtn.'
                                                  </div>
                                </li>
                                         
</ul>
                                       </div>';
                                
                                
                            }
                            echo json_encode(array('st'=>'ok','text'=>$data));
                        }else{
                              echo json_encode(array('st'=>'no more users'));
                        }
}   


function addtask2(){
        global  $TASK,$_POST,$ST,$ln;
    
    $type = abs(intval($_POST['type']));
    
    $posts = trim($_POST['posts']);
    
    $count = trim($_POST['count']);
    
    $gander = trim($_POST['gander']);
    
    $taskfor = trim($_POST['taskfor']);
    
    
    $share_time = trim($_POST['share_time']);
    
    if(empty($type) || empty($posts) || empty($count) || empty($gander) || empty($taskfor) || empty($share_time)){
         echo '<div class="alert alert-danger">'.$ln['all_important'].'</div>'; 
      return;
    }else{
        $share_time = ($share_time*60*60);
        $t = time();
        $share_time += $t;
        if($TASK->AddUser(array(
                'type'=>$type,
                'time'=>$share_time,
                'posts'=>$posts,
                'count'=>$count,
                'gander'=>$gander,
                'isfinish'=>0,
                'idnow'=>0,
                "taskfor"=>$taskfor
                ))
                ){
            
            $countMsg = ($count=="all") ? $ln['all'] : $count;
            if($gander=="male")
                $ganderMsg = $ln['male'];
                elseif ($gander=="female")
                $ganderMsg = $ln['female'];
            else {
                $ganderMsg = $ln['two'];
            }
            
            $taskforMsg = ($taskfor=="users") ? $ln['users'] : $ln['pages'];
            
            $ID = mysql_insert_id();
            echo ' <div class="panel panel-default">
                        <ul class="list-group" >
                                         <li class="list-group-item text-right">#'.$ID.' '.$share_time.'<br/> '.$t.'</li>
                                         <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$posts.'</li>
                                         <li class="list-group-item text-right">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                         <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                         <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                         <li class="list-group-item text-right"><a href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/doTask.php?id='.$ID.'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\' class="btn btn-primary">'.$ln['start_share'].'</a></li>
                                     </ul>
                                 
                             </div>
                             <div class="alert alert-info">'.$ln['start_share_msg'].'</div>
';
            
            
        }else{
             echo '<div class="alert alert-danger">'.$ln['error_while_insert'].'</div>';   
        }
    }
    
}


function getTasks2($start){
    global  $TASK;
    global $ST;
    global $ln;
    $usersData = $TASK->getUsers($start,20,"where `type`='2' ");
                        if($usersData){
                            $data = "";
                            for($i=0;$i<count($usersData);$i++){
                                $u = $usersData[$i];
                                $UDD = $i+$start;                                
                                            $countMsg = ($u['count']=="all") ? $ln['all'] : $u['count'];
                                            if($u['gander']=="male")
                                                $ganderMsg = $ln['male'];
                                                elseif ($u['gander']=="female")
                                                $ganderMsg = $ln['female'];
                                            else {
                                                $ganderMsg = $ln['two'];
                                            }

                                            $taskforMsg = ($u['taskfor']=="users") ? $ln['users'] : $ln['pages'];
                                                
                                            if($u['isfinish']==0){
                                                $isFinshMsg = $ln['is_finish_not'];
                                                 $lbtn = ' <a href=\'javascript:void(window.open("'.$ST->get('url').'/admincp/doTask.php?id='.$u['id'].'","","width=525,height=550,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes"))\' class="btn btn-primary">'.$ln['cont_share'].'</a>';

                                            }else{
                                                 $isFinshMsg = $ln['finsihed'];
                                                 $lbtn = "";
                                            }
                                $data .= '<div class="panel panel-default item  user-item" id="'.$UDD.'">
                                        <ul class="list-group" >
                                             <li class="list-group-item text-right">#'.$u['id'].'</li>
                                             <li class="list-group-item text-right">'.$ln['postsss_idss'].' : '.$u['posts'].'</li>
                                             <li class="list-group-item text-right" style="direction: rtl">'.$ln['count_chose'].' : '.$countMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['gander'].' : '.$ganderMsg.'</li>
                                             <li class="list-group-item text-right">'.$ln['taskfor'].' : '.$taskforMsg.'</li>
                                             <li class="list-group-item text-right">'.date("Y-m-d H:i:s",$u['time']).'</li>
                                             <li class="list-group-item text-right">'.$ln['totalcount'].' : '.$u['totalcount'].'</li>
                                             <li class="list-group-item text-right">'.$ln['successed'].' : '.$u['successed'].'</li>
                                             <li class="list-group-item text-right">'.$ln['failed'].' : '.$u['failed'].'</li>
                                             <li class="list-group-item text-right">'.$isFinshMsg.'</li>
                                                <li class="list-group-item text-center">
                                                
                                                    <div class="btn-group">
                                                            <a type="button" class="btn btn-default" href="?step=deluser&id='.$u['id'].'">'.$ln['deluser'].'</a>
                                                        '.$lbtn.'
                                                  </div>
                                </li>
                                         
</ul>
                                       </div>';
                                
                                
                            }
                            echo json_encode(array('st'=>'ok','text'=>$data));
                        }else{
                              echo json_encode(array('st'=>'no more users'));
                        }
}   

?>