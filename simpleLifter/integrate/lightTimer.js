var clockOffset=0,
 oldJSON,
 lights=[],
 timeoutClearScreen,
 timeoutLockout,
 tik,
 oldt1=0,
 oldt2=0,
 ticker,
 secondInterval,
 updatet1,
 updatet2,
 dGB,
 waiting=0;
tick();
//module for server interaction

function loadServer(){
var l1=0,l2=0;
fetch("saveload.php?q=loadlifter&comp="+compName,{method:"POST"})
  .then((response) => {
    return response.json();
  })
  .then((myJson) => {
	var a=document.querySelectorAll(".tr");
	var i; const l=a.length;
	for (i=1;i<l;i++) {a[i].remove();}
	lifters = new Lifters(0);
	loading=myJson;
	var i=0;
	while (loading.liftList[i]) {
		lifters.liftList[i] = new Lifter("","","","","","","","","",loading.liftList[i]);
		i++;
	};
  });

//load setup
fetch("saveload.php?q=loadsetup&comp="+compName,{method:"POST"})
  .then((response) => {
    return response.json();
  })
  .then((myJson) => {
	setup=myJson;
	setupCx();
  });
} //end function loadServer

function saveServer(){
	var fd= new FormData();
	fd.append("json", JSON.stringify(lifters));
	fetch("saveload.php?q=lifters&comp="+compName,{method:"POST",body:fd});

	var fd2= new FormData();
	fd2.append("json", JSON.stringify(setup));
	fetch("saveload.php?q=setup&comp="+compName,{method:"POST",body:fd2});


} //end function saveServer

//////////////////////////////////////////////////////////////////////


function setBarLoaded(t){
	if( document.getElementById("btnBar").innerHTML=="Reset timer" || t==-1) {
		clearInterval(ticker);
		t=-1;
		document.getElementById("btnBar").innerHTML="Bar Loaded";
	}	else {
		t=1;
	};
//try {lightWindow.setBarLoaded();} catch {};
//try {plateWindow.setBarLoaded()} catch {};
var str="&pos=timeTo" + "&compName="+compName+"&col="+t;
//first up we write to the server
fetch("../../updatelights.php?t="+Math.random()+str,{method:"GET"})
  .then((response) => {
    return response;
  });
} //end function setBarLoaded

function setTimer2(t){
var str="&pos=timeTwo" + "&compName="+compName+"&col="+t;
fetch("../../updatelights.php?t="+Math.random()+str,{method:"GET"})
  .then((response) => {
    return response;
  });
} //end function setBarLoaded


function tick(){
// polling
fetch("../../lights.php?compLetters="+compName,{method:"GET"})
	.then((response) => {
		return response.json();
	})
	.then((myJSON) => {
		if (JSON.stringify(myJSON)!=JSON.stringify(oldJSON)){
			interpret(myJSON);
			oldJSON=myJSON;
			tik=setTimeout(tick,3000);
		} else {tik=setTimeout(tick,2000)};
		

	});

};

function interpret(json) {
lights[0] = json.leftLight;
lights[1] = json.centreLight;
lights[2] = json.rightLight;

if (!lights[0] && !lights[1] && !lights[2]) { // if all the lights are off
	waiting=0;
	updateStatus();
	clearLights(); //make sure you can't see em
}
  if (evaluateTimer1(json.timeTo)) {  //if all lights off but timer 1 on BAR LOADED (also parse second timer)
	clearLights();
  	showBarLoaded(); //show the bar loaded timer
  }


if (evaluateTimer2(json.timeTwo)) { //if all lights off but timer 2 > 1 minute BREAK
  showBreakTimer(); //show the break timer
} 


if (lights.some(x => x>0)) { //if at least one light is non zero
  waiting=1;
  doLights(lights);
  clearTimer1(); //clear the timer but don't reformat - don't clear timer 2 though just in case
}

if (lights.every(x => x>0)) { //if every light is non-zero then 10 tidy up. note that displaying the lights happens above, so we don't need to display again
  waiting=1;
  clearTimeout(timeoutClearScreen); //clear these
	clearTimeout(timeoutLockout);
	if (setup.autoRefs) doGoodBad(); // increment after clearing the lights (if it's too quick it might jump twice)
	timeoutLockout = setTimeout(function() {	dGB=0;},20000); //lockout the timer from trying this again for 10 seconds
  timeoutClearScreen = setTimeout(function() {
		clearTimeout(tik);
		tik=setTimeout(tick,5000);
		clearScreen();}, 5000); //hide everything
}

} //end of interpret function

