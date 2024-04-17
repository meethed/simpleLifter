<?php
include_once("../includes/config.inc");
if (isset($_GET["c"])) {
  $cL=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
  $dsp=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);
  if (!isset($dsp)) {$dsp="111";};
} else {
  if (empty($_SESSION["compLetters"])) {die();}
  $cL=$_SESSION["compLetters"];
  if(isset($_SESSION["sesh"])) {$dsp=$_SESSION["sesh"];} else {$dsp="111";};
}
$sql="SELECT * FROM compstatus WHERE session LIKE '%". $dsp ."' AND compLetters='".$cL."' ORDER BY updated DESC";
$result=$conn->query($sql)->fetch_assoc();
echo json_encode($result,JSON_NUMERIC_CHECK);
?>
