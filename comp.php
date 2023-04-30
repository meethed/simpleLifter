<?php
// yes POST no  SESSION = new session with POST data
// no  POST no  SESSION = redirect to index.php
// no  POST yes SESSION = use session data
// yes POST yes SESSION = update with POST data
// yes GET - check if OBS "auto"

if (!empty($_GET)) //if there's something in there
if ($_GET['s']="OBS") { // plus if part of the something is OBS
$_POST['compName']=$_GET['compName'];
$_POST['pos']="lights";
$_POST['pwd']=$_GET['pwd'];
} else
if (empty($_POST)) { // if there is no POST data (ie a direct URL)
	if(session_status() !== PHP_SESSION_ACTIVE) {  //if also no session
//		header('Location: index.php'); //no post no session
//		die();
	} else {
		// no post yes session
		$_POST['compName'] = $_SESSION['compName'];
		$_POST['pos'] = $_SESSION['pos'];
		$_SESSION['compName'] = $_SESSION['compName']; //hack to keep session
	}
} else {


	//if there's post data, regardless of session
	//then overwrite the session data with the new post

	//restart the session
	session_unset();
	session_destroy();
	session_start();

	$_SESSION["compName"] = $_POST['compName'];
	$_SESSION["pos"]= $_POST['pos'];
	}
?>

<!DOCTYPE html>
<HTML>
<HEAD>
<link rel="stylesheet" href="./resources/styles.css">
<TITLE>Powerlifting Competition Lights Manager</TITLE>
</HEAD>
<BODY style="overflow:hidden" id="body">


<div id="heading"><h1>
	<?php
// Create connection
$conn = new mysqli('localhost', 'lightsuser','lights','lightsdb');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT compLetters, compID, compName, hish FROM comps WHERE compLetters=\"" . $_POST["compName"]. "\"";
$result = $conn->query($sql);


if ($result->num_rows > 0) { //if there's a competition that matches the compLetters - now there's only 1 unique 
  // competition so if the database is corrupted this will be a bit messy but that can't happen as that isn't
  // how the db is set up
  // output data of each row
  while($row = $result->fetch_assoc()) {

    if ($row["hish"]==crypt($_POST["pwd"], substr($row["compLetters"],1,2))) {

    $pos = "";
    if ($_POST["pos"] == "lights") {$pos = "";}
    if ($_POST["pos"] == "leftLight") {$pos = " - Left Referee";}
    if ($_POST["pos"] == "centreLight") {$pos = " - Centre Referee";}
    if ($_POST["pos"] == "rightLight") {$pos = " - Right Referee";}
    if ($_POST["pos"] == "timer") {$pos = " - Timer";}
    echo $row["compName"]. $pos ."<br>";
  
}
 else {
	if ($pos!="") {
  echo 'Error accessing database. Possibly a password error<br><a href="index.php">Redirect to home</a></body></html>';
  die();}
}}}
$conn->close();
?>

</h1>
		<div id="menuDrop" class="dropDown">
			<div class="item" id="mReasons">Referee Help</div>
			<div class="item" id="mLeft">Change to Left</div>
			<div class="item" id="mCentre">Change to Centre</div>
			<div class="item" id="mRight">Change to Right</div>
			<div class="item" id="mBreak">10 min break</div>
			<div class="item" id="mBreak20">20 min break</div>
			<div class="item" id="mScramble">Clear screen</div>
			<div class="item" id="mTap">Switch to Tap Mode</div>
			<div class="item" id="mSpeech">xP Features Off</div>
		</div>
	</div>
   <div id="centreRef">
	<div class="rBut" id="Loaded">Bar Loaded</div>
<?php if ($_POST["pos"] == "timer") {
echo '<div class="rBut clear" id="cLoaded">X</div><div class="rBut" id="Attempt">Attempt Timer</div><div class="rBut clear" id="cAttempt">X</div><br><input class="rBut" id="timer2" type="number" value="10" min="1" max="59"><p>(Minutes)</p>';}; ?>

  </div>
   <div id="sideRef">
	<div class="rBut" id="White">Good Lift - White</div>
	<div class="rBut" id="Red">No Lift - Red</div>
	<div class="rBut" id="Blue">No Lift - Blue</div>
	<div class="rBut" id="Yellow">No Lift - Yellow</div>
   </div>

    
    <div id="Lights" class="<?php echo $_POST['pos']; ?>">
        <div id="bigLights">
            <div id="bigL" class="lightContainer">
                <div class="round" id="choiceL"></div>
            </div>
            <div id="bigC" class="lightContainer">
                <div class="round" id="choiceC"></div>
            </div>
            <div id="bigR" class="lightContainer">
                <div class="round" id="choiceR"></div>
            </div>
        </div>
            <div id="smolLights">
                <div class="sLight">
                  <div class="round" id="failL"></div></div>
		<div class="sLight">
                  <div class="round" id="failC"></div></div>
		<div class="sLight">
                  <div class="round" id="failR"></div></div>
            </div>
        <div id="secondTimerDiv">1:00</div>
        <div id="timer">1:00</div>
	</div>
