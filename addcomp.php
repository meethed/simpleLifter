<?php

 include_once "../config.php";

  $compName = test_input($_POST["newCompName"]);
  $startdate = $_POST["startdate"];
  $enddate = $_POST["enddate"];
  $pwd = test_input($_POST["pwd"]);
  $cnt = test_input($_POST["contact"]);


if ($compName == "") die("Bruv the competition name was blank do it again");

//get the next comp letter sequence
$str = "SELECT compLetters, compName FROM comps ORDER BY compLetters DESC limit 1";
$result = $conn->query($str);
$data = $result->fetch_assoc();
$lastcomp= $data["compLetters"];
$oldname = $data["compName"];

//close query
$result->close;
if ($oldname == $compName) {
echo "Error duplicate competition";

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
$sql = $conn->prepare("INSERT INTO comps (compLetters, compName,startdate,enddate,hish,contact,leftLight,centreLight,rightLight,timeTo,timeTwo) VALUES  (?,?,?,?,?,?,?,?,?,?,?)");
$z = 0;
$zd ="0";
$sql->bind_param("ssssssiiiii",$lastcomp,$compName,$startDate, $endDate, $cpwd, $cnt,$z,$z,$z,$z,$z);
$sql->execute();

$sql->close();
$conn->close();

}
?>
<html>
<head>
<title>New Competition!</title>
<link rel="stylesheet" href="./resources/styles.css">
</head>
<body>
<div class="container">
<h1>Your competition has been added</h1>
<div class="nicebox">
Details - please make sure they're right!
<br>
<br>

<?php
echo "Name of the competition:<br>".$compName."<br><br>";
echo "Stat Date:<br>".$startDate."<br><br>";
echo "End Date:<br>".$endDate."<br><br>";
echo "Access Code - make sure you share this with the other referees:<br>".$pwd."<br><br>";
echo "Competition code:<br>".$lastcomp."<br><br>";
?>
<br>
If you'll be using the spreadsheet and not just the lights, you might need this:<br>
<a href="./simpleUpload.xlsm">simpleUpload excel spreadsheet</a><br>
Note!! You must save it to your hard drive (eg. My Documents) and enable macros (which is slightly confusing to do). Follow the instructions here: <a href="https://support.microsoft.com/en-us/topic/a-potentially-dangerous-macro-has-been-blocked-0952faa0-37e7-4316-b61d-5b5ed6024216">A potentially dangerous macro has been blocked - Microsoft Support</a>
If you have any issues, contact:
@compofthefuture on instagram
</div>
</div>
</body>
</html>