function doGoodBad(){
if (dGB==1) {return false}; // if it's just been triggered
if (lights.every(x => x==0)) {return false;} //idiot check if they're all zero then quit
dGB=1;
var countWhites=0;
lights.forEach(x => {if (x==1) countWhites+=1;});

console.log(countWhites);

if (countWhites>=2) {
	lifters.activeRw.setLift(lifters.activeLi,1);
	lifters.incrementRow;
}

if (countWhites<=1) {
	lifters.activeRw.setLift(lifters.activeLi,-1);
	lifters.incrementRow;
}

} //end of function do good / bad

function doLights(l){
	try{ lightWindow.drawLights(l);} catch{};
  try{plateWindow.drawLights(l)} catch{console.log("no Light Window")};

	var ls=""; //light string
	ldiv=document.getElementById("lights");
	if (l.every(x => x==0)) { // if they're all zero then clear them
		ldiv.style.opacity=0;
	} else { //if some are non-zero
		clearTimer1();
		for (var i=0;i<3;i++){ //loop through characters
		if (l[i]==0) ls+=String.fromCodePoint(0x26AB);
		if (l[i]>0) ls+=String.fromCodePoint(0x1F7E2);
		}
		ldiv.innerHTML=ls;
		ldiv.style.opacity=1;
	}
	if (l.every(x => x>0)) { //if they're not all zero
		ls="";
		for (var i=0;i<3;i++){ //loop through characters
		if (l[i]==1) ls+=String.fromCodePoint(0x26aa);
		if (l[i]>1) ls+=String.fromCodePoint(0x1f534);
		}
		ldiv.innerHTML=ls;
		ldiv.style.opacity=1;
	}

} //end function dolights

function clearTimer1(){
	clearInterval(ticker);
	document.getElementById("timerFrame").style.opacity=0;
	try{ lightWindow.clearTimer1();} catch {};
	try{plateWindow.clearTimer1();} catch {console.log("fail clear timer1");}
} //end function clear timer 1

function clearTimer2(){
	clearInterval(secondInterval);
	document.getElementById("timer2Frame").style.opacity=0;
	try { lightWindow.clearTimer2();} catch {};
	try {plateWindow.clearTimer2();} catch {console.log("fail clear timer 2");}

}
function clearLights(){
	try {lightWindow.drawLights([0,0,0]);} catch {};
	try{plateWindow.drawLights([0,0,0]); } catch {console.log("fail clear lights");}
	document.getElementById("lights").style.opacity=0;
} //end function clearLights

function clearScreen(){
	document.getElementById("timerFrame").style.opacity=0;
	document.getElementById("lights").style.opacity=0;
} //end function clearscreen

function showBreakTimer(){
} //end function showbreaktimer

function showBarLoaded(){
} //end function show barloaded

function evaluateTimer1(t){
	try {lightWindow.setTimer1(t); } catch {};
	try{plateWindow.setTimer1(t); plateWindow.drawLights([0,0,0]); } catch {console.log("fail set bar loaded");}

	var d=Date.now()/1000;
	diff=t-d+clockOffset;
	var updatet1=oldt1-t;

	if (diff<=0) {oldt1=t; clearTimer1(); return false;}
	if (diff>0 && updatet1) {
		oldt1=t;
		timer1=1;
	}
	var targetTime=t;
		clearInterval(ticker);
		document.getElementById("btnBar").innerHTML="Reset timer";
  document.getElementById("lights").style.opacity=0;
	ticker=setInterval(function() {
	var now = Date.now()/1000;
	var distance = (t-now+clockOffset)*1000;
	var seconds = Math.floor((distance % (1000*60)) / 1000);
	seconds = ('00'+seconds).slice(-2);
	if (seconds>0) document.getElementById("timerFrame").style.opacity=1;
	document.getElementById("timerFrame").innerHTML="0:"+seconds;
	if (distance<0) {
		clearInterval(ticker);
		document.getElementById("timerFrame").innerHTML="0:00";
		document.getElementById("timerFrame").style.opacity=0;
		document.getElementById("btnBar").innerHTML="Bar Loaded";
		try {lightWindow.clearTimer1();}catch {};
		try{plateWindow.clearTimer1();} catch {console.log("clear timer 1 fail")};
	}
	
},250)
return true;

} //end function evaluate timer1