<div class="warning" id="warncont"><div id="connection">Connected Successfully</div><span id="hb" class="heartbeat">&#128994</span></div>
 <?php   if ($_POST["pos"] == "lights") {echo '

<div class="tickerwrap" id="tickerw"><div class="ticker" id="ticker"><div class="tickercontent" id="tickerc">Woohoo</div></div></div>

';}; ?>
</body>

<script>
var timerto,
 timertwo,
 barLoaded,
 ll,
 cl,
 rl,
 frontandcentre,
 timer,
 delaytimer,
 cleartimer,
 secondTimer,
 oldl,
 oldc,
 oldr,
 ls,
 cs,
 rs,
 dbcache,
 oldt1,
 oldt2,
 lightvars,
 resetinterval,
 secondinterval,
 isTiming,
 isAttempting,
 isCentre=0,
 compName="<?php echo $_POST["compName"]; ?>",
 pos="<?php echo $_POST["pos"]; ?>", 
 loadedTimeout=0,
 whiteTimeout=0,
 redTimeout=0,
 blueTimeout=0,
 yellowTimeout=0,
 holdTime= 500,
 currenttimer,
 clockOffset=0,
 twoOffset,
 att,
 oldplt,
 synth = window.speechSynthesis,
 talkOn=0,
 ticking=0;
;
//init
doHB();
var sheet = document.createElement('style'); //style sets a css transition variable
if (window.location !== window.parent.location) document.getElementById("body").style.background="rgba(0,0,0,0)"; //if it's in an iframe, default to making the background transparent
//if (pos=="lights") var plateWindow = window.open("./plates.php?compName="+compName, "_blank", "toolbar=no,scrollbars=no,resizable=yes,top=0,left=0,width=960,height=540");

if (pos=="timer") setTimer(); //timer adjustments 
initPos(); //position adjustments



//end main init
// this will fix things becaus of fucking ipahonnneeessssss

if (typeof window.screen.orientation !== "undefined") {
// make fullscreen
screen.orientation.addEventListener('change', tryFullscreen);

/* Get the documentElement (<html>) to display the page in fullscreen */
var elem = document.documentElement;

/* View in fullscreen */
function tryFullscreen() {
 window.scrollTo(0,1);

//if it's a lights view, only make it fullscreen when it's in landscape
if (pos=="lights") { 


 if (screen.orientation["type"][0]=="l") { 
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) { /* Safari */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE11 */
    elem.msRequestFullscreen();
  }
} else {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) { /* Safari */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE11 */
    document.msExitFullscreen();
  }
}
}}

}




function setTimer() {
isCentre=1; //to make sure it responds


var loadedId=document.getElementById('Loaded');
var cLoadedId=document.getElementById('cLoaded');
var attemptId=document.getElementById('Attempt');
var cAttemptId=document.getElementById('cAttempt');
// normal click handler for the two timers when in 'timer' mode

//bar loaded timer
loadedId.addEventListener('click', function() { //start bar loaded timer 
	setBarLoaded(1); //if timer is off, turn it on
} );


//second timer
attemptId.addEventListener('click', function() { //start the extra timer
	updateTimer2(document.getElementById('timer2').value); //separate function to run the extra timer
});

//clear bar loaded timer
cLoadedId.addEventListener('click', function() {
	setBarLoaded(-1);
});

cAttemptId.addEventListener('click', function() {
	updateTimer2(-1);
});


//input box 
document.getElementById('timer2').addEventListener('change', function() {
	attemptId.innerHTML=document.getElementById('timer2').value + ' minutes';
});

//formatting
loadedId.style.width='70%';
loadedId.style.fontSize='10vh';
cLoadedId.style.width='4%';
attemptId.style.width='70%';
attemptId.style.fontSize='10vh';
cAttemptId.style.width='4%';



};// end timer specifics

