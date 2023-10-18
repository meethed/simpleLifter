<?php
$dir="data";
if (!file_exists($dir)){
	mkdir($dir,0744);
}
$compName=filter_input(INPUT_GET, "comp");


$jsondata = $_POST["json"];

$isExcel=filter_input(INPUT_GET,"s");
if ($isExcel=="excel") {
	$jsondata= $_POST["excel"];
	$pwd=filter_input(INPUT_GET, "pwd");
////// this bit is to validate the password

// Create connection
include_once "../../../config.php";
$sql = "SELECT compLetters, hish FROM comps WHERE compLetters=\"" . $compName. "\"";
$result = $conn->query($sql);

if ($result->num_rows > 0) { //if there's a competition that matches the compLetters - now there's only 1 unique
  // competition so if the database is corrupted this will be a bit messy but that can't happen as that isn't
  // how the db is set up
  // output data of each row
  while($row = $result->fetch_assoc()) {

  if ($row["hish"]!=crypt($pwd, substr($compName,1,2))) {
  die();
  }

else {};
}}};
$lifterFile=$compName.'.json';
$setupFile=$compName.'setup.json';

$saveload=filter_input(INPUT_GET, "q");

if ($saveload=="lifters" && isset($jsondata)) {
	file_put_contents($dir.'/'.$lifterFile, $jsondata);
	echo "saved lifters";
};

if ($saveload=="setup" && isset($jsondata)) {
	file_put_contents($dir.'/'.$setupFile, $jsondata);
	echo "saved setup";
};

if ($saveload=="loadlifter") { //then it's a load of the lifter
	if ($compName=="ACO") {
		$a=json_decode(file_get_contents($dir.'/ACE.json'),true);
		$b=json_decode(file_get_contents($dir.'/ACF.json'),true);
		$c=json_decode(file_get_contents($dir.'/ACG.json'),true);
		$d=json_decode(file_get_contents($dir.'/ACH.json'),true);
		$e=json_decode(file_get_contents($dir.'/ACI.json'),true);
		$f=json_decode(file_get_contents($dir.'/ACJ.json'),true);
		$g=json_decode(file_get_contents($dir.'/ACK.json'),true);
		$h=json_decode(file_get_contents($dir.'/ACL.json'),true);
		$i=json_decode(file_get_contents($dir.'/ACM.json'),true);
		$j=json_decode(file_get_contents($dir.'/ACN.json'),true);

		$jsondata = json_encode(array_merge($a,$b,$c,$d,$e,$f,$g,$h,$i,$j));
	} ;
	if ($compName=="ADE") {
		$a=json_decode(file_get_contents($dir.'/ACU.json'),true);
		$b=json_decode(file_get_contents($dir.'/ACV.json'),true);
		$c=json_decode(file_get_contents($dir.'/ACW.json'),true);
		$d=json_decode(file_get_contents($dir.'/ACX.json'),true);
		$e=json_decode(file_get_contents($dir.'/ACY.json'),true);
		$f=json_decode(file_get_contents($dir.'/ACZ.json'),true);
		$g=json_decode(file_get_contents($dir.'/ADA.json'),true);
		$h=json_decode(file_get_contents($dir.'/ADB.json'),true);
		$i=json_decode(file_get_contents($dir.'/ADC.json'),true);
		$j=json_decode(file_get_contents($dir.'/ADD.json'),true);

		$jsondata = json_encode(array_merge($a,$b,$c,$d,$e,$f,$g,$h,$i,$j));
	} else {
		$jsondata=file_get_contents($dir.'/'.$lifterFile);
	}
	echo $jsondata;
}

if ($saveload=="loadsetup") { //then it's a load of the setup

	$jsondata=file_get_contents($dir.'/'.$setupFile);
	echo $jsondata;
}
?>
