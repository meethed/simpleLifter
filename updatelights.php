<?php
include_once("../config.php");
$completter = $_GET['compName'];
$colour     = $_GET['col']; //this isn't just colour, it's like the 'variable'
$pos        = $_GET['pos'];
//print_r($_GET);

//if it's a streamdeck we need to do a convert
if ($pos=="sd") {
$pos = "timeTo";
$t=time();
$t=$t+$colour*60;
$colour = date("Y-m-d H:i:s",$t);
}
//find the current thing to make sure it's zero

$sql = 'SELECT '.$pos. ' FROM comps WHERE compLetters = ? ';
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $completter);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($c);
$stmt->fetch();
$stmt->close();

if ($pos=="timeTo" || $pos=="timeTwo") {
 if ($colour!=0) {$colour = time() + intval($colour)*60;}
}

if ($pos=="timeTo") {
 if ($colour!=0) {
 $sql = 'UPDATE comps SET leftLight=0, centreLight=0, rightLight=0,' . $pos. ' = from_unixtime(' . $colour . ') WHERE compLetters = "' . $completter . '"';
} else {
 $sql = 'UPDATE comps SET ' . $pos. ' = from_unixtime(' . $colour . ') WHERE compLetters = "' . $completter . '"';
}
} elseif ($pos=="timeTwo") {
 $sql = 'UPDATE comps SET ' . $pos. ' = from_unixtime(' . $colour . ') WHERE compLetters = "' . $completter . '"';
} else {
 $sql = 'UPDATE comps SET timeTo=0,' . $pos. ' = "' . $colour . '" WHERE compLetters = "' . $completter . '"';
}
if ($conn->query($sql) === TRUE) {
	//do something here
	} else {
	// fail what to do

	}


$conn->close();
?>