// this bit is all about mouse clicks / custom event handlers for the custom hold time
function initPos() {
if (pos!="lights") document.querySelector("h1").innerHTML = document.querySelector("h1").innerHTML.split("-")[0]+ " - " + pos.slice(0,-5).charAt(0).toUpperCase() + pos.slice(0,-5).slice(1)+ " Referee";
sheet.innerHTML = ".dn {transition: "+holdTime+"ms";
	document.body.appendChild(sheet);

if (pos=="centreLight") { //only do this bit if it's a centre referee
	isCentre=1;
	item=document.getElementById('Loaded');
	item.addEventListener('mousedown', function() {
		document.getElementById('Loaded').classList.add('dn');
		loadedTimeout = setTimeout(function() {setBarLoaded(1);}, holdTime)});
    	item.addEventListener('mouseup', function() {
		document.getElementById('Loaded').classList.remove('dn');
		clearTimeout(loadedTimeout)});
	item.addEventListener('mouseleave', function() {
		document.getElementById('Loaded').classList.remove('dn');
		clearTimeout(loadedTimeout)});
};

	item=document.getElementById("White");
	item.addEventListener('mousedown', function() {
		document.getElementById("White").classList.add("dn");
		whiteTimeout = setTimeout(function() {updateLights(1);}, holdTime)});
    	item.addEventListener('mouseup', function() {
		document.getElementById("White").classList.remove("dn");
		clearTimeout(whiteTimeout)});
	item.addEventListener('mouseleave', function() {
		document.getElementById("White").classList.remove("dn");
		clearTimeout(whiteTimeout)});

	item=document.getElementById("Red");
	item.addEventListener('mousedown', function() {
		document.getElementById("Red").classList.add("dn");
		redTimeout = setTimeout(function() {updateLights(2);}, holdTime)});
    	item.addEventListener('mouseup', function() {
		document.getElementById("Red").classList.remove("dn");
		clearTimeout(redTimeout)});
	item.addEventListener('mouseleave', function() {
		document.getElementById("Red").classList.remove("dn");
		clearTimeout(redTimeout)});


	item=document.getElementById("Yellow");
	item.addEventListener('mousedown', function() {
		document.getElementById("Yellow").classList.add("dn");
		yellowTimeout = setTimeout(function() {updateLights(3);}, holdTime)});
    	item.addEventListener('mouseup', function() {
		document.getElementById("Yellow").classList.remove("dn");
		clearTimeout(yellowTimeout)});
	item.addEventListener('mouseleave', function() {
		document.getElementById("Yellow").classList.remove("dn");
		clearTimeout(yellowTimeout)});

	item=document.getElementById("Blue");
	item.addEventListener('mousedown', function() {
		document.getElementById("Blue").classList.add("dn");
		blueTimeout = setTimeout(function() {updateLights(4)}, holdTime)});
    	item.addEventListener('mouseup', function() {
		document.getElementById("Blue").classList.remove("dn");
		clearTimeout(blueTimeout)});
	item.addEventListener('mouseleave', function() {
		document.getElementById("Blue").classList.remove("dn");
		clearTimeout(blueTimeout)});

// these are for mobile devices which don't use mouseup mousedown functions

if (pos=="centreLight"){
	isCentre=1;
	item=document.getElementById('Loaded');
	item.addEventListener('pointerdown', function() {
		document.getElementById('Loaded').classList.add('dn');
		loadedTimeout = setTimeout(function() {setBarLoaded(1);}, holdTime)});
    	item.addEventListener('pointerup', function() {
		document.getElementById('Loaded').classList.remove('dn');
		clearTimeout(loadedTimeout)});
	item.addEventListener('pointerleave', function() {
		document.getElementById('Loaded').classList.remove('dn');
		clearTimeout(loadedTimeout)});
}

	item=document.getElementById("White");
	item.addEventListener('pointerdown', function() {
		document.getElementById("White").classList.add("dn");
		whiteTimeout = setTimeout(function() {updateLights(1);}, holdTime)});
    	item.addEventListener('pointerup', function() {
		document.getElementById("White").classList.remove("dn");
		clearTimeout(whiteTimeout)});
	item.addEventListener('pointerleave', function() {
		document.getElementById("White").classList.remove("dn");
		clearTimeout(whiteTimeout)});

	item=document.getElementById("Red");
	item.addEventListener('pointerdown', function() {
		document.getElementById("Red").classList.add("dn");
		redTimeout = setTimeout(function() {updateLights(2);}, holdTime)});
    	item.addEventListener('pointerup', function() {
		document.getElementById("Red").classList.remove("dn");
		clearTimeout(redTimeout)});
	item.addEventListener('pointerleave', function() {
		document.getElementById("Red").classList.remove("dn");
		clearTimeout(redTimeout)});


	item=document.getElementById("Yellow");
	item.addEventListener('pointerdown', function() {
		document.getElementById("Yellow").classList.add("dn");
		yellowTimeout = setTimeout(function() {updateLights(3);}, holdTime)});
    	item.addEventListener('pointerup', function() {
		document.getElementById("Yellow").classList.remove("dn");
		clearTimeout(yellowTimeout)});
	item.addEventListener('pointerleave', function() {
		document.getElementById("Yellow").classList.remove("dn");
		clearTimeout(yellowTimeout)});

	item=document.getElementById("Blue");
	item.addEventListener('pointerdown', function() {
		document.getElementById("Blue").classList.add("dn");
		blueTimeout = setTimeout(function() {updateLights(4)}, holdTime)});
    	item.addEventListener('pointerup', function() {
		document.getElementById("Blue").classList.remove("dn");
		clearTimeout(blueTimeout)});
	item.addEventListener('pointerleave', function() {
		document.getElementById("Blue").classList.remove("dn");
		clearTimeout(blueTimeout)});


// the lights will do a poll or update and were doing some DB access. but there might be a situation where the lights 'user' disconnects.
// in this case, we want the centre referee user (as the 'controller') to still do some of the DB updating, but the lights to do the polling
// this is ONLY to clear the bar loaded or when all three lights have been set 

// this section controls who sees what (HTML wise)

if (pos=="lights") { //only run this bit if we're logged on as a display 
	//open a new window for plates

	document.getElementById("sideRef").style.display="none";
	document.getElementById("centreRef").style.display="none";
	updateChecker = setInterval(checkUpdate, 1000); //this is the updating that the lights needs to do - ONLY VISUALS GET UPDATED!
} else {
	document.getElementById("sideRef").classList.add("visible");
	document.getElementById("centreRef").classList.remove("visible");	
 }

 if (pos=="centreLight"){  //this is for the centre referee only - it's been phpd so no weird JS confusion on other positions
	document.getElementById('centreRef').classList.add('visible');
	updateChecker = setInterval(checkUpdate, 1000); //this is the updating that the lights need to do (but on the centre referee spot)
};

if (pos=="timer") {
	document.getElementById('centreRef').classList.add('visible');
	document.getElementById('sideRef').classList.remove('visible');
	document.getElementById('sideRef').style.display='none';
	document.getElementById('timer').style.position='relative';
	document.getElementById('timer').style.transform='none';
	document.getElementById('timer').style.left='0px';
	document.getElementById('timer').style.width='100%';
	document.getElementById('timer').style.top='0';
	updateChecker = setInterval(checkUpdate, 1000); //this is the updating that the lights need to do (but on the centre referee spot)
}
}
function checkUpdate() {
	var xhttp;
	var newdbcache;
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
		newdbcache=this.responseText; // the function will return CSV data for JS to process

		if (newdbcache!=dbcache) { //if something has changed
					   //note we don't know what has changed yet

			//update the dbcache
			lightvars = JSON.parse(newdbcache);
			//lightvars = newdbcache.split(",");
			timerto   = lightvars.timeTo;
			timertwo  = lightvars.timeTwo;
			ll 	  = lightvars.leftLight;
			cl	  = lightvars.centreLight;
			rl	  = lightvars.rightLight;
			att   = lightvars.compStatus;
			plt   = lightvars.currentAttempt; //note this is updated whenever we change the attempt 
var d= Math.floor(Date.now()/1000); //d is now in seconds

			// if the update is to the lights, then it overrules the timers
			if ((oldl!=ll) || (oldr!=rl) || (oldc!=cl)) {
				setLights (ll, cl, rl); //call a separate function
				oldl=ll;
				oldc=cl;
				oldr=rl;
			};

	//		if (plt != oldplt)  //if there's been a change
	//			if (!barLoaded)
	//				if (plt) {
	//					oldplt=plt;
	//					plateWindow.drawPlates(oldplt);
	//					plateWindow.updateNext(parseFloat(lightvars.nextLoad));
	//				}
			
			if (timerto != oldt1) //if there's a new timer1 (this should always be 60 seconds)
			{
				ticking=0; //get rid of the ticker
				t1 = timerto + clockOffset;
				if (t1-d<=0) { clearLoaded();} //if it's behind, then stop
				if (t1-d>0) {startBarTimer(t1);} //if barloaded timer in front of now
				oldt1=timerto;
			}

			if (timertwo != oldt2)  //if there's a new secondary timer
			{
				ticking=0; //get rid of the ticker
				t2 =timertwo + clockOffset;
				if (t2-d<=0) { clearSecond();} //if it's behind then stop
				oldt2=timertwo;
				if (t2-d>0) {startSecondTimer(t2)} //if the other timer is loaded
			}

			dbcache=newdbcache;

		} //end if the cached data has changed
		} //end if connected

	}; //end check function
	xhttp.open("GET", "lights.php?compLetters=" + compName) //this is what happens every second - the stuff previous waits until it's ready (complete)
	xhttp.send();
}


