<?php
 include_once "./includes/config.inc";
 $cL = $_SESSION["compLetters"];
 if (empty($_SESSION["sesh"])) {$dsp="111";} else {$dsp=$_SESSION["sesh"];};
 $stmt = "SELECT updated,l,c,r,timeTo,timeTwo,timeThree FROM compstatus WHERE compLetters = '".$cL."' and session = '".$dsp."'";
 $row = $conn->query($stmt)->fetch_assoc();
 $row["timeTo"]=($row["timeTo"]);
 $row["timeTwo"]=strtotime($row["timeTwo"]);
 $row["timeThree"]=strtotime($row["timeThree"]);
 echo json_encode($row,JSON_NUMERIC_CHECK);
 $conn->close(); ?>