function evaluateTimer2(t){
	try {lightWindow.setTimer2(t)} catch {};
	var d = Date.now()/1000;
	diff=t-d+clockOffset;
	var updatet2=oldt2-t;

	if (diff<=0) {clearTimer2(); return false;}
	if (diff>0 && updatet2) {
		oldt2=t;
		clearInterval(secondInterval);
	}
	var targetTime=t;
		clearInterval(secondInterval);
//		document.getElementById("btnBar").innerHTML="Reset timer";
//  document.getElementById("lights").style.opacity=0;
	try{plateWindow.setTimer2(t);} catch {console.log("timer2 error");};

	secondInterval=setInterval(function() {
	
	var now = Date.now()/1000;
	var distance = (t-now+clockOffset)*1000;

	var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var seconds = Math.floor((distance % (1000*60)) / 1000);
	seconds = ('00'+seconds).slice(-2);
	if (seconds>0) document.getElementById("timer2Frame").style.opacity=1;
	document.getElementById("timer2Frame").innerHTML=minutes+":"+seconds;
	if (distance<0) {
		clearInterval(secondInterval);
		document.getElementById("timer2Frame").innerHTML="0:00";
		document.getElementById("timer2Frame").style.opacity=0;
	}
	
},250)
return true;

} //end function evaluate timer 2

function smolClearTimer1() {
	timer1=0;
	clearInterval(ticker);
	document.getElementById("timerFrame").style.opacity=0;;
  document.getElementById("btnBar").innerHTML="Bar Loaded";
	try {lightWindow.clearTimer1()} catch {}
	try {plateWindow.clearTimer1();} catch {console.log("smol clear failed");};
} //end function smol clear timer 1



// heartbeat bit

offsetTimer = setInterval(function () { //offset update & heartbeat
	var d= Date.now()/1000; //get the current unix date
fetch("../../heartbeat.php?t="+Math.random(),{method:"GET"})
  .then((response) => {
    return response.json();
  })
  .then((myJson) => {
	//
			var nd =  parseInt(myJson);
			clockOffset=(d-nd); //set the clock offset to the difference in times to account for timezones, badly synced clocks, etc.
		    	document.getElementById("connection").textContent="Connected Successfully. Sync: " + clockOffset ;
			document.getElementById("hb").classList.remove("hb");
			setTimeout(function() {document.getElementById("hb").classList.add("hb");},15000);
	});

}, 30000);

function sendStream(){
	var lifter={};

	lifter.compLetters=compName;
	lifter.lifterName=lifters.activeRw.name;
	lifter.currentAttempt=lifters.activeRw.activeCell(lifters.activeLi);
	lifter.total=lifters.activeRw.total;
	lifter.lifterBW=lifters.activeRw.bw;
	lifter.compStatus=getStatNum(lifters.activeLi);
	lifter.lifterTeam=lifters.activeRw.team;
	lifter.lifterClass=lifters.activeRw.weightClass;
	lifter.lifterCat=lifters.activeRw.division + " - " + lifters.activeRw.ageDiv;
	lifter.lifterFlight=lifters.activeRw.group;
	lifter.nextLoad=lifters.nextAtt["a"];
	lifter.nextRack=lifters.nextAtt["r"];
	lifter.nextName=lifters.nextAtt["n"];
	lifter.nextLot=lifters.nextAtt["l"];
	lifter.lot=lifters.activeRw.lot;
	lifter.rack=document.getElementById("staMisc").innerHTML;
	lifter.bar=setup.bar;
	var fd= new FormData();
	fd.append("json", JSON.stringify(lifter));

fetch("../../iupdate.php",{method:"POST",body:fd})
	.then((response) => {
		return response.text();
	})
	.then((myJson) => {
	console.log(myJson);
	});

	function getStatNum(li){
	var n=0;
	if (li.charAt(0)=="S") n=0;
	if (li.charAt(0)=="B") n=3;
	if (li.charAt(0)=="D") n=6;
	n+=parseInt(li.slice(-1));
	return n
	}

} //end function send stream