function clearTimers() { //clear both timers
clearLoaded();
clearSecond();
}

function clearLoaded() { //clear the barloaded timer
barLoaded=0;
clearInterval(currenttimer);
document.getElementById("timer").classList.remove("visible");
}

function clearSecond() { //clear second timer
secondTimer=0;
clearInterval(secondinterval);
document.getElementById("secondTimerDiv").classList.remove("visible");
}

function startSecondTimer(t) //start second timer (the attempt timer usually)
{
	clearInterval(secondinterval); //clear this interval (the 2 second interval)
	var pad ="00";
	//wait a second
	document.getElementById("secondTimerDiv").innerHTML = "1:00";
	document.getElementById("secondTimerDiv").classList.add("visible"); //show the timer
	document.getElementById("secondTimerDiv").style.color="#fff";

	//timer time
	secondTimer=1;
	secondinterval = setInterval(function() {

	var now = Math.floor(Date.now()/1000); // now is in unix time

	var distance = (t - now)*1000; // how many seconds difference

	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	var seconds =  (pad+seconds).slice(-pad.length); //formatting of the seconds

  //this is at the start of the timer
	if (!synth.pending && talkOn && minutes>2 && seconds==0) saySomething(minutes + "minutes remaining"); //say something

	document.getElementById("secondTimerDiv").innerHTML = minutes +":" + seconds;
  if (pos=="lights") 	document.getElementById("tickerc").innerHTML = minutes-3 + ":"+ seconds+ " to change opening attempts";

	if (minutes ==0) {
	if (seconds ==30) { //30s remaining goes orange
	document.getElementById("secondTimerDiv").style.color="#970";
	if (!synth.pending && talkOn) saySomething("30 seconds remaining");
	}
	if (seconds ==9) { //move to single figures goes red
	document.getElementById("secondTimerDiv").style.color="#f00";
	}
	if (seconds == 0) {
	clearSecond();
	}
}
	//move to the middle if it's the only one running and it's a >2 minute break
	if ((barLoaded==0) & (minutes>1) & !(frontandcentre)) {
	document.getElementById("secondTimerDiv").classList.add("central");
	frontandcentre=1;
	}

  //show the 'attempt change' warning
  if ((minutes>=3) & (minutes<8) & !(ticking) & (pos=="lights")) {
		document.getElementById("tickerw").classList.add("ticking");
		ticking=1;
	}
	if ((minutes<3) & (ticking) & (pos=="lights")) {
		document.getElementById("tickerw").classList.remove("ticking");
		ticking=0;
	}
	if ((minutes <0) || (seconds <0)) {clearSecond()}; //random clear if it goes negative for some reason


}, 250);


}

