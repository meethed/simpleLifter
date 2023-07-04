<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once ("../config.php");

$lifterName = strval(test_input($_GET["lifterName"]));
$compName       = strval(test_input($_GET["compName"]));
$lifterTeam = strval(test_input($_GET["lifterTeam"]));
$lifterClass = strval(test_input($_GET["lifterClass"]));
$lifterCat = strval(test_input($_GET["lifterCat"]));
$lifterFlight = strval(test_input($_GET["lifterFlight"]));
$currentAttempt        = floatval(test_input($_GET["currentAttempt"]));
$total        = floatval(test_input($_GET["total"]));
$lifterBW        = floatval(test_input($_GET["lifterBW"]));
$compStatus        = intval(test_input($_GET["compStatus"]));
$compLetters        = strval(test_input($_GET["compLetters"]));
$nextLoad           = strval(test_input($_GET["nextLoad"]));



$lifterName = strval(test_input($_POST["lifterName"]));
$compName       = strval(test_input($_POST["compName"]));
$lifterTeam = strval(test_input($_POST["lifterTeam"]));
$lifterClass = strval(test_input($_POST["lifterClass"]));
$lifterCat = strval(test_input($_POST["lifterCat"]));
$lifterFlight = strval(test_input($_POST["lifterFlight"]));
$currentAttempt        = floatval(test_input($_POST["currentAttempt"]));
$total        = floatval(test_input($_POST["total"]));
$lifterBW        = floatval(test_input($_POST["lifterBW"]));
$compStatus        = intval(test_input($_POST["compStatus"]));
$compLetters        = strval(test_input($_POST["compLetters"]));
$nextLoad           = strval(test_input($_POST["nextLoad"]));

//check if it exists
$stmt=$conn->prepare("select compName from comps where compLetters=?");

$stmt->bind_param("s", $compLetters);
$stmt->execute();

$result = $stmt->get_result();

$stmt->close();

var_dump($result);
print_r($result);
if  (mysqli_num_rows($result) == 1) { //if it already exists do an update
  if($stmt=$conn->prepare("UPDATE comps SET  lifterName=?, currentAttempt=?, total=?, compStatus=?, lifterTeam=?, lifterBW=?, lifterClass=?, lifterCat=?, lifterFlight=? WHERE compLetters=?")){
    $stmt->bind_param("sddisdssss", $lifterName, $currentAttempt, $total, $compStatus, $lifterTeam, $lifterBW, $lifterClass, $lifterCat, $lifterFlight, $compLetters);
    $stmt->execute();
    $stmt->close();
  }



};

//all done updating
$conn->close();




?>

