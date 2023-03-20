<!DOCTYPE html>
<html>
<?php
$compName = $_GET['c'];
?>

<head>
<title>Lights & Plates</title>
<link rel="stylesheet" href="../../resources/plateLights.css">
</head>
<body>
<div id="p1" class="plateSet">
	<div id="lt1"></div>
	<div id="lightsframe">
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
	</div>
	<div id="timer1"></div>
	<div id="timer2"></div>
	<div id="plates1" class="plateHolder"></div>
	<div id="lt12"></div>
</div>

<div id="p2" class="plateSet">
	<div id="lt2"></div>
	<div id="plates2" class="plateHolder"></div>
	<div id="lt22"></div>
</div>
 
</body>
<script>
var cN="<?php echo $compName ?>";
var timer1,
 timer2,
 interval1,
 interval2,
 clockOffset=0,
 d,
 lightVars,
 t,
 tic,
 oldD,
 newD,
 target1,target2,
 globalPlates = [25, 20, 15, 10, 5, 2.5, 1.25, 0.5, 0.25],
 lights=document.getElementById("lightsframe"),
 choiceL=document.getElementById("choiceL"),
 choiceC=document.getElementById("choiceC"),
 choiceR=document.getElementById("choiceR"),
 failL=document.getElementById("failL"),
 failC=document.getElementById("failC"),
 failR=document.getElementById("failR"),
 timer1=document.getElementById("timer1"),
 timer2=document.getElementById("timer2");

// add global listeners here
window.addEventListener("resize",offsetText,false);
if (cN) tic = setInterval(tick,1000); //this gets us running

function offsetText() {
	let w = document.body.clientWidth;
	let h = document.body.clientHeight;
	let z = h/960;
	console.log (w);
	timer1.style.paddingLeft=(w/2-(400*z)) + "px";
}

//function tick is the php getter if it's running remotely
function tick() {

	var tickD=checkUpdate();

	if (tickD!="") {
		console.log(tickD);

		drawPlates1(tickD.lifterName, tickD.currentAttempt,tickD.lot,tickD.rack,tickD.bar);
		drawPlates2(tickD.nextName,tickD.nextLoad,tickD.nextLot,tickD.nextRack,tickD.bar);

		drawLights([tickD.leftLight, tickD.centreLight, tickD.rightLight]);
		if (lights.style.display!="block")
			if (target1 != tickD.timeTo)
				setTimer1(tickD.timeTo);
		if (target2 != tickD.timeTwo)
			setTimer2(tickD.timeTwo);
	}	
}



function checkUpdate() {

	//lights information
	fetch('../../lights.php?compLetters='+cN).then((response)=>response.json())
	.then((data) => {
	newD=data;
	});
	if (JSON.stringify(newD)!=JSON.stringify(oldD))
	{
		oldD=newD;
		return newD;
	} else {return "";};
}


function drawLights(l) {
var lds;
	if (l.every(x=> x==0)) { //if all zero
		lds = document.querySelectorAll(".round");
		lds.forEach((e) => {e.className="round"});
		lights.style.display="none";
	} else
	{ lights.style.display="block"; timer1.classList.remove("visible"); plates1.style.display="none";}	
; //end all zero

	if (l.some(x=> x>0 && !l.every(x=>x>0))) { //if some are zero
		document.querySelectorAll(".round").forEach((e) => {e.className="round"});
		if (l[0]) {choiceL.className="round pending";}
		if (l[1]) {choiceC.className="round pending";}
		if (l[2]) {choiceR.className="round pending";}
	} //end some zero

	if (l.every(x=>x>0)) { //if they're all on
	if (l[0]==1)
		{choiceL.className="round white"; failL.className="round";} else {choiceL.className="round red"; failL.className="round "+g(l[0]);}
	if (l[1]==1)
		{choiceC.className="round white"; failC.className="round";} else {choiceC.className="round red"; failC.className="round "+g(l[1]);}
	if (l[2]==1)
		{choiceR.className="round white"; failR.className="round";} else {choiceR.className="round red"; failR.className="round "+g(l[2]);}


	} //end all on




} //end drawLights

function g(c) {
if (c==1) return "white";
if (c==2) return "red";
if (c==3) return "yellow";
if (c==4) return "blue";
}


function setTimer1(t) {
	var diff;
	var d= new Date();
	t=t.split(/[- :]/);
	var t1= new Date(Date.UTC(t[0],t[1]-1,t[2],t[3],t[4],t[5]));

	diff=t1-d; //t1 is the timer 1 time from the server, d is the current date

	if (diff<=0) {clearTimer1(); return false};

	if (diff>0) { //if there's a positive difference, it means we need to update the timer
		target1=t1;
		timer1.classList.add("visible");
		plates1.style.display="none";
		clearInterval(interval1);
		getHeartbeat();
		interval1=setInterval(function() {
			var now = new Date().getTime();
			var distance = t1-now-(clockOffset*1000);

			var seconds=Math.floor((distance % (1000*60)) /1000);
		
			if (seconds>30) { timer1.style.color="#FFF" } else 
				if (seconds>10) {timer1.style.color="#970" } else 
					if (seconds<=10) { timer1.style.color="#f00" };
			if (seconds<0) { //if the timer has run down - go back to showing the plates
				clearTimer1();
				 return false};
			seconds = ('00'+seconds).slice(-2);
			
			timer1.innerHTML="0:"+seconds;
		},250); //end timer 1 tick function
	} //


} //end function setTimer1


