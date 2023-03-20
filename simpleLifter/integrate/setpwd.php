<?php
$c=$_GET["c"];
$p=$_GET["p"];
$cpwd = crypt($p,substr($c,1,2));

echo $cpwd;

?>