function startBarTimer(t) { //if it's a barloaded call
	if (talkOn) saySomething("Bar is Loaded!");
	if (pos=="lights") document.getElementById("tickerw").classList.remove("ticking");
		ticking=0;
	var pad ="00";

	clearTimeout(resetinterval); //reset the 10 second timer just in case
	setLights (0,0,0); //clear the lights

	document.getElementById("timer").innerHTML = "0:59";
	document.getElementById("timer").classList.add("visible"); //show the timer
	document.getElementById("bigLights").classList.remove("visible");
	document.getElementById("smolLights").classList.remove("visible");
	document.getElementById("timer").style.color="#fff";

	//timer time
	clearInterval(currenttimer);
	barLoaded=1;
	document.getElementById("secondTimerDiv").classList.remove("central");
	frontandcentre=0;
 
	currenttimer = setInterval(function() {

	var now = Math.floor(Date.now()/1000); //now is in unix time

	var distance = (t - now)*1000; //number of seconds difference

	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

	var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	var seconds =  (pad+seconds).slice(-pad.length);



	document.getElementById("timer").innerHTML = minutes +":" + seconds;
        if (minutes ==0) {
	if (seconds ==30) { //30s remaining goes orange
	document.getElementById("timer").style.color="#970";
  if (talkOn) saySomething("30 seconds");
	}
	if (seconds ==9) { //move to single figures goes red
	document.getElementById("timer").style.color="#f00";
  if (talkOn) saySomething("10 seconds");
	}
	if (seconds == 0) {clearLoaded()}
	}
	if ((minutes <0) || (seconds <0)) {clearLoaded()}; //random clear if it goes negative for some reason

}, 250);

}

