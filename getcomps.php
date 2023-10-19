<?php 
include_once "../config.php";
 if ($cc=filter_input(INPUT_GET, "c", FILTER_SANITIZE_STRING) ) {
  $stmt=$conn->prepare("SELECT parentComp from comps where compLetters = ?");
  $stmt->bind_param("s",$cc);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
 
  $stmt=$conn->prepare("SELECT compName,compLetters from comps where parentComp=? AND enddate >= curdate() AND NOT compLetters=?");
  $stmt->bind_param("ss",$result["parentComp"],$result["parentComp"]);

} else {
 $stmt=$conn->prepare("SELECT isChild,parentComp,isParent, compName, compLetters from comps WHERE enddate >= curdate()"); 
}
  $stmt->execute(); 
  $result = $stmt->get_result(); 
  while ($row =  $result->fetch_assoc()) {
    echo $row["compName"].",". $row["compLetters"].",";
  }

  $conn->close(); ?>
