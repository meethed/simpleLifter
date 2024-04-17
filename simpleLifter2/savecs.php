<?php
include_once("../includes/config.inc");
$cL=$_SESSION["compLetters"];
if(isset($_SESSION["sesh"])) {$sesh=$_SESSION["sesh"];} else {$sesh="111";};

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);
//var_dump($data);

$statement="";
foreach($data as $key => $value) {
if (!$value) {$value="NULL";};
if (!is_numeric($value) && ($value!="NULL")) {$value="'". $value."'";};
$statement = $statement.$key."=".$value.",";
}
$statement = substr($statement, 0, -1);
$sql="UPDATE compstatus set ".$statement." where compLetters='".$cL."' and session='".$sesh."'";;

echo $sql;

$result=$conn->query($sql);
echo $conn->error;
?>

