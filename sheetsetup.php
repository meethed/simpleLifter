<?php include_once("./includes/config.inc");
$sql = "select seshs from comp where compLetters='".$_SESSION["compLetters"]."'";
$result=$conn->query($sql)->fetch_assoc();

$dsp=explode(",",$result["seshs"]);
foreach ($dsp as $d) {
if (substr($d,0,1) > $maxd) {$maxd=(int) substr($d,0,1);};
if (substr($d,1,1) > $maxp) {$maxp=(int) substr($d,1,1);};
if (substr($d,2,1) > $maxs) {$maxs=(int) substr($d,2,1);};
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body>
<!-- Content -->
<div class="content">
<div class="heading">Competition Setup - <?php echo $_SESSION["compName"];?></div>
<div class="setupbox" id="configbox">
  <?php if ($_SESSION["sheet"]==1) { ?>
    <h1>Tech Desk Spreadsheet options</h1>
    <label for="auto">Auto referee (good/no lift based on referee lights)?<input type="checkbox" name="auto" id="auto" onchange="saveConfig();"></label>
    <label for="countup">Progress groups from A-Z (off is from Z-A)<input type="checkbox" name="countup" id="countup" onchange="saveConfig();"></label>
    <label for="showlights">Show the lights & timers box (top right)<input type="checkbox" name="showlights" id="showlights" onchange="saveConfig();"></label>
    <label for="autobreaks">Automatic break timer? (20 mins if 1 flight, 10 minutes multiple flights)<input type="checkbox" name="autobreaks" id="autobreaks" onchange="saveConfig();"></label>
    <label for="simplelights">Simplified referee lights? (Red and White only)<input type="checkbox" name="simplelights" id="simplelights" onchange="saveConfig();"></label>
    <label for="sqw">Bar + Collars for Squat<input type="number" name="sqw" id="sqw" onchange="saveConfig();" placeholder="20"></label>
    <label for="bpw">Bar + Collars for Bench Press<input type="number" name="bpw" id="bpw" onchange="saveConfig();" placeholder="20"></label>
    <label for="dlw">Bar + Collars for Deadlift<input type="number" name="dlw" id="dlw" onchange="saveConfig();" placeholder="20"></label>
  <?php }?>

</div>

<!-- End Content -->
</div>

</body>

<script>
let compLetters="<?php echo $_SESSION["compLetters"]; ?>";
let config={}

fetch("./users/"+compLetters+".json").then(response => response.json()).then(data => {config=data;loadConfig()});

function loadConfig() {
  for (const [key,value] of Object.entries(config)) {
    let a=document.querySelector(`#${key}`);
    if (a) {
      if (key==a.id && a.type=="checkbox") a.checked=value;
      if (key==a.id && a.type!="checkbox") a.value=parseFloat(value);
    }
  }
  window.opener.fedSetupPrep();
} //end function loadConfig

function saveConfig() {
  config={};
  let a=document.querySelectorAll("#configbox input");
  a.forEach(e => {
    if (e.type=="checkbox") {config[e.id]=e.checked} else {config[e.id]=parseFloat(e.value)}
    })


  l = JSON.stringify(config);
  console.log(l);
  fetch("./simpleLifter2/saveconfig.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',
      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => {
    console.log(res)
    window.opener.updateConfig();
  });
} //end function config

</script>
</html>

