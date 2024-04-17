<?php
include_once("../includes/config.inc");
$cL=$_SESSION["compLetters"];

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);

//this next bit converts it into an SQL string
$keys="";
$cols="";
foreach($data as $key => $value) {
if (!$value) {$value="NULL";} else {$value=strip_tags(htmlspecialchars($value),ENT_QUOTES);};
if (!is_numeric($value) && ($value!="NULL")) {$value="'". $value."'";};
$cols=$cols.",".$key;
$values = $values.",".$value;
}
$statement = substr($statement, 0, -1);
//echo $data["idx"]

$cols=ltrim($cols,",");
$values=ltrim($values,",");

$sql="INSERT INTO ".$cL." (".$cols.") values (".$values.")";
echo $sql;
$result=$conn->query($sql);
echo $conn->error;
?>

