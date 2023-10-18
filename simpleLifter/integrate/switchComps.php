
  <?php
// Create connection
$conn = new mysqli('localhost', 'lightsuser','lights','lightsdb');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT compLetters, compID, compName, hish FROM comps WHERE compLetters=\"" . $_POST["compName"]. "\"";
$result = $conn->query($sql);


if ($result->num_rows > 0)  //if there's a competition that matches the compLetters - now there's only 1 unique
  // competition so if the database is corrupted this will be a bit messy but that can't happen as that isn't
  // how the db is set up
  // output data of each row
  while($row = $result->fetch_assoc()) 
    if ($row["hish"]==crypt($_POST["pwd"], substr($row["compLetters"],1,2))) {
  $cl = $row["compLetters"];
  $cn = $row["compName"];
  echo $cl;
  $failed=0;}
    else {$failed=1;};
$conn->close();
?>
