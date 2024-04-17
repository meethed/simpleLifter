<?php include_once("./includes/config.inc");
$c=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
$sql ="select sheet, compName,seshs from comp where compLetters='".$c."'";
$result=$conn->query($sql)->fetch_assoc();
$compName=$result["compName"];
$sheet=$result["sheet"];
$dsp=explode(",",$result["seshs"]);
foreach($dsp as $e) {
  $d[]=$e[0];
  $s[]=$e[1];
  $p[]=$e[2];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter - Live View</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body>
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">
<div class="heading">Live Comp View - <?php echo $compName; ?></div>
<h1>
<?php
$sql = "select compLetters,session,streamURL,id from compstatus where compLetters='".$c."'";
$result=$conn->query($sql)->fetch_all(MYSQLI_ASSOC);
if (sizeof($result) >1) {
  echo "<select name='seshs' id='seshchange'>";
  echo "<option id='all'>All sessions</option>";
  foreach ($result as $r) {
    $dsp="";
    $sesh=$r["session"];
    if (max($d)>1) {$dsp.="Day ".$sesh[0];}
    if (max($s)>1) {$dsp.=" Session ".$sesh[1];}    
    if (max($p)>1) {$dsp.=" Platform ".$sesh[2];}
    echo "<option id='" . $r["id"] . "'>" . $dsp . "</option>";
  }
  echo "</select><br>";
} else {
  $streamURL=$result[0]["streamURL"];
}
?>
</h1>
<div id="alignme">
<!-- iFrame for youtube -->
<div id="containertubeframe"><iframe id="tubeframe" src="<?php echo $r["streamURL"]; ?>"></iframe></div>
<div id="containerliveframe" <?php if (!$sheet) {echo "style='display:none'";}?>><iframe id ="liveframe" src="./results.php?iframe=1&c=<?php echo $c."&f=111";?>"></iframe></div>
</div>
<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script>

var multidata=<?php echo json_encode($result); ?>;
var compLetters="<?php echo $c; ?>";
setSesh();


document.addEventListener("change", e => {
  if (e.target.id=="seshchange")  //if we've changed the session
   setSesh(e);
});

function setSesh(e) {
let id,idx;
  if (!e) {//this is the initial setup 
  id="all";
  idx=0;
  } else {
   id=e.target.options[e.target.options.selectedIndex].id;
   idx=multidata.findIndex(e => e.id==id);
  }
  if (id=="all") {
    liveframe.src="./results.php?iframe=1&c="+compLetters;
    liveframe.parentElement.classList.add("onlyresults");
      tubeframe.parentElement.classList.remove("showtube");
  } else {
    tubeframe.src=multidata[idx].streamURL || "";
    if (multidata[idx].streamURL!=null) {
      tubeframe.parentElement.classList.add("showtube");
      liveframe.parentElement.classList.remove("onlyresults");
    } else {
      tubeframe.parentElement.classList.remove("showtube");
      liveframe.parentElement.classList.add("onlyresults")
    };
    liveframe.src="./results.php?iframe=1&c="+compLetters+"&f="+multidata[idx].session;
  }
}


</script>
</html>

