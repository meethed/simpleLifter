<?php
include_once("./includes/config.inc");
$c=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
if (!$c) die(0);
$sql="UPDATE comp set fed='".$c."' where compLetters='".$_SESSION["compLetters"]."'";
$conn->query($sql);
$_SESSION["fed"]=$c;
?>
