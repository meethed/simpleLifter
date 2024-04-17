<?php
include_once("../includes/config.inc");
$message="";

$target_dir = "../uploads/";
if (!is_dir($target_dir)) {
 mkdir($target_dir, 0744);
}
if (isset($_FILES["uploadedFile"])) {
//if (isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["error"] === UPLOAD_ERR_OK) {
 // get details of the uploaded file 
 $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
 $fileName = $_FILES['uploadedFile']['name'];
 $fileSize = $_FILES['uploadedFile']['size'];
 $fileType = $_FILES['uploadedFile']['type'];
 $fileNameCmps = explode(".", $fileName);
 $fileExtension = strtolower(end($fileNameCmps));

 $newFileName = $fileName;

// validate file extension
$allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc','php','xlsm','ico','csv');
if (in_array($fileExtension, $allowedfileExtensions)) {

 // directory in which the uploaded file will be moved 
 $dest_path = $target_dir . $newFileName;
 if(move_uploaded_file($fileTmpPath, $dest_path))
 {
   $message ='File was successfully uploaded.';
 }
 else
 {
   $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.<br>Path: '.$dest_path;
 } //end if file upload and move is successful
} else { //end if in array of allowed extensions
 $message = "There was an upload error.<br>";
 $message .= "Error: ".$_FILES["uploadedFile"]["error"];
} //end else for when not in allowed extensions
} else {//end if isset file
$message ="The file was not set in the POST data properly";
}
$_SESSION["filemessage"] = $message;
header("Location: ../admin.php");

?>
