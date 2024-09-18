<?php
include_once("./includes/config.inc");
$cL=$_SESSION["compLetters"];
$messages=[];
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);
foreach($data as $l) {
  if (!empty($l["idx"])) {
    $statement="";
    foreach($l as $key => $value) {
      if (!$value) {$value="NULL";} else {$value=strip_tags(htmlspecialchars($value,ENT_QUOTES));};
      if (!is_numeric($value) && ($value!="NULL")) {$value="'". $value."'";};
      if ($key!="idx") {$statement = $statement.$key."=".$value.",";};
    }
    $statement = substr($statement, 0, -1);
    $sql="UPDATE ".$cL. " set ".$statement." where idx=". $l["idx"];
    $result=$conn->query($sql);
    array_push($messages,$sql);
  }
}

echo json_encode($messages);

?>

