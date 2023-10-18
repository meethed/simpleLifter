<!DOCTYPE HTML>
<html>
<head>
<title>simpleLifter OBS Livestream</title>
<link rel="stylesheet" href="./resources/stream.css">
</head>
<body>
<!-- <button onclick="toggleInfo()">Toggle Lifter Info</button>
<button onclick="toggleAttempt()">Toggle Lifter Attempt</button> -->
<div id="lifterInfo">
	<div id="lifterName">Test Layout</div>
	<div class="cnt"><div id="labelPos" class="lbl">Place: </div><div class="inf" id="lifterPos">1st</div></div>
        <div class="cnt"><div id="labelTotal" class="lbl">Total: </div><div class="inf" id="lifterTot">500kg</div></div>
        <div class="cnt"><div id="labelTeam" class="lbl">Team: </div><div class="inf" id="lifterTm">VIC</div></div>
	<div class="cnt"><div id="labelCat" class="lbl">Weight Class: </div><div class="inf" id="lifterCat">120kg</div></div>
	<div class="cnt"><div id="labelAge" class="lbl">Category: </div><div class="inf" id="lifterAge">120kg</div></div>
	<div class="cnt"><div id="labelPB" class="lbl">Squat PB: </div><div class="inf" id="lifterPB">120kg</div></div>
	<div class="cnt"><div id="labelTotal" class="lbl">Total PB: </div><div class="inf" id="lifterTotal">120kg</div></div>
	<img id="pic" style="z-index: -1; display: none;"  src="./simpleLifter/integrate/pics/blank.jpg"/>
</div>
<div id="lifterAttempt">
	<div class="cont"><div class="info" id="attemptName">Test Layout</div></div>
	<div class="cont gd num"><div class="info" id="attemptNum1">240kg</div></div>
	<div class="cont bd num"><div class="info" id="attemptNum2">250kg</div></div>
	<div class="cont at num"><div class="info" id="attemptNum3">260kg</div></div>
	<div class="cont at num" id="cTot"><div class="info" id="attemptTot">660</div></div>
	<div class="cont"><div class="info" id="attemptGouge">(Squat 1) Open Men Equipped -120kg</div></div>
	<iframe id="lt" src="./comp.php?s=OBS&compName=<?php echo $_GET["c"] ?>"+></iframe>
</div>

</body>
<script>
var tickInterval,
 currentJSON,
 currentLights,
 oldJSON,
 oldLights,
 setup=[],
 si=0,
 lastUpdate="2020-01-01 00:00:00";

var compName=[];
compName="<?php echo filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING); ?>".toUpperCase().split(",");
var simple="<?php echo filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING); ?>";
compName.forEach((e,i) => {
  fetch("./simpleLifter/integrate/saveload.php?q=loadsetup&comp="+e).then(response=>response.json().then(d=>{setup[i]=d}));
});

if (compName=="AGZ") document.getElementById("pic").style.display="block";
//set up the interval timer
tickInterval=setInterval(tick, 2000);


function toggleInfo() {
if (!simple) { //if it's simple then never display this bit
  document.getElementById("lifterInfo").classList.remove("visible");
  var oldnode = document.getElementById("lifterInfo");
  var newnode = oldnode.cloneNode(true);
  oldnode.parentNode.replaceChild(newnode,oldnode);
  document.getElementById("lifterInfo").classList.add("visible");
}
}

function toggleAttempt() {
document.getElementById("lifterAttempt").classList.toggle("visible");

}

function tick() { //tick Interval
compName.forEach((e) => {
  fetch("lights.php?compLetters="+e,{method:"GET"})
    .then((response) => {
    return response.json();})
    .then((myJSON) => {
        if (myJSON.updated > lastUpdate) {
        si=compName.indexOf(myJSON.compLetters);
        interpret(myJSON);
        oldJSON=myJSON;
        lastUpdate=myJSON.updated;
    }
  });
});
//iframe monitoring
var ltf=document.getElementById("lt").contentWindow.document;

if (ltf.getElementById("choiceL").classList.contains("visible") || ltf.getElementById("choiceR").classList.contains("visible") || ltf.getElementById("choiceC").classList.contains("visible") || ltf.getElementById("timer").classList.contains("visible")) {
document.getElementById("lifterAttempt").classList.add("visible");
} else document.getElementById("lifterAttempt").classList.remove("visible");

} //end function tick

