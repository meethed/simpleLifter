<html>
<head><title>simpleSetup</title></head>
<body>
<?php
$f = $_GET["n"];
$e = strtoupper($_GET["e"][0]) ?? null;
if (!$e) $e="R";

$f = fopen("https://www.openpowerlifting.org/api/liftercsv/".$f ,"r");

if ($f) {

echo "<table>\n";
$bs=0;$bb=0;$bd=0;
while (($data=fgetcsv($f,0,","))) {
echo "<tr>";
for ($i=0;$i < count($data); $i++) {
echo "<td>".$data[$i]."</td>";
}
echo "</tr>\n";

if ($data[3][0]==$e){
if ($data[14]>$bs) $bs=$data[14];
if ($data[19]>$bb) $bb=$data[19];
if ($data[24]>$bd) $bd=$data[24];
}
}
echo "</table>\n";
echo "Best Squat: ".$bs."\n";
echo "Best Bench: ".$bb."\n";
echo "Best Dead: ".$bd."\n";
}

$js = array("s" => $bs, "b" => $bb, "d" => $bd);
echo json_encode($js);
?>
</body>
</html>
