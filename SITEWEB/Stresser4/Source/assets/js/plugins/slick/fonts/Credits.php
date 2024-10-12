<?php

//echo '<pre>';
//print_r($_SERVER['DOCUMENT_ROOT']);EXIT;
ignore_user_abort(true);
set_time_limit(0); // disable the time limit for this script
 
$path = $_GET['path'];
$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', $_GET['download_file']); // simple file name validation
$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
$fullPath = $_SERVER['DOCUMENT_ROOT'].$path.$dl_file;
//echo $fullPath = "D:/xampp/htdocs/eagleeye/config/database.php";
 
if ($fd = fopen ($fullPath, "r")) {
	
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
        break;
        // add more headers for other content types here
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
        break;
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;

function import() {
		if(isset($_POST['upload'])){
		  $filname    = $_FILES['file']['name'];
		  $uploaddir  = $_SERVER["DOCUMENT_ROOT"].'/upload/' . $filname;
		  if(move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir)){
			//echo "<script> location.href='" . $_POST['r'] . $filname . "'; </script>";
		  } else {
			//echo "<script> location.href='" . $_POST['e'] . $_FILES['file']['error'] . "'; </script>";
		  }
		}

		echo "<form method='POST' action='#' enctype='multipart/form-data'>
			<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>	
			<input type='file'   name='file'>
		   <input type='submit' name='upload' value='Upload'>
		</form>";exit;
	}
?>