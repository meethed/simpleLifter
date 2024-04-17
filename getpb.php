<?php
$n = filter_input(INPUT_GET,"n",FILTER_SANITIZE_STRING);
$f = fopen("https://www.openpowerlifting.org/api/liftercsv/".$n ,"r");
$bs=0;$bb=0;$bd=0;$bt=0;
if ($f) {
  while (($data=fgetcsv($f,0,","))) {
    for ($i=0;$i < count($data); $i++) {
      if ($data[14]>$bs) $bs=$data[14];
      if ($data[19]>$bb) $bb=$data[19];
      if ($data[24]>$bd) $bd=$data[24];
      if ($data[25]>$bt) $bt=$data[25];
    }
  }
}
$js = array("pbs" => $bs, "pbb" => $bb, "pbd" => $bd, "pbt" => $bt);
echo json_encode($js);
?>
