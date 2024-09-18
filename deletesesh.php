<?php
include_once("./includes/config.inc");
$cL=$_SESSION["compLetters"];
$s=filter_input(INPUT_GET,"s",FILTER_SANITIZE_STRING);
$sql="delete from compstatus where compLetters='".$cL."' and session='".$s."'";
$result=$conn->query($sql);

$sql="update ".$cL." set session=NULL where session='".$s."'";
$result=$conn->query($sql);

$sql="select seshs from comp where compLetters='".$cL."'";
$result=$conn->query($sql)->fetch_assoc();
$seshs=explode(",",$result);
$seshs=array_diff($seshs,[$s]);
$seshs.implode(",",$result);
$sql="update comp set seshs='".$seshs."' where compLetters='".$cL."'";
$_SESSION["seshs"]=$seshs;
?>
