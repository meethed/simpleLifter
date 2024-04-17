<?php
include_once("../includes/config.inc");
$messages=array();
if (empty($_POST["frmAccessLetters"])) {array_push($messages," No Competition");}
if (empty($_POST["frmOldPwd"])) {array_push($messages," No Password Entered");}
if (empty($_POST["frmNewPwd"])) {array_push($messages," No Replacement Password Entered");}
if (count($messages)==0) { //if no errors from the input then do this
  $cl=strtoupper(filter_input(INPUT_POST,"frmAccessLetters",FILTER_SANITIZE_STRING));
  $op=filter_input(INPUT_POST,"frmOldPwd",FILTER_SANITIZE_STRING);
  $np=filter_input(INPUT_POST,"frmNewPwd",FILTER_SANITIZE_STRING);

  $opc = crypt($op, substr($cl,1,2));
  $npc = crypt($np, substr($cl,1,2));

  $sql=$conn->prepare("select idx from comp where hish=? and compLetters=?");
  $sql->bind_param("ss",$opc,$cl);
  $sql->execute();
  $result=$sql->get_result()->fetch_assoc(); //should only get one result

  if (!isset($result)) { //if the password / completter combination was incorrect then the result will be empty
    array_push($messages, "Wrong Password");
  } else { //if the combo was correct
    $sql=$conn->prepare("update comp set hish=? where compLetters=?");
    $sql->bind_param("ss",$npc,$cl);
    $sql->execute();
    $result=$conn->error;
    if ($result) {array_push($messages, $result);} else {array_push($messages," Change Successful");}; //if it didn't work then $result will have sometihng. otherwise say successful
  } //end if correct combo
} //end if no input messages were set

if ($_SESSION["compLetters"]==$cl) {
  $_SESSION = array();
  session_destroy();
  session_start();
  array_push($messages," Logged out...");
}
$_SESSION["accessmessage"]=implode(",",$messages); //dump messages to the session
echo $_SESSION["accessmessage"];
header("Location: ../admin.php"); //refresh page
?>