function setLights(ll, cl, rl) {



var spots =[ll,cl,rl];

// 0 = off
// 1 = white
// 2 = red
// 3 = yellow
// 4 = blue
// 5>= red no smol light


if ((ll==0)  || (cl==0) || (rl==0)) { //if at least one of them is zero
	var bigL = document.getElementById("bigLights").children; //setup the arrays
	var smolL = document.getElementById("smolLights").children;
	var i;
	for (i=0; i < bigL.length; i++) {
		smolL[i].children[0].className="round";
		if (spots[i]!=0){
			bigL[i].children[0].classList.add("pending");
			bigL[i].children[0].classList.add("visible");
		}
		else {
			bigL[i].children[0].className="round";
		}
	} //make the non-zero ones green
}
if ((ll==0) && (cl==0) && (rl==0)) { //if they're all zero

	document.getElementById("bigLights").classList.remove("visible");
	document.getElementById("smolLights").classList.remove("visible");
	return;
	}
 else { //else if they're NOT all zero
	if (talkOn) sayDecision(ll,cl,rl);

	updateLights(10);
	clearTimeout(resetinterval); //reset the 10 second timer just in case
	document.getElementById("timer").classList.remove("visible");
	document.getElementById("bigLights").classList.add("visible");
//	updateLights(10);
	clearLoaded();
//	clearSecond();
}


if ((ll!=0) && (cl!=0) & (rl!=0)) { //if they are all non-zero THEN DISPLAY THE LIGHTS!!
	updateLights(10); //clear th etimer
	if (att!=3 && att!=6 && att!=9) updateTimer2(1); //if the referees have set all the lights, set the next attempt timer to 1 minute;
	clearTimeout(resetinterval); //reset the 10 second timer just in case
	resetinterval = setTimeout(function() {clearlights();},5000);  //this is the timer for clearing the lights. it is currently 5 seconds. 10 was too long


document.getElementById("smolLights").classList.add("visible");

	var bigL = document.getElementById("bigLights").children; //big lights
	var smolL = document.getElementById("smolLights").children; //failure reason lights
	var i;
	for (i=0; i < bigL.length; i++) {
		smolL[i].children[0].classList.remove("visible");
		bigL[i].children[0].classList.remove("pending", "white", "red", "yellow", "blue"); //nuke wrong colours
		smolL[i].children[0].classList.remove("pending", "white", "red", "yellow", "blue"); //nuke wrong colours on smol 
		switch (spots[i]) { // set the class (colour) depending on the database value
			case "1":
			case 1:
				bigL[i].children[0].classList.add("white"); //if white then white
			break;
			case "2":
			case 2:
				bigL[i].children[0].classList.add("red"); //if red then red, failure red
				smolL[i].children[0].classList.add("red");
				smolL[i].children[0].classList.add("visible");
			break;
			case "3":
			case 3:
				bigL[i].children[0].classList.add("red"); //if yellow then red, failure yellow
				smolL[i].children[0].classList.add("yellow");
				smolL[i].children[0].classList.add("visible");

			break;
			case "4":
			case 4:
				bigL[i].children[0].classList.add("red"); //if blue then red, failure blue
				smolL[i].children[0].classList.add("blue");
				smolL[i].children[0].classList.add("visible");

			break;
			case "5":
			case "6":
				bigL[i].children[0].classList.add("red"); // if it's 5 or 6 then just show the big red one
			break;
			default:
				bigL[i].children[0].classList.add("red");
		}
		bigL[i].children[0].classList.add("visible"); //make the thing visible
	}
}
}

function clearlights() { //this function clears the lights off the screen (after 10 seconds or so)
	updateLights(99);
	setLights (0,0,0); //clear the lights

}

