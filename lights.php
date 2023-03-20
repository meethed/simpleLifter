<?php 
include_once "../config.php";
 $sql = "SELECT * FROM comps WHERE compLetters = ?"; 
$stmt = $conn->prepare($sql); 
$comp = test_input($_GET["compLetters"]); 
$stmt->bind_param("s",$comp); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$row=$result->fetch_assoc(); 
$row["timeTo"]=strtotime($row["timeTo"]);
$row["timeTwo"]=strtotime($row["timeTwo"]);
echo json_encode($row); 
$conn->close(); ?>
