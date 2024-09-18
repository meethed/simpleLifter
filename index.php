<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
<title>simpleLifter by Comp Of The Future</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./resources/newstyles.css">

</head>
<body>
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!--end navbar -->

<!-- Content -->
<div class="content">

<!-- News and updates -->
<div class="heading">Updates and alerts</div>
<?php
$c=glob("./news/*.txt");
rsort($c,SORT_NATURAL);
$f=$c[0];
  $content = file($f);
  $d=date_create_from_format("ymd",basename($f,".txt"));
  $d=date_format($d,'d M Y');
  echo "<div class='news-title'>".$d." - ".$content[0]."</div>";
  array_shift($content);
   foreach($content as $lines) {
    echo $lines;
  };
?>
<br><br>
<a href="news.php">For all updates click here</a>
<!-- Comp List -->
<div class="heading">Active Competitions</div>
<div class="compList" id="compList">
		<!-- competitions -->
<?php
// check connection
if ($conn->connect_error) {
  echo '<div class="warning">Connection failed: ' . $conn->connect_error . '</div>';
  die("connection error");
}

$sql = "SELECT fed,compLetters,compName,startDate,endDate from comp where (endDate >=curdate())";
$result = $conn->query($sql);

if ($result->num_rows >0) {
  while ($row = $result->fetch_assoc()) { ?>
    <a class='competitionEntry' data-id='<?php echo $row["compLetters"]; ?>'>
       <img class='comp-image' src='./resources/comp1.jpg' data-id='<?php echo$row["fed"]; ?>'>
        <div class='comp-title'><?php echo $row["compName"]; ?></div>
        <div class='comp-description'>Date: <?php echo $row["startDate"]." - ".$row["endDate"]; ?> </div>
    </a>
  <?php }
}
?>

	</div>

	<div class="heading"></div>
	<img class="footer-image" src="./resources/happyevan.jpg"></img>

<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php");?>
<!-- End Footer -->

<!-- Dummy submit -->
<div id="template" style="display:none">
<form class="frmManager hide" id="frmManager" action="login.php" method="post">
  <a class="btn cmpInputGo" id="cmpInputLive" href="watch.php">Spectator Access</a>
  <input name="pwd" class="cmpInputPWD" id="cmpInputPWD" type="password" placeholder="Access code..."></input>
  <input id="frmLetters" name="compLetters" style="display:none"></input>
  <button name="go" type="submit" class="btn cmpInputGo" id="cmpInputGo">Official Access</button>
</form>
</div>
</body>
<script>
////////////init stuff 
var frmManager=document.getElementById("frmManager");
document.querySelector(".competitionEntry").append(frmManager);
//click handler for the active competitions
var comps=document.querySelectorAll(".competitionEntry");
comps.forEach(c => {
	c.addEventListener("click",function (c) {loadComp(c.target); }); //on click show the menu
});

document.getElementById("template").appendChild(frmManager);
var ce=document.querySelectorAll(".competitionEntry");
ce.forEach((e) => {e.classList.remove("active");e.scrollTo({top:0,left:0,behavior:"smooth"})});


//update images at load time rather than php time
comps.forEach(c => {
  if (c.children[0].dataset.id=="IPF") c.children[0].src="./resources/feds/IPF.png"; 
  if (c.children[0].dataset.id=="WDF") c.children[0].src="./resources/feds/WDF.png"; 
  if (c.children[1].innerHTML.search("WCPA")>=0) c.children[0].src="./resources/feds/WCPA.png";
  if (c.children[1].innerHTML.search(/test/i)>=0) c.children[0].src="./resources/comp1.jpg";

  c.children[0].addEventListener("load", e => {p=(212-e.target.height)/2; e.target.style.padding=`${p}px 0`});
});

/////////////// end init stuff



function loadComp(c) {
  
  if (c.parentElement.classList.contains("active") || !c.parentElement.classList.contains("competitionEntry")) {return 0};
 // c.parentElement.scrollIntoView({behavior:"smooth", block: "center", incline:"center"});
    var ce=document.querySelectorAll(".competitionEntry.active");
    ce.forEach((e) => {e.classList.remove("active");e.scrollTo({top:0,left:0,behavior:"smooth"})});
    setTimeout(function() {
    c.parentElement.append(frmManager);
    c.parentElement.classList.add("active");
    document.getElementById("frmLetters").value=c.parentElement.dataset.id;
    document.getElementById("cmpInputLive").href="watch.php?c="+c.parentElement.dataset.id;
    setTimeout(function(){frmManager.parentElement.scrollTo({behavior:"smooth",top:400})},100); //IntoView({behavior:"instant",block:"nearest",inline:"nearest"});
    //frmManager.scrollIntoView({behavior:"smooth",block: "end", inline:"nearest"});
    frmManager.parentElement.scrollIntoView({behavior:"smooth",block:"center",inline:"nearest"});
    },100);
}

function submitQuery(event) {
event.preventDefault();
}


</script>
</html>

