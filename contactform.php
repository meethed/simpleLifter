<?php
include_once("./includes/config.inc");
$_SESSION["contact"]="yes";

$f=fopen("./utils/contacts/".time().".txt","w") or die ("help");

fwrite($f,$_GET["frmName"]."\n");
fwrite($f,$_GET["frmEmail"]."\n");
fwrite($f,$_GET["frmQuery"]."\n");

fclose($f);
header("Location: help.php");
?>
