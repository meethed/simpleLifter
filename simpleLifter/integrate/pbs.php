<?php
include_once "../../../config.php";
$compName=filter_input(INPUT_GET,"comp");
$pwd = filter_input(INPUT_GET, "pwd");

if ($pwd=="") {
	die("No access code entered");
}
if ($compName=="") {
	die("No comp selected");
}

$sql="SELECT hish FROM comps WHERE compLetters = ?";
$stmt=$conn->prepare($sql);
$stmt->bind_param("s",$compName);
$stmt->execute();
$result = $stmt->get_result();
$dbpwd=$result->fetch_assoc()["hish"];
$conn->close;

$cpwd=crypt($pwd,substr($compName,1,2));

if ($dbpwd!=$cpwd) {
  die ("Incorrect password");
}

$dir="data";
if (!file_exists($dir)){
        mkdir($dir,0744);
}

$isExcel=filter_input(INPUT_GET,"s");
if ($isExcel=="excel") {
        $jsondata= $_POST["excel"];
}
if (!isset($jsondata)) {
die ("No data");
}

$lifterFile='best'.$compName.'.json';


//if (isset($jsondata)) {
        file_put_contents($dir.'/'.$lifterFile, $jsondata);
        echo "saved PBs";
//};



?>




