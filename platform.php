<?php include_once "./includes/config.inc";
  if (!isset($_GET["isPopup"]) && !isset($_SESSION)) header("Location: index.php");
  $cL=$_SESSION["compLetters"];
  if (isset($_GET["f"])) $f=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);
  if (isset($_GET["c"])) $c=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
  if (empty($c)) {$c=$cL;}
  if (empty($f)) {$f=$_SESSION["sesh"];}
  if (isset($_GET["notimer"])) {$notimer=1;} else {$notimer=0;}
?>
<!DOCTYPE html>
<html>
<head>
<title>Lights & Plates</title>
<link rel="stylesheet" href="./resources/plateLights.css">
<meta name="robots" content="noindex">
</head>
<body>
<div id="activeLifter" class="attempt-box">
<div id="aText" class="lifter-text">
  <h1 id="n1">(AUS) Current Attempt</h1>
  <div id="a1" class="attempt">500kg</div>
  <div id="r1" class="rackinfo">SR:15</div>
  </div>
<div id="aPlates" class="plate-frame"></div>
</div>
<div id="nextLifter" class="attempt-box">
<div id="nText" class="lifter-text">
  <h1 id="n2">(AUS) Next Attempt</h1>
  <div id="a2" class="attempt">500kg</div>
  <div id="r2" class="rackinfo">SR:15</div>
</div>
<div id="nPlates" class="plate-frame"></div>
</div>

<div id="lightsTimers">
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

<div id="timersBox">
<div id="timer1Box"></div>
<div id="timer2Box"></div>
<div id="timer3Box"></div>
</div>
</div>


</body>

<script src="./simpleLifter2/plates.js"></script>
<script src="./simpleLifter2/lightsTimers.js"></script>
<script>
<?php if (isset($_GET["audience"])) { ?>
document.getElementById("nextLifter").style.display="none";
document.getElementById("activeLifter").style.height="60%";
document.getElementById("activeLifter").style.top="20%";
document.getElementById("activeLifter").style.position="absolute";
document.body.style.background="linear-gradient(black, #aaa, black)";
<?php }; ?>

function getFlagEmoji(countryCode) {
if (!countryCode) return "";
return String.fromCodePoint(...[...countryCode.toUpperCase()].map(x=>0x1f1a5+x.charCodeAt(0)));
}
const alpha2={"AUT":"AT","AUS":"AU","DEU":"DE","ITA":"IT","GBR":"GB","ZAF":"ZA","CAN":"CA","ESP":"ES","USA":"US","CZE":"CZ","NZ":"NZ"};
let interval=setInterval(checkUpdate,1000);
let oldStatus="";
let lifterData=[];
let compStatus=[];
let aL,nL;
let clear;
drawPlates(aPlates,50,25);
drawPlates(nPlates,320.5,25);


function checkUpdate() {

  fetch("./simpleLifter2/loadsetup.php?c=<?php if (!empty($c)) {echo $c;} if (!empty($f)) {echo "&f=".$f;}; ?>").then(response=>response.json().then(s=>{
    compStatus=s;
    fetch("./simpleLifter2/load.php?f="+compStatus.session).then(response=>response.json().then(d=>{
      lifterData=d;

      if (compStatus.updated!=oldStatus.updated) {
        //plates and names and things
        aL=lifterData.findIndex(e=>e.idx==compStatus.activeLifter) ||0;
        nL=lifterData.findIndex(e=>e.idx==compStatus.nextLifter) ||0;
        drawPlates(aPlates,lifterData[aL][compStatus.activeLift],compStatus.bar||25);
        updateText(aText,aL);
        updateText(nText,nL,1);
        drawPlates(nPlates,lifterData[nL][compStatus.nextLiftIs],compStatus.bar||25);

        <?php if (!$notimer) { ?>
        //lights and timers are drawn on top of the plates
        let l=drawLights(lightsBox,[compStatus.l,compStatus.c,compStatus.r]);
        if (l==1) clear=setTimeout(() => { //get ready with a 5 second timer to clear the lights (not just the display but reset the status)
          drawLights(lightsBox,[0,0,0]);
          lightsTimers.classList.remove("visible");
          fetch("clearLights.php"); //this used to be the centre ref only, but now any platform display / ref will send the signal
        },5000); //if we're showing all of them clear in 5 seconds

        //timers
        var d=Date.now();
        let a=0;
        if (compStatus.timeTo!=oldStatus.timeTo) {
          let t1=new Date(convertTime(compStatus.timeTo)).valueOf() ||0;
          a+=setTimer(timer1Box,t1);
        }
        if (compStatus.timeTo!=oldStatus.timeTo) {
          let t2=new Date(convertTime(compStatus.timeTwo)).valueOf() ||0;
          a+=setTimer(timer2Box,t2);
        }
        if (compStatus.timeThree!=oldStatus.timeThree) {
         let t3=new Date(convertTime(compStatus.timeThree)).valueOf() ||0;
         a+=setTimer(timer3Box,t3);
        }
        if (a>0 || l>0) { // if a timer or light is active
          lightsTimers.classList.add("visible");
        } else {
          lightsTimers.classList.remove("visible");
        };
        <?php } ?>
        //make sure we only update when the compstatus has changed by comparing timestamp. updates the cached timestamp
        oldStatus=compStatus;
      } //end if statuses are different
    })) //end nested fetch 2
  })) //end nested fetch 1
} //end function check update

function updateText(d,l,n) {
  let str="";
  if (lifterData[l].team) {
  d.children[0].innerHTML=`(${lifterData[l].team}) ${getFlagEmoji(alpha2[lifterData[l].team])}<br>${lifterData[l].name}`;
  } else 
    d.children[0].innerHTML=lifterData[l].name;
  if (n) {d.children[1].innerHTML=lifterData[l][compStatus.nextLiftIs]+"kg"} else 
    {d.children[1].innerHTML=lifterData[l][compStatus.activeLift]+"kg";}
  switch (compStatus.activeLift[0]) {
    case "s":
    case "b":
      str="Rack: "+lifterData[l][compStatus.activeLift[0]+"r"];
      break
    case "d":
    default:
      str="";
      break
  }
    d.children[2].innerHTML=str;
    let e=str.charAt(str.length-1);
    nstr=str.slice(0,-1);

  switch (e) {
    case "i":d.children[2].innerHTML= nstr+" In";
    break
    case "l":d.children[2].innerHTML= nstr+" Left In";
    break
    case "r":d.children[2].innerHTML= nstr+" Right In";
    break
    case "b":d.children[2].innerHTML= nstr+" Blocks";
    break
  }
}

function checkAllOff() { //function to check if all the timers are off
  if (Object.keys(timers).length ===0) lightsTimers.classList.remove("visible");
}

</script>
</html>
