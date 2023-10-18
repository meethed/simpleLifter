<!DOCTYPE html>
<html>
<head>
<link rel=stylesheet href="./resources/styles.css">
<title>simpleLifter</title>
</head>
<body>
<div class="backdrop"></div>

<form id="frm" method="POST">
<div id="allContainer" class="containertb">
<h1><i>simpleLifter</i></h1><h2>Powerlifting competition management system</h2>
<p>This suite of tools was written to provide a contemporary, feature-rich and COMPLETELY FREE computer-controlled lighting, timing and live-stream overlay set up for powerlifting competition.</p>
<p>For help, more information on available features, and contacts in case you need assistance, please click the question mark on the right.</p>
<p>Please select the referee position or display type, choose your competition and enter the codeword (if applicable) before clicking "let's go".</p>

<h2>Select Position / Lights</h2>
<div class="question">
<a HREF="reasons.php"><strong>Help</strong></a>
</div>
</form>

<label class="container positions">
  <input type="radio" name="pos" id="le" value="leftLight">
  <span class="label">Left Referee</span>
  <span class="checkmark"></span>
</label><br>
<label class="container positions">
  <input type="radio" checked name="pos" id="ce" value="centreLight">
  <span class="label">Centre Referee</span>
  <span class="checkmark"></span>
</label><br>
<label class="container positions">
  <input type="radio" name="pos" id="ri" value="rightLight">
  <span class="label">Right Referee</span>
  <span class="checkmark"></span>
</label><br><br>
<label class="container positions">
  <input type="radio" name="pos" id="li" value="lights">
  <span class="label">Lights</span>
  <span class="checkmark"></span>
</label><br>
<!--<label class="container positions">
  <input type="radio" name="pos" id="ti" value="timer">
  <span class="label">Timers</span>
  <span class="checkmark"></span>
</label><br>-->

<label class="container positions">
  <input type="radio" name="pos" id="td" value="techDesk">
  <span class="label">simpleLifter</span>
  <span class="checkmark"></span>
</label><br>
<label class="container positions">
  <input type="radio" name="pos" id="pL" value="plateLights">
  <span class="label">Platform Display</span>
  <span class="checkmark"></span>
</label><br>

<label class="container positions" id="posScoresheet">
  <input type="radio" name="pos" id="ss" value="scores">
  <span class="label">Live Scoresheet</span>
  <span class="checkmark"></span>
</label><br><br>

<h2>Choose your competition:</h2>

<?php
include_once "../config.php";
// check connection
if ($conn->connect_error) {
  echo '<div class="warning">Connection failed: ' . $conn->connect_error . '</div>';
  die("connection error");
}
echo '<div class="warning">Connected Successfully</div>';


$sql = "SELECT isParent, compName, compLetters,startdate,enddate FROM comps WHERE (enddate >= curdate()) ORDER BY parentComp ASC,startdate,compName";
//$sql = '';
$result = $conn->query($sql);

//start a new scrollable div
echo '<select class="aninput" name="compName" id="compName" onChange="linky()"><br>';

if ($result->num_rows > 0) {
  //output data of each row
  while($row = $result->fetch_assoc()) {

    echo "<option data-isParent='".$row["isParent"]."' value='".$row["compLetters"] ."'>".$row["compName"]." - ".$row["startdate"]." to ".$row["enddate"]. "</option>\r\n";
  }
} else {
  echo "<option>There are no active competitions</option>";
}


echo "</select>";
$conn->close();

?>
<br>Access Code:<input class="aninput" type="password" name="pwd">
<br>
<button class="btn" id="gogogo" onclick="sendUpdate()">Let's go!</button>
<br>
<br>
<h2>If you want to add a new competition click here: </h2>
<button class="btn" type="button" id="newComp">New comp...</button>
<br><br>
<h2>The overlay link (to copy into OBS) is here:</h2>
<div id="newstream"></div>
<h2>Archived competition results:</h2>
<a href="archives.php">Click here!</a>


</div>


<div class="containerntb" id="popupbox">
<h1>New Competition Setup</h1>
<form name="newForm" id="newForm" method="POST" action="">
<div class="question closer"><a HREF="">X</a></div>
<div class="nicebox">
<label class="textcontainer" for="newCompName">Enter Competition Name:
  <input type="text"  name="newCompName" id="newCompName">
</label>
<p>The competition name will be displayed on the lights and all referee pages. It'll also be used to select the competition from the main page</p>
</div>
<div class="nicebox">
<label class="textcontainer" for="startdate">Date From:
  <input type="date"  name="startdate" id="startdate">
</label><br>
<label class="textcontainer" for="enddate">Date To:
  <input type="date"  name="enddate" id="enddate">
</label>
<br>
<p>The start date is used to determine when the competition / session becomes active. For a multi-session competition, the individual sessions are automaticcaly assigned to days and will be hidden on completion. The main competition will be visible until the end date.</p>
</div>
<div class="nicebox">
<p>If you are running multiple sessions, input the number of days/platforms/sessions in the boxes below. If it is only one session, set everything to 1</p>
<label class="textcontainer" for="days">Days:
<input type="number" name="days" id="days" min=1 max=10 value=1 oninput="setMulti()"></input></label>
<label class="textcontainer" for="sessions">Sessions:
<input type="number" name="sessions" id="sessions" min=1 max=10 value=1 oninput="setMulti()"></input></label>
<label class="textcontainer" for="platforms">Platforms:
<input type="number" name="platforms" id="platforms" min=1 max=10 value=1 oninput="setMulti()"></input></label>
</label><br>
<div class="nicebox" id="multibox"><div class="ms"  data-compname="Competition" data-sd="2023-01-01" data-sesh="">2023-01-01 Competition</div></div>
</div>
<div class="nicebox">
<label class="textcontainer" for="pwd">Enter Access Code:
  <input class="aninput" type="password"  name="pwd" id="pwd">
