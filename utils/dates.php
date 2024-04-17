<?php

include_once("../includes/config.inc");

$message=array();
if (empty($_POST["frmLetters"]))  {array_push($message,"No comp!");};
if (empty($_POST["frmStart"]))  {array_push($message,"No Start Date!");};
if (empty($_POST["frmEnd"]))  {array_push($message,"No End Date!");};
if (empty($_POST["frmAccessLetters"]))  {array_push($message,"No Access Code Provided");};

if ($message==[]) {

$cl=strtoupper(filter_input(INPUT_POST,"frmLetters",FILTER_SANITIZE_STRING));
$sd=filter_input(INPUT_POST,"frmStart",FILTER_SANITIZE_STRING);
$ed=filter_input(INPUT_POST,"frmEnd",FILTER_SANITIZE_STRING);
$pwd=filter_input(INPUT_POST,"frmAccessLetters",FILTER_SANITIZE_STRING);
$cpwd=crypt($pwd,substr($cl,1,2));
$startDate=$sd;//date_format($sd,"Y-m-d");  
$endDate=$ed;//date_format($ed,"Y-m-d");  

$sql="select idx from comp where compLetters='".$cl."' and hish='".$cpwd."'";
$result=$conn->query($sql)->fetch_assoc();
if (!$result["idx"]) {array_push($message,"Incorrect access code"); } else {
$sql = $conn->prepare("update comp set startdate=?, enddate=? where compLetters=? and hish=?");
$sql->bind_param("ssss",$sd,$ed,$cl,$cpwd);
$sql->execute();
}}

if (count($message)>0) {
  $_SESSION["datemessage"]=implode(",",$message);
} else {
  $_SESSION["datemessage"]="Update Successful";
  }
  header("Location: ../admin.php");
  die();
?>
