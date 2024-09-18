<?php
include_once("./includes/config.inc");
if (!isset($_SESSION["compLetters"])) {$cL=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);} else {$cL=$_SESSION["compLetters"];};
if (empty($_SESSION["sesh"])) {$dsp="111";} else { $dsp=$_SESSION["sesh"];};

$sql="select l,c,r,timeTo,timeTwo,timeThree,updated from compstatus where compLetters='".$cL."' and session='".$dsp."'";
$result=$conn->query($sql)->fetch_assoc();

echo json_encode($result,JSON_NUMERIC_CHECK);

?>
