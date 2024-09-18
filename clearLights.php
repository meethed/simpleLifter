<?php
include_once("./includes/config.inc");
if (isset($_SESSION["compLetters"])) {
$sql="update compstatus set l=0,c=0,r=0 where compLetters='".$_SESSION["compLetters"]."'";
$conn->query($sql);
}
?>
