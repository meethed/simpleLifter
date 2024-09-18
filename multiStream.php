<?php
include_once("./includes/config.inc");
$cL=filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);
$s="select fed from comp where compLetters='".$cL."'";
$result=$conn->query($s);
$fed=$result->fetch_assoc()["fed"];
?>
<!DOCTYPE HTML>
<html>
<head>

<title>simpleLifter OBS Livestream</title>
<link rel="stylesheet" href="./resources/stream.css">
<?php if ($_GET["c"]=="AAB") { ?> <link rel="stylesheet" href="./resources/redstream.css"> <?php ;} ?>
<meta name="robots" content="noindex">
</head>
<body>
<div id="lifterInfo">
  <div class="bg bg1"></div>
  <div class="bg bg2"></div>
  <div class="bg bg3"></div>
	<span class="name" id="lifterName">Test Layout</span>
	<div class="cnt"><div id="labelPos" class="lbl">Place: </div><span class="place" id="lifterPos">1st</span></div>
        <div class="cnt"><div id="labelTotal" class="lbl">Total: </div><span class="total" id="lifterTot">500kg</span></div>
        <div class="cnt"><div id="labelTeam" class="lbl">Team: </div><span class="team" id="lifterTm">VIC</span></div>
	<div class="cnt"><div id="labelCat" class="lbl">Weight Class: </div><span class="wc" id="lifterCat">120kg</span></div>
	<div class="cnt"><div id="labelAge" class="lbl">Age Division: </div><span class="agediv" id="lifterDiv">120kg</span></div>
	<div class="cnt"><div id="labelPB" class="lbl">Squat PB: </div><span class="pbL" id="lifterPB">120kg</span></div>
	<div class="cnt"><div id="labelTotal" class="lbl">Total PB: </div><span class="pbT" id="lifterPBT">120kg</span></div>
	<img id="pic" style="z-index: -1; display: none;"  src="./users/pics/blank.jpg"/>
</div>
<div id="lifterAttempt">
	<div class="cont"><div class="name" id="attemptName">Test Layout</div></div>
  <!-- <span class="place"></span> -->
	<div class="cont gd num"><div class="aa" id="attemptNum1">240kg</div></div>
	<div class="cont bd num"><div class="aa" id="attemptNum2">250kg</div></div>
	<div class="cont at num"><div class="aa" id="attemptNum3">260kg</div></div>
  <div class="box">
  	<div class="cont at num" id="cTot"><div class="total" id="attemptTot">660</div></div>
  </div>	
<div class="cont"><div class="gouge" id="attemptGouge">Open Men Equipped -120kg</div></div>
  <div class="cont timer" id="timer1Box"></div>
  <div class="cont timer" id="timer3Box"></div>
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

</body>
<script src="./simpleLifter2/feds/<?php echo $fed;?>rules.js"></script>
<script src="streamteams.js"></script>
<script src="./simpleLifter2/lightsTimers.js"></script>
<script>
if (window.innerWidth==1920 && window.innerHeight==1080) OBSsettings.style.display="none";




const ordinalPlace = (n) =>n+(n%10==1&&n%100!=11?'st':n%10==2&&n%100!=12?'nd':n%10==3&&n%100!=13?'rd':'th');

let tickInterval,
 compStatus={},
 si=0,
 cL="<?php echo $cL;?>";
 simple="<?php echo filter_input(INPUT_GET, 'i', FILTER_SANITIZE_STRING); ?>",
 sesh="<?php echo filter_input(INPUT_GET,'f',FILTER_SANITIZE_STRING); ?>",
 oldLifter="",
 oldLift="";
tick();

function toggleInfo() {
if (!simple) { //if it's simple then never display this bit
  document.getElementById("lifterInfo").classList.remove("visible");
  var oldnode = document.getElementById("lifterInfo");
  var newnode = oldnode.cloneNode(true);
  oldnode.parentNode.replaceChild(newnode,oldnode);
  document.getElementById("lifterInfo").classList.add("visible");
}
}

function showAttempt() {
  document.getElementById("lifterAttempt").classList.add("visible");
  lifterInfo.className="";
}
function hideAttempt() {
  document.getElementById("lifterAttempt").classList.remove("visible");
}


function tick() { //tick Interval
  fetch(`./simpleLifter2/loadsetup.php?c=${cL}&f=${sesh}`).then(response => response.json()).then(data => {
    if (data.updated != compStatus.updated) {
      interpret(data);
    }
    else {tickInterval=setTimeout(tick,2000);};
  });
}

