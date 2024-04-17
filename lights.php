<?php include_once "./includes/config.inc";
if (isset($_GET["c"])) {$c=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING); }else {
  if (!empty($_SESSION["compLetters"])) {$c=$_SESSION["compLetters"]; } else {
    die(0);
  }
}

?>
<html>
<head>
<link rel="stylesheet" href="./resources/plateLights.css">
<title>Lights
</title>
<meta name="robots" content="noindex">
</head>
<body>
<div id="lightsTimers" style="opacity:1">
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

<script src="./simpleLifter2/lightsTimers.js"></script>
<script src="./simpleLifter2/2save-load.js"></script>
<script>
let interval=setInterval(checkUpdate,1000);
let oldStatus="";
let compStatus=[];
let clear;

function checkUpdate() {
  fetch("./simpleLifter2/loadsetup.php?c=<?php echo $c; if (!empty($f)) {echo "&f=".$f;}; ?>").then(response=>response.json().then(s=>{
    compStatus=s;
    if (compStatus.updated!=oldStatus.updated) {
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
      if (compStatus.timeTwo!=oldStatus.timeTwo) {
        let t2=new Date(convertTime(compStatus.timeTwo)).valueOf() ||0;
        a+=setTimer(timer2Box,t2);
      }
      if (compStatus.timeThree!=oldStatus.timeThree) {
        let t3=new Date(convertTime(compStatus.timeThree)).valueOf() ||0;
        a+=setTimer(timer3Box,t3);
      }
      //make sure we only update when the compstatus has changed by comparing timestamp. updates the cached timestamp
      oldStatus=compStatus;
    } //end if statuses are different
  })) //end nested fetch
} //end function check update

</script>

</html>
