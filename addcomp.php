<?php

  include_once "../config.php";

  $compName = test_input($_POST["newCompName"]);
  $startdate = $_POST["startdate"];
  $enddate = $_POST["enddate"];
  $pwd = test_input($_POST["pwd"]);
  $cnt = test_input($_POST["contact"]);
  $parentComp = test_input($_POST["parentComp"]);

  $jsonout=array("success" => 0, "compLetters" => "", "message" => "");
  if ($compName == "") {
    $jsonout["message"]="Error: Blank comp name!";
    echo json_encode($jsonout); die();
  }

//get the next comp letter sequence
$str = "SELECT compLetters, compName FROM comps ORDER BY compLetters DESC limit 1";
$result = $conn->query($str);
$data = $result->fetch_assoc();
$lastcomp= $data["compLetters"];
$oldname = $data["compName"];

//close query
$result->close;
if ($oldname == $compName) {
$jsonout->message="Error: Duplicate competition name";
echo json_encode($jsonout);
die();
}
// if the last competition to be added had the same name, it's likely an F5 issue so just nuke it (no duplicates allowed)
if (($oldname!=$compName) || (!is_null($compName))) {

if ($enddate < $startdate)  $enddate = $startdate ;
$startDate=date('Y-m-d', strtotime($startdate));
$endDate=date('Y-m-d', strtotime($enddate));
//increment the comp
$lastcomp++;
//hash the password using simple DES on the completters as the hash;
$cpwd = crypt($pwd, substr($lastcomp,1,2));
//prepare the sql statement

if (!$parentComp) {$parentComp=$lastcomp; $isChild=0;} else {
  $isChild=1;
  $s2= "UPDATE comps SET isParent=1 WHERE compLetters='".$parentComp."'";
  $conn->query($s2);
  $conn->close;
};

$sql = $conn->prepare("INSERT INTO comps (compLetters, compName,startdate,enddate,hish,contact,leftLight,centreLight,rightLight,timeTo,timeTwo,isChild,parentComp) VALUES  (?,?,?,?,?,?,?,?,?,?,?,?,?)");
$z = 0;
$zd ="0";
$sql->bind_param("ssssssiiiiiis",$lastcomp,$compName,$startDate, $endDate, $cpwd, $cnt,$z,$z,$z,$z,$z,$isChild,$parentComp);
$sql->execute();
$sql->close;


$jsonout["success"]=1;
$jsonout["message"]="Competition added successfully";
$jsonout["compLetters"]=$lastcomp;

$sql->close();
$conn->close();

echo json_encode($jsonout);
}
?>
