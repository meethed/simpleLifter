<?php include_once("./includes/config.inc");
if (!isset($_SESSION["compLetters"])) {header("Location: index.php");};
  if (isset($_GET["f"])) {
    $f = filter_input(INPUT_GET,f,FILTER_SANITIZE_STRING); //get the comp session from the url
    $_SESSION["sesh"]=$f;
    header("location: simpleLifter2.php");
  } else { //if there's nothing in the GET url
    if (!isset($_SESSION["sesh"])) {$_SESSION["sesh"]="111"; header("location: simpleLifter2.php");} //set session to 111 and reload

  }



if (!isset($_SESSION["idx"])) {
  header("location: index.php");
}
$sql= "select fed from comp where compLetters='".$_SESSION['compLetters']."'";
$fed=$conn->query($sql)->fetch_assoc()["fed"];
if (!$fed) {$fed="IPF";};
?>



<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./resources/simplestyles.css">
</head>
<body>

<!-- Content -->
<div class="content">

<!-- Start Statusbar (at the top -->
<div class="statusbar">
<!-- navbar -->
<?php include(ROOT_PATH."/headerSimple.php"); ?>
<!-- end navbar -->
  <div class="lifter-status" id="activeStatus"> <!-- Current status part showing who is up -->
    <div id="lifterName">Testing Terrence Triple - 500kg</div>
    <div id="lifterRack">Squat Rack: 15</div>
  </div>
  <div class="plate-frame" id="activePlates"></div>
  <div class="lifter-status" id="nextStatus"> <!-- Next lifter status part showing who is up -->
    <div id="nextName">Testing Terrence Triple - 500kg</div>
  </div>
 <div class="plate-frame visible" id="lightsTimers">
    <div class="time-text" id="blt" onclick="doBarLoaded();">01 : 00</div>
    <div class="time-text" id="nast"> </div>
    <div id="lightsBox" class="light-frame">
     <div id="bigLights" class="light-box">
       <div id="bl" class="light"></div>
       <div id="bc" class="light"></div>
       <div id="br" class="light"></div>
    </div>
    <div id="smolLights" class="light-box">
      <div id="sl" class="light"></div>
      <div id="sc" class="light"></div>
      <div id="sr" class="light"></div>
    </div>
  </div>
 </div>
 
</div> <!-- End Statusbar area -->

<div class="comp-table" id="comp-table">
<div class="tr">
<div class="th g">Gp</div>
<div class="th l">Lot</div>
<div class="th name">Lifter Name</div>
<div class="th team">Team</div>
<div class="th yearr">Birth Year</div>
<div class="th ad">Age Division</div>
<div class="th division">Division</div>
<div class="th bw">BW</div>
<div class="th wc">Class</div>
<div class="th sr">S Rack</div>
<div class="th br">B Rack</div>
<div class="th sq1">SQ-1</div>
<div class="th sq2">SQ-2</div>
<div class="th sq3">SQ-3</div>
<div class="th bsq">Best SQ</div>
<div class="th bp1">BP-1</div>
<div class="th bp2">BP-2</div>
<div class="th bp3">BP-3</div>
<div class="th bbp">Best BP</div>
<div class="th st">Subtotal</div>
<div class="th dl1">DL-1</div>
<div class="th dl2">DL-2</div>
<div class="th dl3">DL-3</div>
<div class="th bdl">Best DL</div>
<div class="th total">Total</div>
<div class="th points">Points</div>
</div>
</div>
<div id="addBtn" class="btn" onclick="addFromBtn();">Add new lifter...</div>
<div class="loading" id="loading"></div>

<dialog class="alert-box" id="alertBox">
  <form id="alertBoxForm">
    <div class="alert-box-text" id="alertBoxText"></div>
    <div class="alert-box-container" id="alertBoxContainer"></div>
    <button value="default" class="alert-box-ok" id="alertBoxOk" name="OK">Ok</button>
    <button value="cancel" formmethod="dialog" class="alert-box-ok" id="alertBoxCx" name="Cancel">Cancel</button>
  </form>
</dialog>



<!-- End Content -->
</div>
<!-- Templates for cloning -->
<div class="templates" style="display:none">
<div class="tr" id="templateLifterRow">
<div class="td g" contenteditable>A</div>
<div class="td l" contenteditable>1</div>
<div class="td name" contenteditable ondblclick="setMeActiveLifter(this);">Testing Terrence triple</div>
<div class="td team" contenteditable>TEST</div>
<div class="td year" contenteditable>1990</div>
<div class="td ad ro">O</div>
<div class="td division" contenteditable>M-CL-PL</div>
<div class="td bw" contenteditable>95.5kg</div>
<div class="td wc ro" ondblclick="setWeightClass(this);">105kg</div>
<div class="td sr" contenteditable>15</div>
<div class="td br" contenteditable>16/7</div>
<div class="td sq1 attempt" contenteditable>205</div>
<div class="td sq2 attempt" contenteditable>312.5</div>
<div class="td sq3 attempt" contenteditable>500</div>
<div class="td bsq ro">312.5</div>
<div class="td bp1 attempt" contenteditable>100</div>
<div class="td bp2 attempt" contenteditable>150</div>
<div class="td bp3 attempt" contenteditable>177.5</div>
<div class="td bbp ro">177.5</div>
<div class="td st ro">677.5</div>
<div class="td dl1 attempt" contenteditable>312.5</div>
<div class="td dl2 attempt" contenteditable>322.5</div>
<div class="td dl3 attempt" contenteditable>347.5</div>
<div class="td bdl ro">347.5</div>
<div class="td total ro">1050</div>
<div class="td points ro">91.34</div>
</div>

<table class="results-table" id="resultsTable"></table>

</div>
<!-- No more templates -->
</div>
<!-- Footer -->
<?php include(ROOT_PATH."/footerSimple.php"); ?>
<!-- End Footer -->
</body>
<script>
<?php if (empty($_SESSION["sesh"])) {$_SESSION["sesh"]=111;};
echo "let globalSession='".$_SESSION["sesh"]; ?>';
const compLetters="<?php echo $_SESSION["compLetters"];?>";
const fed="<?php echo $fed; ?>";
let globalLifters=[];
let lifters=[];
let compStatus=[];
let setupPopup;
</script>
<?php
  echo "<script src='./simpleLifter2/feds/".$fed."rules.js'></script>";
  foreach(glob("./simpleLifter2/*.js") as $f) {
    echo "<script src='".$f."'></script>\n";
  }
?>

<script>
fedPrep();
getStatus(); //load up


document.getElementById("loading").classList.add("loading");
setTimeout(function() { //delay 1 second after the first load
  alert("Note you can only have one instance of the 'Tech Desk' screen showing otherwise they will conflict with each other by sending updates to the server. This is a known issue that is being worked on, but not yet resolved. Please do not have the 'Tech Desk' loaded twice.");
  loadfromserver(); //load the lifter data 
  tick(); //set the status checker going

  }
,1000);


</script>
</html>