// this function to write the updates to the database
// yes you need the centre referee running to update the database. client side scripting reaching
// back into the db via php isn't that bad
function updateLights(col) {
// 9 bar loaded
// 10 not bar loaded
// 99 clear all
// else write to the DB

if (compName=="") {
 alert("Error. Disconnected from server. Redirecting...");
 window.location.href="index.php";}

switch (col) {

	// clear the bar loaded

	case 10:
	case "10":
	if (isCentre) {
	var str = "&pos=timeTo" + "&compName="+compName+"&col=0";

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
	}
	}
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);;
	xhttp.send();
	}
break;

	//clear all lights

case "99":
case 99:
	if (isCentre) {
	//clear left
	var str = "&pos=leftLight" + "&compName="+compName+"&col=0";


	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
	}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);;
	xhttp.send();


	//clear centre
	var str = "&pos=centreLight" + "&compName="+compName+"&col=0";

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
	}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);;
	xhttp.send();


	//clear right
	var str = "&pos=rightLight" + "&compName="+compName+"&col=0";

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
	}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);
	xhttp.send();
	}
break;

default:
	var str = "&pos=" + pos + "&compName="+compName+"&col=" + col;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
	if ("vibrate" in navigator) { //fucking iOS / safari
	var successBool = navigator.vibrate([holdTime/2]);
	}
	}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);
	xhttp.send();
break;

}
}

function setBarLoaded(t) {

	if  (t===null) {t=1};

	//set the bar loaded

	var str = "&pos=timeTo" + "&compName="+compName+"&col="+t;

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
			if ("vibrate" in navigator) { //fucking iOS / safari
			var successBool = navigator.vibrate([holdTime/2]);
			}
		}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);;
	xhttp.send();


}




function updateTimer2(mins) {

	if  (mins===null) {mins=0};

	//set the second timer. Note that this is the click handler, this is the real deal.

	var str = "&pos=timeTwo" + "&compName="+compName+"&col="+mins;

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
			if ("vibrate" in navigator) { //fucking iOS / safari
			var successBool = navigator.vibrate([holdTime/2]);
			}
		}
	};
	xhttp.open("GET", "updatelights.php?t="+Math.random()+str, true);;
	xhttp.send();

}

offsetTimer = setInterval(doHB, 30000);

function doHB() { //offset update & heartbeat
	var d= Date.now()/1000; //get the current unix date
	var xhttp = new XMLHttpRequest(); //set up an ajax request

	xhttp.onreadystatechange = function() {
		if (this.readyState ==4 && this.status ==200) {
			//do things when the get has been successful
			var nd =  parseInt(this.responseText);
			 clockOffset=(d-nd); //set the clock offset to the difference in times to account for timezones, badly synced clocks, etc.
		    	document.getElementById("connection").textContent="Connected Successfully. Sync: " + clockOffset ;
			document.getElementById("hb").classList.remove("hb");
			setTimeout(function() {document.getElementById("hb").classList.add("hb");},15000);
		}
	};
	xhttp.open("GET", "heartbeat.php?t="+Math.random(), true);
	xhttp.send();

} //end function doHB

function saySomething(talk) {
if (pos=="lights") {
//var voices = synth.getVoices();

var msg = new SpeechSynthesisUtterance();
msg.text = talk;
synth.speak(msg);
}
}

function sayDecision(l,c,r) {
const a=[l,c,r];
if (a.filter(x => x==0).length ==2) {
saySomething("and the referees say");
}
if (a.filter(x => x==0).length ==0) {
if (a.every(x => x==1)) { //if all white
saySomething("Three white lights!");
}
if (a.filter(x => x==1).length ==2) { //if 2 white
saySomething("Two white lights! Still a good lift");
}
if (a.filter(x => x==1).length ==1) { //if 1 white
saySomething("Two reds. No lift!");
}
}
} //end function sayDecision

// new help menu section
document.getElementById("heading").addEventListener("click", menuOn, false);
//document.getElementById("centreRef").addEventListener("click", menuOff);
//document.getElementById("sideRef").addEventListener("click", menuOff);
//document.getElementById("body").addEventListener("click", menuOff);

//better menu click swap handler
document.getElementById("body").addEventListener("click", function(e) {
if (document.getElementById("menuDrop").classList.contains("showMenu")) {
menuOff();
}
},true);

