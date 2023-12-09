<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./resources/styles.css">
<title>archived comps</title>
</head>

<body>
<div id="only" class="containertb">
 <h1>Archived Competition Results</h1>

 <?php
include_once "../config.php";
// check connection
if ($conn->connect_error) {
  echo '<div class="warning">Connection failed: ' . $conn->connect_error . '</div>';
  die("connection error");
}

$sql = "SELECT isParent, compLetters,compName,startdate,enddate FROM comps WHERE (enddate <= curdate()) AND NOT (isChild=1)";
$result = $conn->query($sql);

//start a new scrollable div
$dummy=$result->fetch_assoc(); //ignore the first one
if ($result->num_rows > 0) {
  //output data of each row
  while($row = $result->fetch_assoc()) {
   if ($row["isParent"]=1 || file_exists("./simpleLifter/integrate/data/".$row["compLetters"].".json"))
    echo "<a href='./simpleLifter/integrate/dead.php?c=".$row["compLetters"] ."'>".$row["compName"]." - ".$row["startdate"]." to ".$row["enddate"]. "</a><br>\r\n";

  }
} else {
  echo "<option>There are no archived competitions</option>";
}


$conn->close();

?>
</div>


</body>
</html>
