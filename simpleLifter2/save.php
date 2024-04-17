<?php
include_once("../includes/config.inc");
$cL=$_SESSION["compLetters"];
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);

$keys="";
$cols="";
$statement="";
foreach($data as $key => $value) {
if (!$value) {$value="NULL";} else {$value=strip_tags(htmlspecialchars($value,ENT_QUOTES | ENT_HTML5,'UTF-8', /*double_encode*/false ));};
if (!is_numeric($value) && ($value!="NULL")) {$value="'". $value."'";};
if ($key!="idx") {$statement .= $key."=".$value.",";}
}
$statement = substr($statement, 0, -1);
echo $data["idx"];

$sql="UPDATE ".$cL." set ".$statement." where idx=".$data["idx"];
echo $sql;
$result=$conn->query($sql);
echo $conn->error;


 ?>

