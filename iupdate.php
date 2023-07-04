<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once ("../config.php");

$json = $_POST["json"];
if (json_decode($json) !=null) { //if the json is valid
$j=json_decode($json);
echo $json;
$stmt=$conn->prepare("select compName from comps where compLetters=?");

$stmt->bind_param("s", $j->compLetters);
$stmt->execute();

$result = $stmt->get_result(); //just getting the current thing to make sure

$stmt->close();

if  (mysqli_num_rows($result) == 1) { //if it already exists do an update
	$j->lifterClass=intval($j->lifterClass);
	if($stmt=$conn->prepare("UPDATE comps SET  lifterName=?, currentAttempt=?, total=?, compStatus=?, lifterTeam=?, lifterBW=?, lifterClass=?, lifterCat=?, lifterFlight=?, nextLoad=?, nextName=?, nextLot=?, nextRack=?, lot=?, rack=?, bar=? WHERE compLetters=?")){
    $stmt->bind_param("sddisdsssdsisisds", $j->lifterName, $j->currentAttempt, $j->total, $j->compStatus, $j->lifterTeam, $j->lifterBW, $j->lifterClass, $j->lifterCat, $j->lifterFlight, $j->nextLoad, $j->nextName, $j->nextLot, $j->nextRack, $j->lot, $j->rack, $j->bar, $j->compLetters);
    $stmt->execute();
    $stmt->close();
	echo "attempted to execute";
  }
}

//all done updating
$conn->close();
} //end of the 'valid json check'


?>