function interpret(js) { //call this one when something has changed
if (document.getElementById("lifterName").innerHTML != js.lifterName) { //if the lifter name has changd, then update the lifter data and reset


  //get the attempt info & PBs
  var lifterInfo=getLifter(js);


}
} //end function interpret


function doStat(s) {
  switch (s) {
  case 1:
    return "gd";
    break;
  case -1:
    return "bd";
    break;
  case 0:
   return "at";
   break;
 }
} //end function doStat
function interpretBW(js) {
  var d=[0];
  var wclass=js.lifterClass;
  var bw=js.lifterBW;
  var g=js.lifterCat.charAt(0);
  var age=js.lifterCat.split("-")[3].slice(-1);
  if (age=="S" || age=="r") {age="J"} else {age="O"}
  switch (g) {
	  case "F": d=setup[si].fW; break;
	  case "X": d=setup[si].xW; break;
	  case "M": 
	  default:
	  	d=setup[si].mW; break;
}

	if (!d || d.length==1) 
		if (gender==="F") {d=[47,52,57,63,69,76,84,1000];} else {d=[56,59,66,74,83,93,105,120,1000];};
	var wc = d.find(e => e >= bw);
	var ix=d.findIndex(e => e >=bw);
	
	if (ix==d.length-1) wc=d.at(-2)+"+";
	if (ix==0 && age!="O") wc=d[1];
	
	if (!wc) wc="";
	return wc+"kg";

} //end function interpretBW

function interpretCat(js) {
// what we want is Age Gender Type Comp
// eg Open Male Classic 3-Lift
// Junior Female Equipped Bench Only

  var goodCat="";
var c=js.lifterCat.split("-").map(item => item.trim());
// first get the age
switch (c[3].charAt(0)) {
case "O":
goodCat="Open ";
break
case "S":
goodCat="Sub-Junior ";
break
case "J":
goodCat="Junior ";
break
case "M":
goodCat="Masters " + c[3].charAt(1) + " ";
break
}
if (setup[si].openOnly) goodCat="Open ";

//then the gender
if (c[0]=="F") goodCat += "Women ";
if (c[0]=="M") goodCat += "Men ";
if (c[0]=="X") goodCat += "Mx ";

// then the comp category

if (c[1]=="CL") goodCat += "Classic ";
if (c[1]=="CR") goodCat += "Classic Raw ";
if (c[1]=="EQ") goodCat += "Equipped ";
if (c[1]=="SO") goodCat += "Special Olympics ";
// then the events entered

if (c[2]=="BP") goodCat += "Bench Only ";
if (c[2]=="PL") goodCat += "3-Lift ";
if (c[2]=="PP") goodCat += "Push Pull ";
if (c[2]=="DL") goodCat += "Deadlift Only ";
//if (c[2]=="SO") goodCat += "Special Olympics ";

return goodCat;
} //end function interpretCat

function compStat(js) {
var out;
var stat=js.compStatus;
  switch(stat) {
    case 1:
    case 2:
    case 3:
      out = "Squat ";
      out += stat;
      break;
    case 4:
    case 5:
    case 6:
      out = "Bench ";
      out += stat-3;
      break;
    case 7:
    case 8:
    case 9:
      out = "Deadlift ";
      out += stat-6;
      break;
}
return "("+out+") ";
} //end function compStat

