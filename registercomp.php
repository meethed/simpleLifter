<?php
include_once("./includes/config.inc");

//filter POST data
$compName=filter_input(INPUT_POST,"frmName",FILTER_SANITIZE_STRING);
$fed=filter_input(INPUT_POST,"frmFed",FILTER_SANITIZE_STRING);
$startDate=filter_input(INPUT_POST,"frmStart",FILTER_SANITIZE_STRING);
$days=intval(filter_input(INPUT_POST,"frmDays",FILTER_SANITIZE_NUMBER_INT));
$platforms=intval(filter_input(INPUT_POST,"frmPlatforms",FILTER_SANITIZE_NUMBER_INT));
$sessions=intval(filter_input(INPUT_POST,"frmSessions",FILTER_SANITIZE_NUMBER_INT));
$seshs=filter_input(INPUT_POST,"frmSesh",FILTER_SANITIZE_STRING);
if (!$seshs) {$seshs="111";};
$seshexplode=explode(",",$seshs);
$PWD=filter_input(INPUT_POST,"frmPWD",FILTER_SANITIZE_STRING);
$contact=filter_input(INPUT_POST,"frmContact",FILTER_SANITIZE_STRING);
$lights=0;if (isset($_POST["frmLights"])) $lights=1;
$sheet=0;if (isset($_POST["frmSheet"])) $sheet=1;
//init variables
$errors=[];

//load the last comp registered 
$sql="select compLetters,compName from comp order by compLetters desc limit 1";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$lastcomp= $data["compLetters"];
$oldname = $data["compName"];


//check the competition master name is valid
if ($oldname == $compName) {
$jsonout->message="Error: Duplicate competition name";
echo json_encode($jsonout);
die();
}


//register the master session
if (($oldname!=$compName) || (!is_null($compName))) {

$lastcomp++;
$compLetters=$lastcomp;
$isChild=0;
$endDate=date_create($startDate);
date_add($endDate,date_interval_create_from_date_string($days." days"));
$endDate=date_format($endDate,"Y-m-d");
$hish = crypt($PWD, substr($compLetters,1,2));
$compLetters2=$compLetters;
$oldLetters=$compLetters;
$z=0;
$stmt = $conn->prepare("insert into comp (compLetters, compName, startDate, endDate, hish, contact, lights, sheet, seshs, fed) values (?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssiiss",$compLetters,$compName,$startDate,$endDate,$hish,$contact,$lights,$sheet,$seshs,$fed);
$stmt->execute();
echo $conn->error;
//add lights session
$i=1;
forEach($seshexplode as $s) {
  $s2 = "insert into compstatus (compLetters,session) values ('".$compLetters."','".$s."')";
  $conn->query($s2);
}
echo $conn->error;

if ($lights || $sheet) {
$s3 ="create table ".$compLetters." like comptemplate";
$conn->query($s3);
}

$f=fopen("./users/".$compLetters.".json","w");
fwrite($f,'{"auto":false,"countup":true,"showlights":true,"autobreaks":false,"simplelights":true}');
fclose($f);



header('Location: index.php');
} else
{ echo "unexpected error, competition not added";};
?>
