<?php
// yes POST no  SESSION = new session with POST data
// no  POST no  SESSION = redirect to index.php
// no  POST yes SESSION = use session data
// yes POST yes SESSION = update with POST data
// yes GET - check if OBS "auto"

if (empty($_POST)) { // if there is no POST data (ie a direct URL)
	if(session_status() !== PHP_SESSION_ACTIVE) {  //if also no session
//		header('Location: index.php'); //no post no session
		die();
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
	}
?>

<!DOCTYPE html>
<html>
	<head>
	<title>simpleLifter Web v0.90 - 
	<?php
// Create connection
$conn = new mysqli('localhost', 'lightsuser','lights','lightsdb');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT compLetters, compID, compName, hish FROM comps WHERE compLetters=\"" . $_POST["compName"]. "\"";
$result = $conn->query($sql);


if ($result->num_rows > 0)  //if there's a competition that matches the compLetters - now there's only 1 unique
  // competition so if the database is corrupted this will be a bit messy but that can't happen as that isn't
  // how the db is set up
  // output data of each row
  while($row = $result->fetch_assoc()) 
    if ($row["hish"]==crypt($_POST["pwd"], substr($row["compLetters"],1,2))) {
	$cl = $row["compLetters"];
	$cn = $row["compName"];
	echo $cn;
	$failed=0;}
    else {$failed=1;};
$conn->close();
?>
		</title>
		<link rel="stylesheet" href="./resources/styles.css">
	</head>
	<body>
	<div id="statusarea">
	<div id="statusbar">
		<div id="compStatus">
			<div id="curLifter">simpleLifter v0.9</div>
			<div id="curAtt">If you can read this, Javascript is disabled. You must have javascript for this to function</div>
			<div id="staMisc">Rack:</div>
			<div id="curGroup" class="btn">A</div>
			<div id="curLift" class="btn">Weigh In</div>
		</div>

	<div id="plates" class="plateHolder"></div>	
	<div id="timer2Frame"></div>
	<div id="lightsFrame">
		<div id="timerFrame">1:00</div>
		<div id="lights"></div>
	</div>
	</div>
	<div id="controls">
		<div class="mbtn" id="btnGood">Good Lift</div>
		<div class="mbtn" id="btnNo">No Lift</div>
		<div class="mbtn" id="btnBar">Bar Loaded</div>
		<div class="mbtn" id="btnTimer">Break Timer...</div>
		<div class="mbtn" id="btnUtil">Utilities...</div>
	</div>
	</div>

	<div id="sheet">
		<div id="tableu">
		<div class="tr" id="head">
			<div class="th" id="g">Gr.</div>
			<div class="th" id="l">Lot</div>
			<div class="th" id="n">Lifter Name</div>
			<div class="th" id="t">Team</div>
			<div class="th" id="y">Birth Year</div>
			<div class="th" id="a">Age Div</div>
			<div class="th" id="d">Division</div>
			<div class="th" id="bw">Body Weight</div>
			<div class="th" id="wc">Weight Class</div>
			<div class="th" id="rs">S Rack</div>
			<div class="th" id="rb">B Rack</div>
			<div class="th" id="s1" onclick="lifters.activeLi='SQ-1';lifters.doSort();lifters.activeRow=0;">SQ&#8209;1</div>
			<div class="th" id="s2" onclick="lifters.activeLi='SQ-2';lifters.doSort();lifters.activeRow=0;">SQ&#8209;2</div>
			<div class="th" id="s3" onclick="lifters.activeLi='SQ-3';lifters.doSort();lifters.activeRow=0;">SQ&#8209;3</div>
			<div class="th" id="bs">Best SQ</div>
			<div class="th" id="b1" onclick="lifters.activeLi='BP-1';lifters.doSort();lifters.activeRow=0;">BP&#8209;1</div>
			<div class="th" id="b2" onclick="lifters.activeLi='BP-2';lifters.doSort();lifters.activeRow=0;">BP&#8209;2</div>
			<div class="th" id="b3" onclick="lifters.activeLi='BP-3';lifters.doSort();lifters.activeRow=0;">BP&#8209;3</div>
			<div class="th" id="bb">Best BP</div>
			<div class="th" id="st">Sub Total</div>
			<div class="th" id="d1" onclick="lifters.activeLi='DL-1';lifters.doSort();lifters.activeRow=0;">DL&#8209;1</div>
			<div class="th" id="d2" onclick="lifters.activeLi='DL-2';lifters.doSort();lifters.activeRow=0;">DL&#8209;2</div>
			<div class="th" id="d3" onclick="lifters.activeLi='DL-3';lifters.doSort();lifters.activeRow=0;">DL&#8209;3</div>
			<div class="th" id="bd">Best DL</div>
			<div class="th" id="to">Total</div>
			<div class="th" id="gl">IPF Points</div>
		</div>
		</div>
	<div class="mbtn" id="btnNew" onclick='lifters.liftList[lifters.liftList.length] = new Lifter("",lifters.liftList.length,"","","","","","","","","");lifters.saveLocal();'>Add Lifter</div> 
	</div>
	<div id="heartbeat">
		<div id="connection"></div>
		<div id="hb"></div>
	</div>
	<div id="menu" class="popup">
		<h2>Competition Setup</h2>
		<div class="cont" id="isEither">
			<p>This is the online version of simpleLifter, meaning it needs an internet connection for full functionality. It will still back up data locally, so can continue if degraded due to internet droppping out.</p>
			<label>Weight of bar+collars<input name="inBarWeight" id="inBarWeight" type="number" value=25></label><br>
		</div>
		<div class="cont">
			<label>Switch Groups: Z to A</label><label class="switch"><input type="checkbox" id="inAlpha" name="inAlpha" checked ><span class="slider"></span></label><label>A to Z</label><br>
			<label>Show Lights Frame?</label><label class="switch"><input type="checkbox" id="inLights" name="inLights"><span class="slider"></span></label><br>
			<label>Auto Attempt/Lift Progression?</label><label class="switch"><input type="checkbox" id="inAuto" name="inAuto"><span class="slider"></span></label><br>
			<label>Auto Referee (Good / No Lift) Progression?</label><label class="switch"><input type="checkbox" id="inAutoRefs" name="inAutoRefs"><span class="slider"></span></label><br>
			<label>Live stream updates for OBS?</label><label class="switch"><input type="checkbox" id="inOBS" name="inOBS"><span class="slider"></span></label><br>
			<label>Bench Press only competition?</label><label class="switch"><input type="checkbox" id="inBP" name="inBP"><span class="slider"></span></label><br>
			<label>CAPO (Glos & Weights)</label><label class="switch"><input type="checkbox" id="inCAPO" name="inCAPO"><span class="slider"></span></label>
		</div>
		<div class="cont">
			<label>Male Weight Classes<input name="inMW" id="inMW" type="string" value="53,56,59,66,74,83,93,105,120"></label><br>
			<label>Female Weight Classes<input name="inFW" id="inFW" type="string" value="43,47,52,57,63,69,67,84"></label><br>
			<label>MX Weight Classes<input name="inXW" id="inXW" type="string" value="53,56,59,66,74,83,93,105,120"></label><br>

		</div>
		<div class="cont">
			<label>Number of lifters<input name="inNumLifters" id="inNumLifters" type="number" value=25></label><br>
			<div class="btn" id="btnReset" onclick="setupReset()">Set Rows (clears data)</div>
		</div>
		<div class="btnBR">
			<div class="btn" id="btnCx" onclick="setupCx()">Cancel</div>
			<div class="btn" id="btnCx" onclick="setupOk()">Change Settings</div>
		</div>
	</div>
	<div id="divs" style="display:none">
		<div class="btn" id="btnSave">Save</div>
		<div class="btn" id="btnLoad">Load</div>	
	</div>
	
	<div id="popupBox" class="popup">
		<p id="msg">Warning Popup</p>
		<div id="btnOk" class="btn" onclick="document.getElementById('popupBox').replaceChildren(msg,btnOk); document.getElementById('popupBox').style.display='none'">Close</div><p></p>
	</div>
	<script>
		failedLogin = <?php echo $failed; ?>;
		compName = "<?php if (!$failed) {echo $cl;};?>";
		fullComp = "<?php if (!$failed) {echo $cn;};?>";
		document.getElementById("curAtt").innerHTML=fullComp;
	</script>	

	<script src="lifter.js"></script>
	<script src="lifters.js"></script>
	<script src="handler.js"></script>
	<script src="scoreboard.js"></script>
	<script src="setup.js"></script>
	<script src="plates.js"></script>
	<script src="input.js"></script>
	<script src="lightTimer.js"></script>
	<script src="windows.js"></script>
        <script src="sessionChange.js"></script>

	<script>

	if (!failedLogin) {
	setup = new Setup();
	lifters = new Lifters(0);
	lifters.loadLocal();
	lifters.activeLi="Weigh In";
	lifters.activeCol=2;
	lifters.activeGp="A";
	lifters.doSort();
	//lifters.activeRow=-1;
	//updateStatus();
	} else document.getElementById("sheet").innerHTML="Failed Login. Incorrect Password.";
	</script>
	</body>
</html>