function setTimer2(t) {
	var diff;
	var d= new Date();
	t=t.split(/[- :]/);
	var t2= new Date(Date.UTC(t[0],t[1]-1,t[2],t[3],t[4],t[5]));

	diff=t2-d; //t1 is the timer 1 time from the server, d is the current date

	if (diff<=0) {clearTimer2(); return false};

	if (diff>0) { //if there's a positive difference, it means we need to update the timer
		target2=t2;
		timer2.classList.add("visible");
		//plates1.style.display="none";
		clearInterval(interval2);
		getHeartbeat();
		interval2=setInterval(function() {
			var now = new Date().getTime();
			var distance = t2-now-(clockOffset*1000);

			var seconds=Math.floor((distance % (1000*60)) /1000);
			var minutes=Math.floor((distance % (1000*60*60)) /60000);
			if (seconds<0) { //if the timer has run down - go back to showing the plates
				clearTimer2();
				 return false};
			seconds = ('00'+seconds).slice(-2);
			timer2.innerHTML=minutes+":"+seconds;
		},250); //end timer 2 tick function
	 //
	}
} //end function setTimer2

function clearTimer1() {
	timer1.classList.remove("visible");
  timer1.innerHTML="";
	clearInterval(interval1);
} //end function clearTimer1

function clearTimer2() {
	timer2.classList.remove("visible");
	timer2.innerHTML="";
	clearInterval(interval2);
	} //end function clearTimer2

function clearLights() {
drawLights([0,0,0]);
} //end function clearLights

function drawPlates1(n,w,l,r,b) {
	ps=plates1;
	lights.style.display="none";
  ps.style.display="inline-flex";
  var c=ps.lastElementChild;
  while (c) {
    ps.removeChild(c);
    c=ps.lastElementChild;
  }
  if (!w) return false;
  var plates=getPlates(w,b);

  for (i=0;i<plates.length;i++){
    var weight=globalPlates[i];
    if (plates[i]>0){
      var plateHolder=document.createElement("div");
      plateHolder.id="plateHolder"+weight;
      plateHolder.classList.add("plateHolder");
      ps.appendChild(plateHolder);

      for (c=0;c<plates[i];c++){
        var plate=document.createElement("div");
        plate.id=i+"plate"+c;
        plate.classList.add("plate");
        plate.classList.add("p"+weight*100);
        //plate.innerHTML=weight=" - " + c;
        plateHolder.appendChild(plate);

      } //end add multiple plates loop

      if (i==0) {var text=document.createElement("div"); //if>
      text.innerHTML=plates[i]+"x";
      text.classList.add("plateText");
      plateHolder.prepend(text); }
    }
  }

	document.getElementById("lt1").innerHTML="<h4>[#"+l+"] "+n + "<br>"+ w + "kg<br><br>"+getLoad(w)+"<br>"+r+"</h4>";
//	document.getElementById("lt12").innerHTML="<h2>"+getLoad(w)+r;
} //end function drawPlates

function drawPlates2(n,w,l,r,b) {
	ps=plates2;
	lights.style.display="none";
  ps.style.display="inline-flex";
  var c=ps.lastElementChild;
  while (c) {
    ps.removeChild(c);
    c=ps.lastElementChild;
  }
  if (!w) return false;
  var plates=getPlates(w,b);

  for (i=0;i<plates.length;i++){
    var weight=globalPlates[i];
    if (plates[i]>0){
      var plateHolder=document.createElement("div");
      plateHolder.id="plateHolder"+weight;
      plateHolder.classList.add("plateHolder");
      ps.appendChild(plateHolder);

      for (c=0;c<plates[i];c++){
        var plate=document.createElement("div");
        plate.id=i+"plate"+c;
        plate.classList.add("plate");
        plate.classList.add("p"+weight*100);
        //plate.innerHTML=weight=" - " + c;
        plateHolder.appendChild(plate);

      } //end add multiple plates loop

      if (i==0) {var text=document.createElement("div"); //if>
      text.innerHTML=plates[i]+"x";
      text.classList.add("plateText");
      plateHolder.prepend(text); }
    }
  }

	document.getElementById("lt2").innerHTML="<h4>[#"+l+"] "+n + "<br>"+ w + "kg"+"<br><br>"+getLoad(w)+"<br>"+r+"</h4>";
//	document.getElementById("lt2").innerHTML="<h4>[#"+l+"] "+n + ": "+ w + "kg";
//	document.getElementById("lt22").innerHTML="<h2>"+getLoad(w)+r;
} //end function drawPlates2

function getPlates(w,b) {
	if (!b) {b=25};
	w=w-b; //need to factor in the bar load here

	var plateCount=[];

	globalPlates.forEach((x) => {
		var plate=Math.floor(w/(x*2));
		w=w-x*plate*2;
		plateCount.push(plate);
	});
	return plateCount
} //end function getPlates

function getLoad(w) {
var plateString="";
var nextPlates=getPlates(w);

nextPlates.forEach((x,id) => {
	if (x>1) {plateString+=x + "x" + globalPlates[id]+" / "} else
	if (x>0) {plateString+=globalPlates[id] + " / "}
	
	});
return plateString;
} //end function nextPlates



//random helper functions--------------------------------------------------


function getHeartbeat() { //gets the clock offset
	var hb;
	var d = new Date().getTime()/1000;
	fetch("../../heartbeat.php?t="+Math.random()).then((response) => response.json())
	.then((data) => {
	hb=data;
	clockOffset=(d-hb);
	});

}
</script>


</html>