</label>
<p>When you connect, you must enter the correct code to login in</p>
</div>
<div class="nicebox">
<label class="textcontainer" for="contact">Enter your contact email address:
  <input class="aninput" type="text"  name="contact" id="contact">
</label>
<p>Whilst you don't have to, I'd really appreciate it if you provided me a contact email so I know who is using the program and to reach out and help</p>
</div>
<div class="nicebox">
  <input type="submit" id="submit" class="btn"></input>
</div>

</form>
</div>


<script>
startdate.valueAsDate = new Date();
enddate.valueAsDate = new Date();

newCompName.addEventListener("input",(e) => {

let c=document.querySelectorAll(".ms");
c.forEach((e) => {e.dataset.compName=newCompName.value; e.innerHTML=e.dataset.sd+" - " + e.dataset.compName + " " + e.dataset.sesh;});
});

enddate.addEventListener("change",() => {upDate()});
startdate.addEventListener("change",() => {upDate()});

function upDate() {
  let sd=new Date(startdate.value);
  let ed=new Date(enddate.value);
  days.value=Math.ceil((ed-sd)/(1000*3600*24))+1;
  setMulti();
}
newForm.addEventListener("submit",(e) => {
e.preventDefault();
sendForm();
});



linky();
var popup= document.getElementById("popupbox");
var btn  = document.getElementById("newComp");
var closer= document.getElementsByClassName("closer")[0];
var backdrop = document.getElementsByClassName("backdrop")[0];


btn.onclick = function() {
	popup.style.display = "block";
	backdrop.style.display = "block";
	window.scrollTo(0,0);
} // end btn.onclick

closer.onclick = function() {
	popup.style.display = "none";
	backdrop.style.display = "none";

} //end closer.onclick

window.onclick = function(event) {
	if (event.target == popup) {
	modal.style.display = "none";
	backdrop.style.display = "none";
	}
} //end window.onclick

function setMulti() {
  let multi = document.getElementById("multibox");

  multi.replaceChildren();
  for (d=1;d<=days.value;d++)
    for (s=1;s<=sessions.value;s++)
      for (p=1;p<=platforms.value;p++) {
        let dsp="";
        if (days.value>1) dsp=" Day " +d;
        if (sessions.value>1) dsp+=" Session " +s;
        if (platforms.value>1) dsp+=" Platform "+p;
        let e=document.createElement("div");
        e.classList.add("ms");
        let date=new Date(startdate.value);
        date.setDate(date.getDate()+d-1);
        e.innerHTML=date.toISOString().substring(0,10)+" - "+newCompName.value+dsp;
        e.dataset.compName=newCompName.value;
        e.dataset.sd=date.toISOString().substring(0,10);
        e.dataset.sesh=dsp;
        e.addEventListener('click',(event) => {event.target.remove()});
        multi.append(e);
      }
  
}

function sendForm() {
  let promises=[];
  //first up send the main form
  const frm = new FormData(document.getElementById("newForm"));
  fetch("addcomp.php", {
   method: "POST",
   body: frm,
  }).then((response) => response.json())
    .then((data) => {
    console.log(data.compLetters);
    let newCL=data.compLetters;    
//now send the rest of the comps
    if (days.value+sessions.value+platforms.value!="111") {
      const cn=newCompName.value;
      let multis=document.getElementById("multibox").children;
      for (c of multis){
        newCompName.value=c.dataset.compName+c.dataset.sesh;
        startdate.value=c.dataset.sd;
        enddate.value=c.dataset.sd;
        const form = new FormData(document.getElementById("newForm"));
        form.append("parentComp",newCL);
        promises.push( fetch("addcomp.php", { method: "POST", body: form}));
      } //end the loop adding the fetch promises
    } //end if we have multiple comps
    Promise.all(promises).then(setTimeout(location.reload(),1000));;
  }) //form send
} //end sendForm function

function linky() { //this is actually the comp update function
var urlstr=self.location;
var cl    = document.getElementById("compName").value;
document.getElementById("newstream").innerHTML="<br>The new version: <br> "+ urlstr + "newStream.php?c="+cl;

if (compName.options[compName.selectedIndex].dataset.isparent==1) {
c=document.querySelectorAll(".positions");
c.forEach((e) => {e.style.display="none"});
document.getElementById("posScoresheet").style.display="inline-block";
} else {
c=document.querySelectorAll(".positions");
c.forEach((e) => {e.style.display="inline-block"});
}


} //end linky

function sendUpdate(){
form = document.getElementById("frm");
	if (document.getElementById("compName").value=="ADE") {addr="./simpleLifter/integrate/live.php"}else {
	if (document.getElementById("td").checked) {addr="./simpleLifter/integrate/index.php"} else {
	if (document.getElementById("ss").checked) {addr="./simpleLifter/integrate/live.php"}  else {
	if (document.getElementById("pL").checked) {addr="plateLights.php?c=" + document.getElementById("compName").value; }
  else {addr="comp.php";}}}};
	form.method="post";
	form.action=addr;
	form.submit;
}
</script>
</body>
</html>

