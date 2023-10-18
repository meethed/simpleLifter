<html>
<head><title>simpleSetup v0.1</title>
<link rel="stylesheet" href="../resources/styles.css">
</head>
<body>
<h1>simpleSetup</h1>
<p>Use this tool to set up a competition. It replaces the MS Excel-based simpleUpload tool, which (obviously) only works on Windows.
<br>
<input type="text" name="inNames">
<br>
<select name="inComps">

<?php
include_once "../../config.php";

$sql = "SELECT * FROM comps WHERE (enddate >= curdate())";
//$sql = '';
$result = $conn->query($sql);

//start a new scrollable div

if ($result->num_rows > 0) {
  //output data of each row
  while($row = $result->fetch_assoc()) {

    echo "<option value='".$row["compLetters"] ."'>".$row["compName"]." - ".$row["startdate"]." to ".$row["enddate"]. "</option>\r\n";
  }
} else {
  echo "<option>There are no active competitions</option>";
}

$conn->close();

?>

</select>

<table>
<th>Comp</th>
<th>Lot</th>
<th>Flight</th>
<th>First Name</th>
<th>Surname / Full Name</th>
<th>Gender</th>
<th>Team</th>
<th>Year of Birth</th>
<th>Equipment?</th>
<th>S/B/D</th>
<tr>
<td><select></select></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>
<td><textarea ></textarea></td>

</tr>
</table>

</html>
