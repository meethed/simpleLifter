<?php

$dir="./simpleLifter/integrate/data";
$n=filter_input(INPUT_GET,"n",FILTER_SANITIZE_STRING); //n = lifter name
$s=filter_input(INPUT_GET,"s",FILTER_SANITIZE_STRING); //s = comp status (sq/bp/dl)
$lifterFile=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING).".json"; //c=comp tri-letter

$jsondata = json_decode(file_get_contents($dir.'/'.$lifterFile),true)["liftList"];
$bestdata = json_decode(file_get_contents($dir.'/best'.$lifterFile),true);

foreach($jsondata as $key=>$value) {
 if ($value["name"]==$n) {
 $ret = $value[$s];
 $ret["place"]=$value["place"];
 foreach ($bestdata as $bkey=>$bvalue) {
   if ($bvalue["name"]==$n) {
    $ret["sqb"]=$bvalue["sqb"];
    $ret["bpb"]=$bvalue["bpb"];
    $ret["dlb"]=$bvalue["dlb"];
    $ret["tpb"]=$bvalue["tpb"];
   }
  }
 echo json_encode($ret);
 }; //end if the lifter name matches
} //end loop


?>
