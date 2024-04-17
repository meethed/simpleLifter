<?php
include_once("../includes/config.inc");

if (isset($_GET["c"])) {
  $cL=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
  $sesh=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);
  if (!isset($sesh)) {$getall=1;};
} else {
  if (isset($_SESSION["compLetters"])) {
    $cL=$_SESSION["compLetters"];
    if(isset($_GET["f"])) {$sesh=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);} else {$getall=1;};
  } else {die();};
}
if ($getall) {$sql="SELECT * from ".$cL; } else {
  $sql="SELECT * from " .$cL. " where session='". $sesh ."'";
}
//echo json_encode($sql);
$result=$conn->query($sql)->fetch_all(MYSQLI_ASSOC);
echo json_encode($result,JSON_NUMERIC_CHECK);
?>