function interpret(ncs) { //call this one when something has changed

  fetch(`./simpleLifter2/load.php?c=${cL}&f=${ncs.session}`).then(response => response.json()).then(data => {
    let newLifter=data[data.findIndex(e => e.idx==ncs.activeLifter)];
    let nextLifter=data[data.findIndex(e => e.idx==ncs.nextLifter)];
    let thirdLifter=data[data.findIndex(e => e.idx==ncs.thirdLifter)];
    let lights=[parseInt(ncs.l), parseInt(ncs.c), parseInt(ncs.r)];

    let lightson=Math.max(...lights); //if the lights array has any value greater than 0
    let a=0; //a counts if timers are set. a=0 means no timers
    if (ncs.timeTo!=compStatus.timeTo) {
      let t1=new Date(convertTime(ncs.timeTo)).valueOf() ||0;
      a+=setTimer(timer1Box,t1);
    }
    if (ncs.timeThree!=compStatus.timeThree) {
      let t3=new Date(convertTime(ncs.timeThree)).valueOf() ||0;
      a+=setTimer(timer3Box,t3);
    }

  drawLights(lightsBox,lights);

  compStatus=JSON.parse(JSON.stringify(ncs));

  if (a || lightson) {showAttempt();} else {hideAttempt(); updateLifter(newLifter,nextLifter,thirdLifter);oldLifter=JSON.stringify(newLifter);}; //it only updates when there are no timers or lights on. but this is a problem when there are lights on and we need to get ready for the next lifter
  tickInterval=setTimeout(tick,1000);
  });
} //end function interpret

function updateLifter(a,n,t) { //function updateLifter using [a]ctive lifter, [n]ext lifter, [t]hird lifter
    if (JSON.stringify(a)==oldLifter && compStatus.activeLift==oldLift) return 0;
  if (!["s","b","d"].includes(compStatus.activeLift[0])) return 0;
  if (a.wc==1000) a.wc=bw[a.gender][bw[a.gender].length-2]+"+";

  ["name","place","total","team","wc","agediv"].forEach((e) => {
    document.querySelectorAll(`.${e}`).forEach(d => d.innerHTML=a[e]||"");
  });
  let al=compStatus.activeLift;
  if (al[0]=="s") al="Squat " + al.slice(-1);
  if (al[0]=="b") al="Bench Press " + al.slice(-1);
  if (al[0]=="d") al="Deadlift " + al.slice(-1);
  document.querySelectorAll(".name").forEach(e => e.innerHTML+= " - "+isNaN(parseFloat(a[compStatus.activeLift])) ? "" : parseFloat(a[compStatus.activeLift])+"kg");
  document.querySelectorAll(".place").forEach(e => e.innerHTML=ordinalPlace(e.innerHTML));
  document.querySelectorAll(".total, .wc, .bw").forEach(e => e.innerHTML=e.innerHTML+"kg");
  al=compStatus.activeLift;
  if (al[0]=="s") {aa="sq";aas="sa";}
  if (al[0]=="b") {aa="bp";aas="ba";}
  if (al[0]=="d") {aa="dl";aas="da";}
  labelPB.innerHTML= aa=="sq" ? "Squat PB: " : aa=="bp" ?  "Bench Press PB: " : "Deadlift PB: ";
  lifterPB.innerHTML=(a["pb"+al[0]]) ? a["pb"+al[0]]+"kg" : a["b"+al.slice(0,2)]+"kg*";
  lifterPBT.innerHTML=(a["pbt"]) ? a["pbt"]+"kg" : a["total"]+"kg*";
  let atts=document.querySelectorAll(".aa");
    atts.forEach((e,i) => {
      e.innerHTML=a[aa+(i+1)] || "-";
      if (i==al[2]-1) e.className="active aa";
      e.parentElement.className=a[aas+(i+1)]==1 ? "cont gd num" : a[aas+(i+1)]==-1 ? "cont bd num" : "cont at num";
    });
  let e=compStatus.activeLift;
  e = e[0]=="s" ? "Squat " + e[2] : e[0]=="b" ? "Bench Press "+e[2] : "Deadlift "+e[2];
  attemptGouge.innerHTML=`(${e}) ${a.division} - ${a.wc}kg ${a.agediv}`;

  //if we've got this far and updated the lifter data that means that we need to start the animation again

  lifterInfo.className="left";
  setTimeout(function () {lifterInfo.className="centre"},500);
  setTimeout(function () {lifterInfo.className="right"},5500);

  oldLifter=JSON.stringify(a);
  oldLift=compStatus.activeLift;
} //end function updateLiftre



</script>

</html>
