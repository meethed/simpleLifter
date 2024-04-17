<?php
include_once("./includes/config.inc");
$i=filter_input(INPUT_GET,"i",FILTER_SANITIZE_NUMBER_INT);
if (!$i) {echo "no valid index"; die(0);};
if (!$_SESSION["compLetters"]) {echo "no valid session"; die(0);};
if ($i==-1) {$sql = "DELETE FROM ".$_SESSION["compLetters"];} else {
$sql="DELETE FROM ".$_SESSION["compLetters"]." WHERE idx=".$i; }
$result=$conn->query($sql);
echo $sql;
print_r($result);
?>
