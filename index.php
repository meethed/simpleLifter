<!DOCTYPE html>
<html>
<head>
<link rel=stylesheet href="./resources/styles.css">
<title>Lights and stream server</title>
</head>
<body>
<div class="backdrop"></div>

<form id="frm" method="POST">
<div id="allContainer" class="containertb">
<h1>Powerlifting lights, timers, spreadsheets,<br>results, and more!</h1>
<p>This suite of tools was written to provide a contemporary, feature-rich and COMPLETELY FREE computer-controlled lighting, timing and live-stream overlay set up for powerlifting competition.</p>
<p>For help, more information on available features, and contacts in case you need assistance, please click the question mark on the right.</p>
<p>Please select the referee position or display type, choose your competition and enter the codeword (if applicable) before clicking "let's go".</p>

<h2>Select Position / Lights</h2>
<div class="question">
<a HREF="reasons.php">&quest;</a>
</div>
</form>

<label class="container">
  <input type="radio" name="pos" id="le" value="leftLight">
  <span class="label">Left Referee</span>
  <span class="checkmark"></span>
</label><br>
<label class="container">
  <input type="radio" checked name="pos" id="ce" value="centreLight">
  <span class="label">Centre Referee</span>
  <span class="checkmark"></span>
</label><br>
<label class="container">
  <input type="radio" name="pos" id="ri" value="rightLight">
  <span class="label">Right Referee</span>
  <span class="checkmark"></span>
</label><br><br>
<label class="container">
  <input type="radio" name="pos" id="li" value="lights">
  <span class="label">Lights</span>
  <span class="checkmark"></span>
</label><br>
<!--<label class="container">
  <input type="radio" name="pos" id="ti" value="timer">
  <span class="label">Timers</span>
  <span class="checkmark"></span>
</label><br>-->

<label class="container">
  <input type="radio" name="pos" id="td" value="techDesk">
  <span class="label">simpleLifter</span>
  <span class="checkmark"></span>
</label><br>
<label class="container">
  <input type="radio" name="pos" id="pL" value="plateLights">
  <span class="label">Platform Display</span>
  <span class="checkmark"></span>
</label><br>

<label class="container">
  <input type="radio" name="pos" id="ss" value="scores">
  <span class="label">Live Scoresheet</span>
  <span class="checkmark"></span>
</label><br><br>

<h2>Choose your competition:</h2>

<?php
include_once "../config.php";
echo '<div class="warning">Connected Successfully</div>';

$sql = "SELECT * FROM comps WHERE (enddate >= curdate())";
//$sql = '';
$result = $conn->query($sql);

//start a new scrollable div
echo '<select class="aninput" name="compName" id="compName" onChange="linky()"><br>';

if ($result->num_rows > 0) {
  //output data of each row
  while($row = $result->fetch_assoc()) {

    echo "<option value='".$row["compLetters"] ."'>".$row["compName"]." - ".$row["startdate"]." to ".$row["enddate"]. "</option>\r\n";
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
<form name="newForm" id="newForm" action="addcomp.php" method="POST">
<div class="question closer"><a HREF="">X</a></div>
<div class="nicebox">
<label class="textcontainer" for="cname">Enter Competition Name:
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
<p>The dates will be used to indicate when the competition is 'active' or not. An inactive comp can not be selected or used. If you want to test, it's recommended you select dates before the competition starts to ensure it's active</p>
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
  <input type="submit" id="submit" class="btn">
</div>

</form>
</div>


<script>
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

function linky() {
var urlstr=self.location;
var cl    = document.getElementById("compName").value;
document.getElementById("newstream").innerHTML="<br>The new version: <br> "+ urlstr + "newStream.php?c="+cl;
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

