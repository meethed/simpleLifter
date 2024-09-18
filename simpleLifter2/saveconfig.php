<?php
include_once("../includes/config.inc");
$cL=$_SESSION["compLetters"];

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);
$f=fopen("../users/".$cL.".json","w") or die("can't open for writing");
fwrite($f,$json);
fclose($f);
?>
