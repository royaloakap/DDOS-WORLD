<?php 
include 'inc.php';
if(isset($_GET['id'])){
	$id = abs(intval($_GET['id']));
	if($TASK->AddUser(array(
                'type'=>1,
                'time'=>time(),
                'posts'=>$id,
                'count'=>'all',
                'gander'=>'two',
                'isfinish'=>0,
                'idnow'=>0,
                "taskfor"=>'users'
                ))
                ){
			header("Location: doTask.php?id=".mysql_insert_id());
			die();
	
		}

}

?>