<?php
include_once("./includes/config.inc");

//first check if this is a qrcode redirect

if (isset($_GET["token"]) & isset($_GET["ts"])) {
  $t=filter_input(INPUT_GET,"token",FILTER_SANITIZE_STRING);
  $ts=filter_input(INPUT_GET,"ts",FILTER_SANITIZE_NUMBER_INT)/1000;
  $cl=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);

  $sql="select * from comp where compLetters='".$cl."'";
  $result=$conn->query($sql)->fetch_assoc();;
  print_r($result);
  echo ($t)."\n";
  echo ($ts)."\n";
  echo ($cl)."\n";
  if ($result["token"]==$t & $result["tokenexp"]>$ts) { //successful login
    echo ("made it");
    $_SESSION=$result;
    header("location: manager.php");
    $_SESSION["message"]="Logged in from QR Code";
  } else { header("location: index.php");}
  
}


//check if we've logged in
if (!isset($_SESSION["idx"])) {
  header("location: index.php");
}

if ($_SESSION["message"]=="Session activated") {
  header("location: login.php?ss=1");
}

?>
<!DOCTYPE html>
<html>
<head>
<script src="qrcode.min.js"></script>
<title>simpleLifter competition management</title>
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
<!-- Header -->
  <div class="heading">Competition Management - <?php echo $_SESSION["compName"];?></div>
<!-- End Header -->
<form class="frmInput" id="managerform">
  <?php
    $ss=explode(",",$_SESSION["seshs"]);
    if (count($ss)>1) { //if there's more than one session
      echo "<div class='sessionflexbox'><h2>Choose your session:</h2>";
      foreach($ss as $e) {
        $d[]=$e[0];
        $s[]=$e[1];
        $p[]=$e[2];
      }
      foreach($ss as $sesh) {
        $bigSesh="";
        if (max($d)>1) {$bigSesh.="Day ".$sesh[0];}
        if (max($s)>1) {$bigSesh.=" Session ".$sesh[1];}
        if (max($p)>1) {$bigSesh.=" Platform ".$sesh[2];}
        echo "<div class='sessionselect sesh' id='".$sesh."'>".$bigSesh."</div>";
      }
    echo "</div><div class='spacer'></div>";
    }
  ?>

  <!-- Flexbox with position options to choose -->
  <div class="sessionflexbox">
    <h2>Choose your function:</h2>
    <div style="display:block; width: 100%;" id="hideme"></div>
    <?php if ($_SESSION["sheet"]==1) {?><div id="t" class="sessionselect tech">Tech Desk</div><?php };?>
    <?php if ($_SESSION["sheet"]==1) {?><div id="w" class="sessionselect tech">Weigh In</div><?php };?>
    <div id="l" class="sessionselect ref">L Ref</div>
    <div id="c" class="sessionselect ref">C Ref</div>
    <div id="r" class="sessionselect ref">R Ref</div>
    <?php if ($_SESSION["sheet"]==1) {?><div id="pl" class="sessionselect displatform">Current Lifter & Lights/Timers</div><?php };?>
    <?php if ($_SESSION["sheet"]==1) {?><div id="pn" class="sessionselect displatform">Platform Display (No Lights)</div><?php };?>
    <?php if ($_SESSION["lights"]==1) {?><div id="o" class="sessionselect platform">Lights & Timers Only</div><?php };?>
    <?php if ($_SESSION["sheet"]==1) {?><div id="lo" class="sessionselect order">Lifting Order</div><?php };?>
    <div id="s" class="sessionselect admin">Comp Setup...</div>
  </div>
</form>
<div class="spacer"></div>


<!-- QR Code to share the link -->

<div class="heading">QR Code - share this with other referees</div>
<div id="qrcode"></div>
<script>new QRCode(document.getElementById("qrcode"),window.location.href+"?token=<?php echo $_SESSION["token"];?>&ts="+Date.now()+"&c=<?php echo $_SESSION["compLetters"];?>");</script>

	<div class="heading"></div>
	<img class="footer-image" src="./resources/happyevan.jpg"></img>

<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php");?>
<!-- End Footer -->

</body>
<script>
document.addEventListener("click", clickhandler);

function clickhandler(e) {

  if (e.target.classList.contains("sesh")) {
  ss=document.querySelectorAll(".selected");
  ss.forEach((s) => {s.classList.remove("selected")})
  e.target.classList.add("selected");
  document.getElementById("s").scrollIntoView({behavior: "auto",block:"center",inline:"center"});
  }

    let a=document.querySelectorAll(".selected");
    if (a.length==1) {
      active=a[0].id;
    } else {active=""};
  

  switch (e.target.id) {
    case "l":
    case "c":
    case "r":
      window.location="ref.php?f="+active+"&pos="+e.target.id;
    break
    case "t":
      window.location="simpleLifter2.php?f="+active;
    break
    case "w":
      window.location="weighin.php?f="+active;
    break
    case "pl":
      window.location="platform.php?f="+active+"&audience";
    break
    case "pn":
      window.location="platform.php?f="+active+"&notimer";
    break
    case "lo":
      window.location="lifting.php?f="+active;
    break
    case "o":
      window.location="lights.php?f="+active;
    break
    case "s":
      window.location="setup.php";
    break

}


} //end clickhandler

</script>
</html>

