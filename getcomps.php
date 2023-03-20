<?php 
include_once "../config.php";
 $stmt=$conn->prepare("SELECT  compName, compLetters from comps WHERE enddate >= curdate()"); 
  $stmt->execute(); 
  $result = $stmt->get_result(); 
  while ($row =  $result->fetch_assoc()) {
    echo $row["compName"].",". $row["compLetters"].",";
  }
  $conn->close(); ?>
