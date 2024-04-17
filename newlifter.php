<?php
include_once("./includes/config.inc");
$cL=$_SESSION["compLetters"];
$sql="insert into ".$cL." (name,lot,gp,gender,gear,lifts,division,wc) values ('Lifter Name',0,'A','F','CL','PL','F-CL-PL',1000)";
$result=$conn->query($sql);

$sql="select idx from ".$cL." order by idx DESC";
$result=$conn->query($sql)->fetch_assoc();
echo (json_encode($result));

?>
