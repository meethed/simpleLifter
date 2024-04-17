
<?php
include_once("./includes/config.inc");
$cL=$_SESSION["compLetters"];
$s=filter_input(INPUT_GET,"s",FILTER_SANITIZE_STRING);
$url=filter_input(INPUT_GET,"url",FILTER_SANITIZE_URL);
$start=filter_input(INPUT_GET,"start",FILTER_SANITIZE_STRING);
if (!empty($url)) {$sql="update compstatus set streamURL='".$url."' where compLetters='".$cL."' and session='".$s."'";};
if (!empty($start)) {$sql="update compstatus set timeThree='".$start."' where compLetters='".$cL."' and session='".$s."'";};
$result=$conn->query($sql);
?>