function menuOn(e) {
	document.getElementById("menuDrop").classList.add("showMenu");
	document.getElementById("centreRef").classList.add("showMenu");
	document.getElementById("sideRef").classList.add("showMenu");
	document.getElementById("Lights").classList.add("showMenu");
}
function menuOff(e) {
	document.getElementById("menuDrop").classList.remove("showMenu");
	document.getElementById("centreRef").classList.remove("showMenu");
	document.getElementById("sideRef").classList.remove("showMenu");
	document.getElementById("Lights").classList.remove("showMenu");
}

//referee help (reasons)
document.getElementById("mReasons").addEventListener("click", function() {
	menuOff();
	window.location="reasons.php";
	event.stopPropagation();
});

//change to left referee
document.getElementById("mLeft").addEventListener("click", function() {
	pos="leftLight";
	menuOff();
	initPos();
	event.stopPropagation();
	isCentre=0;
});
//change to centre referee
document.getElementById("mCentre").addEventListener("click", function() {
	pos="centreLight";
	menuOff();
	initPos();
	event.stopPropagation();
	isCentre=1;
});
//change to right referee
document.getElementById("mRight").addEventListener("click", function() {
	pos="rightLight";
	menuOff();
	initPos();
	event.stopPropagation();
	isCentre=0;
});
//10 min break
document.getElementById("mBreak").addEventListener("click", function() {
	menuOff();
	clearlights();
	setBarLoaded(-1);
	updateTimer2(10);
	event.stopPropagation();
});
//20 min break
document.getElementById("mBreak20").addEventListener("click", function() {
	menuOff();
	clearlights();
	setBarLoaded(-1);
	updateTimer2(20);
	event.stopPropagation();
});
//whoopsiedoopsie
document.getElementById("mScramble").addEventListener("click", function() {
	menuOff();
	isCentre=1;
	clearlights();
	setBarLoaded(-1);
	updateTimer2(-1);
	isCentre=0;
	initPos();
	event.stopPropagation();
});
//tap or hold
document.getElementById("mTap").addEventListener("click", function() {
	if (holdTime==500) {
		document.getElementById("mTap").innerHTML="Switch to Hold Mode";
		holdTime=0;
		sheet.innerHTML = ".dn {transition: "+holdTime+"ms";
	} else {
		document.getElementById("mTap").innerHTML="Switch to Tap Mode";
		holdTime=500;
		sheet.innerHTML = ".dn {transition: "+holdTime+"ms";
	}
	menuOff();
	event.stopPropagation();
});

//experimental
document.getElementById("mSpeech").addEventListener("click", function() {
	menuOff();
	if (talkOn) {
		document.getElementById("mSpeech").innerHTML="xP Features Off";
		talkOn=0;
	} else {
		document.getElementById("mSpeech").innerHTML="xP Features On";
		talkOn=1;
		saySomething("Talk Mode On!");
	}
	event.stopPropagation();
});

function getPlates(w) { //this one just tells you how many you will have
w=w-25; //take away 25kg for the bar and collars

var plateCount=[];

globalplates.forEach((x) => {
	var plate=Math.floor(w/(x*2));
	w=w-x*plate*2;
	plateCount.push(plate);
	});

return plateCount
} //end getPlates function

function drawPlates(w) { //this one will draw the plates in the 'plate' parent div
	plateDiv=document.getElementById("plates");
	plateDiv.style.display="inline-flex";
	var c=plateDiv.lastElementChild;
	while (c) {
		plateDiv.removeChild(c);
		c=plateDiv.lastElementChild;
	}
	if (!w) return false;
	var plates=getPlates(w);
	
	for (i=0;i<plates.length;i++){
		var weight=globalplates[i];
		if (plates[i]>0){
			var plateHolder=document.createElement("div");
			plateHolder.id="plateHolder"+weight;
			plateHolder.classList.add("plateHolder");
			plateDiv.appendChild(plateHolder);
			
			for (c=0;c<plates[i];c++){
				var plate=document.createElement("div");
				plate.id=i+"plate"+c;
				plate.classList.add("plate");
				plate.classList.add("p"+weight*100);
				//plate.innerHTML=weight=" - " + c;
				plateHolder.appendChild(plate);
			
			} //end add multiple plates loop
			
			if (i==0) {var text=document.createElement("div"); //if there's multiple 25s
			text.innerHTML=plates[i]+"x";
			text.classList.add("plateText");
			plateHolder.prepend(text); }
		} 
	}
} //end drawplates fuinction
	



</script>



</HTML>

