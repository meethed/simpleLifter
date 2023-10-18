<html>
<head>
<title>DB Dump</title>
</head>
<body>
<table>
<?php
include_once "../../config.php";

$sql="SELECT * FROM comps";
$result=$conn->query($sql);

$row=$result->fetch_assoc();
echo "<tr>";
foreach ($row as $x => $val) {
  echo "<th>".$x."</th>";
}
echo "</tr>";
if ($result->num_rows>0) {
  while ($row = $result->fetch_assoc()) {
   echo "<tr>";
//   print_r($row);
   foreach ($row as $x => $val) {
    echo "<td>".$val."</td>";
   }
  echo "</tr>\n";
  
  }
}


?>
</table>
</body>
</html>
