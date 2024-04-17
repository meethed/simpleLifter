<?php 
include_once("./includes/config.inc");
$cl=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
$sql="select compName,startdate,enddate from comps where compLetters='".$cl."'";
$result=$conn->query($sql)->fetch_assoc();
$y=explode("-",$result["startdate"])[0];
 ?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./resources/newstyles.css">
  <style>
  td,th {border:1px solid black;}
  table {border-collapse:collapse;}
  td.y {background: #7f7;}
  td.n {background: #f77;}
  td.at {background: #ccc;}
  </style>
</head>
<body>
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">
<div class="heading">Archived competition results - <?php echo $result["compName"];?></div>
<p>PLEASE NOTE: These results were generated from legacy simpleLifter so some of the data may be incomplete. IPF Points or Glossbrenner will not be calculated for these lifters - it was not stored in the save file, it was calculated live and it was decided not to replicate that code.If the weightclass is missing it is because simpleLifter calculated the weightclass from the lifter's bodyweight rather than using a predetermined weight class. It is safe to assume that these lifters lifted in their calculated weight class.</p>
<table id="tableu">
<tr>
<th>Place</th>
<th>Name</th>
<th>Sex</th>
<th>BirthDate</th>
<th>Age</th>
<th>Equipment</th>
<th>Division</th>
<th>BodyweightKg</th>
<th>WeightClassKg</th>   
<th>Squat1Kg</th>
<th>Squat2Kg</th>
<th>Squat3Kg</th>
<th>Best3SquatKg</th>
<th>Bench1Kg</th>
<th>Bench2Kg</th>
<th>Bench3Kg</th>
<th>Best3BenchKg</th>
<th>Deadlift1Kg</th>
<th>Deadlift2Kg</th>
<th>Deadlift3Kg</th>
<th>Best3DeadliftKg</th>
<th>TotalKg</th>
<th>Event</th>
<th>Points</th>
<th>Team</th>
</tr>
<?php 
$fn="./archive/simpleLifter/integrate/data/".$cl.".json";
$cf=fopen($fn,"r") or die ("file error");
$lifters=json_decode(fread($cf,filesize($fn)));
$liftlist=$lifters->liftList;

foreach ($liftlist as $l) {
$div=explode("-",$l->division);
echo "<tr>";
echo "<td>".$l->place."</td>";
echo "<td>".$l->name."</td>";
if (empty($l->gender)) {echo "<td>".$div[0]."</td>";} else {echo "<td>".$l->gender."</td>";}
echo "<td>".$l->year."</td>";
echo "<td>".(intval($y)-intval($l->year))."</td>";
if (empty($l->gear)) {echo "<td>".$div[1]."</td>";} else {echo "<td>".$l->gear."</td>";}
echo "<td>".$l->division."</td>";
echo "<td>".$l->bw."</td>";
echo "<td>".$l->wc."</td>";

if ($l->sq->s1==-1) $s1="n'>";
if ($l->sq->s2==-1) $s2="n'>";
if ($l->sq->s3==-1) $s3="n'>";
if ($l->sq->s1==1) $s1="y'>";
if ($l->sq->s2==1) $s2="y'>";
if ($l->sq->s3==1) $s3="y'>";
$ss1=$l->sq->s1*$l->sq->a1;
$ss2=$l->sq->s2*$l->sq->a2;
$ss3=$l->sq->s3*$l->sq->a3;
$s1.=$ss1;
$s2.=$ss2;
$s3.=$ss3;
$bsq=max($ss1,$ss2,$ss3,0);
if ($ss1) {echo "<td class='".$s1."</td>";} else {echo "<td class='at'>".$l->sq->a1."</td>";};
if ($ss2) {echo "<td class='".$s2."</td>";} else {echo "<td class='at'>".$l->sq->a2."</td>";};
if ($ss3) {echo "<td class='".$s3."</td>";} else {echo "<td class='at'>".$l->sq->a3."</td>";};

echo "<td>".$bsq."</td>";

if ($l->bp->s1==-1) $s1="n'>";
if ($l->bp->s2==-1) $s2="n'>";
if ($l->bp->s3==-1) $s3="n'>";
if ($l->bp->s1==1) $s1="y'>";
if ($l->bp->s2==1) $s2="y'>";
if ($l->bp->s3==1) $s3="y'>";
$ss1=$l->bp->s1*$l->bp->a1;
$ss2=$l->bp->s2*$l->bp->a2;
$ss3=$l->bp->s3*$l->bp->a3;
$s1.=$ss1;
$s2.=$ss2;
$s3.=$ss3;
$bsq=max($ss1,$ss2,$ss3,0);
if ($ss1) {echo "<td class='".$s1."</td>";} else {echo "<td class='at'>".$l->bp->a1."</td>";};
if ($ss2) {echo "<td class='".$s2."</td>";} else {echo "<td class='at'>".$l->bp->a2."</td>";};
if ($ss3) {echo "<td class='".$s3."</td>";} else {echo "<td class='at'>".$l->bp->a3."</td>";};
echo "<td>".$bsq."</td>";

if ($l->dl->s1==-1) $s1="n'>";
if ($l->dl->s2==-1) $s2="n'>";
if ($l->dl->s3==-1) $s3="n'>";
if ($l->dl->s1==1) $s1="y'>";
if ($l->dl->s2==1) $s2="y'>";
if ($l->dl->s3==1) $s3="y'>";
$ss1=$l->dl->s1*$l->dl->a1;
$ss2=$l->dl->s2*$l->dl->a2;
$ss3=$l->dl->s3*$l->dl->a3;
$s1.=$ss1;
$s2.=$ss2;
$s3.=$ss3;
$bsq=max($ss1,$ss2,$ss3,0);
if ($ss1) {echo "<td class='".$s1."</td>";} else {echo "<td class='at'>".$l->dl->a1."</td>";};
if ($ss2) {echo "<td class='".$s2."</td>";} else {echo "<td class='at'>".$l->dl->a2."</td>";};
if ($ss3) {echo "<td class='".$s3."</td>";} else {echo "<td class='at'>".$l->dl->a3."</td>";};

echo "<td>".$bsq."</td>";

$total=floatval($bsq)+floatval($bbp)+floatval($bdl);
echo "<td>".$total."</td>";
if (empty($l->lifts)) {echo "<td>".$div[2]."</td>";} else {echo "<td>".$l->lifts."</td>";}
echo "<td>".$l->pt."</td>";
echo "<td>".$l->team."</td>";
echo "</tr>";
}
?>
</table>

<!-- End Content -->
</div>
<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
</html>

