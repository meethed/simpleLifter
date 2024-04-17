<?php
include_once("./includes/config.inc");

$errors = array();
if (!isset($_SESSION["pos"])) {array_push($errors, "no position");}
if (!isset($_GET["d"])) {array_push($errors, "no button pressed");}
if (!isset($_SESSION["compLetters"])) {array_push($errors, "login issue");}
if (!isset($_SESSION["sesh"])) {$dsp="111";} else {$dsp=$_SESSION["sesh"];};
$pos=$_SESSION["pos"];
if (isset($_GET["p"])) {$pos=filter_input(INPUT_GET,"p",FILTER_SANITIZE_STRING);}
$val=filter_input(INPUT_GET,"d",FILTER_SANITIZE_STRING);
$comp=$_SESSION["compLetters"];
if ($val>10 && ($pos!="timeTo" && $pos!="timeTwo" && $pos!="timeThree")) {array_push($errors,"only centre ref or tech desk can trigger bar loaded timer");}

if (empty($errors)) {
  if ($pos=="l" || $pos=="c" || $pos=="r") {
    $sql ="select ".$pos." from compstatus where compLetters='".$comp."'";
    $result=$conn->query($sql);
    $row=$result->fetch_assoc();
    if ($row[$pos]==0) {
      $sql ="update compstatus set timeTo=0, ".$pos."=".$val." where compLetters='".$comp."' and session = '".$dsp."'";
    } else {$sql="";array_push($errors,"already set");};
  } else {
    $t = new DateTime();
    if ($val>0) {$t->add(new DateInterval('PT'.$val.'S'));};
    if ($val<=0) {$t->sub(new DateInterval('PT1M'));};
    $ts= $t->format("Y-m-d H:i:s");
    $sql = "update compstatus set l=0, c=0, r=0, ".$pos." = '".$ts."' where compLetters='".$comp."' and session = '".$dsp."'";
  }
  if($sql) {$conn->query($sql);
    if ($conn->error) array_push($errors, $conn->error);
    array_push($errors, "success");
   // array_push($errors, $sql);
  }
}
// echo '{"hi":"i"}';
if (isset($errors)){
  echo json_encode($errors);
}


?>