function getLifter(js) { //get the lifter details and update the divs
  var setNow="";
  var name=js.lifterName;
  var stat=js.compStatus;
 switch (Math.floor((stat-1)/3)) {
   case 0:
     stat="sq";
     break;
  case 1:
     stat="bp";
     break;
  case 2:
    stat="dl";
    break;
 };

  fetch("curLifter.php?c="+js.compLetters+"&n="+name+"&s="+stat,{method:"GET"})
    .then((response) => {
      return response.json();
    })
    .then((lifterInfo) => {

   //update current lifter data if lights are already off
   if (!document.getElementById("lifterAttempt").classList.contains("visible")) {

  document.getElementById("attemptNum1").innerHTML=doNum(lifterInfo.a1);
  document.getElementById("attemptNum2").innerHTML=doNum(lifterInfo.a2);
  document.getElementById("attemptNum3").innerHTML=doNum(lifterInfo.a3);
  document.getElementById("attemptTot").innerHTML=doNum(js.total);
  document.getElementById("attemptNum1").parentNode.className = "cont num " + doStat(lifterInfo.s1);
  document.getElementById("attemptNum2").parentNode.className = "cont num " + doStat(lifterInfo.s2);
  document.getElementById("attemptNum3").parentNode.className = "cont num " + doStat(lifterInfo.s3);

 document.getElementById("lifterName").innerHTML = js.lifterName;
 document.getElementById("lifterPos").innerHTML = ordinal(lifterInfo.place);
 document.getElementById("attemptName").innerHTML =js.lifterName + " - " + js.currentAttempt + "kg";
 document.getElementById("lifterTot").innerHTML= js.total+"kg";
 document.getElementById("lifterTm").innerHTML = js.lifterTeam;
 document.getElementById("lifterCat").innerHTML = interpretBW(js)
 document.getElementById("lifterAge").innerHTML = interpretCat(js);
 document.getElementById("attemptGouge").innerHTML = compStat(js) + interpretCat(js) + interpretBW(js);

 if (js.compStatus==1) {
  document.getElementById("lifterTot").style.display="none";
  document.getElementById("cTot").style.display="none";}

 else {
  document.getElementById("lifterTot").style.display="inline";
  document.getElementById("cTot").style.display="inline-block"};

  //clear team if it's blank
  if (js.lifterTeam.length<2) {document.getElementById("lifterTm").parentElement.style.display="none"} else {document.getElementById("lifterTm").parentElement.style.display="block";};
  //set the PBs
  if (!stat) stat="sq";
  switch (stat) {

  case "sq":
  document.getElementById("labelPB").innerHTML="Squat PB: ";
  var cur=Math.max(lifterInfo.a1*lifterInfo.s1,lifterInfo.a2*lifterInfo.s2,lifterInfo.a3*lifterInfo.s3,doNum(lifterInfo.pb[0]),0);
  if (cur>doNum(lifterInfo.pb[0])) {setNow="*";}
  break;
  case "bp":
  document.getElementById("labelPB").innerHTML="Bench Press PB: ";
  var cur=Math.max(lifterInfo.a1*lifterInfo.s1,lifterInfo.a2*lifterInfo.s2,lifterInfo.a3*lifterInfo.s3,doNum(lifterInfo.pb[1]),0);
  if (cur>doNum(lifterInfo.pb[1])) {setNow="*";}
  break;
  case "dl":
  document.getElementById("labelPB").innerHTML="Deadlift PB: ";
  var cur=Math.max(lifterInfo.a1*lifterInfo.s1,lifterInfo.a2*lifterInfo.s2,lifterInfo.a3*lifterInfo.s3,doNum(lifterInfo.pb[2]),0);
  if (cur>doNum(lifterInfo.pb[2])) {setNow="*";}
  break;
  }

  document.getElementById("lifterPB").innerHTML=cur + "kg"+setNow;
 
  var curt=Math.max(doNum(lifterInfo.pb[3]), js.total,0);
  document.getElementById("lifterTotal").innerHTML=curt + "kg";
  if (curt>doNum(lifterInfo.pb[3])) document.getElementById("lifterTotal").innerHTML += "*";

  //do the lifter picture bit
  fn=filterName(js.lifterName);
  fetch("./simpleLifter/integrate/pics/"+fn+".jpg", { method: "HEAD" })
   .then(res => {
     if (res.ok) {
  document.getElementById("pic").src="./simpleLifter/integrate/pics/"+fn+".jpg";
    } else {
  document.getElementById("pic").src="./simpleLifter/integrate/pics/blank.jpg";
  }
  }).catch(err => console.log("Error:", err));
  //pic updated or set to blank
  

  setTimeout(toggleInfo,2000); //if the lifter name has changed, flash up the new info box
} // this is if the lights are still showing
else {
setTimeout(function() {getLifter(js)},2000)
}

    }); //end lifter update when we get the fetch


} //end function getLifter

	
function filterName(n) {
fn = n.toLowerCase().replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,"");
fn = fn.replace(/\s+/g,"");
return fn
} //end function filterName


function doNum(n) {
var dn=parseFloat(n) || 0;
return dn;
} //end function donum

function ordinal(i) { //add the st,nd,rd,th as required

    if (!i) return "";

    var j = i % 10,
        k = i % 100;
    if (j == 1 && k != 11) {
        return i + "st";
    }
    if (j == 2 && k != 12) {
        return i + "nd";
    }
    if (j == 3 && k != 13) {
        return i + "rd";
    }
    return i + "th";
}


</script>

</html>
