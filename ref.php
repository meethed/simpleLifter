<?php include_once("./includes/config.inc");

if (!isset($_SESSION["idx"])) {
  header("location: index.php");
}

if (isset($_GET["pos"])) 
  switch ($_GET["pos"]) {
    case "l":
    case "L":
    $pos="Left Referee";
    $_SESSION["pos"]="l";
    break;
    case "c":
    case "C":
    $pos="Centre Referee";
    $_SESSION["pos"]="c";

    break;
    case "r":
    case "R":
    $pos="Right Referee";
    $_SESSION["pos"]="r";
    break;
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="./resources/buttons.css">
</head>
<body class="ref">

<!-- title -->
<h2 class="ref"><?php echo $_SESSION["compName"]." - ".$pos ?> </h2>

<!-- buttons -->
<div class="btn-container">
  <div id="bl" data-val=60 class="ref-btn grn <?php echo $_SESSION['pos']; ?>">Bar Loaded</div>
  <div id="gl" data-val=1 class="ref-btn wht">Good Lift</div>
  <div id="nr" data-val=2 class="ref-btn red">No Lift - Red</div>
  <div id="nb" data-val=3 class="ref-btn blu">No Lift - Blue</div>
  <div id="ny" data-val=4 class="ref-btn yel">No Lift - Yellow</div>
</div>
<?php if (isset($_SESSION["sheet"])) { ?><iframe id="lights" class="light-frame" src="./platform.php" style:"z-index:-1"></iframe> <?php } else
  if (isset($_SESSION["lights"])) { ?><iframe id="lights" class="light-frame" src="./lights.php" style:"z-index:-1"></iframe> <?php } ?>
</body>
<script>
////////init stuff
//globals
var
 btnTimeout,
 holdTime=500,
 config={},
 compLetters="<?php echo $_SESSION["compLetters"];?>";
//mouse down handler
document.addEventListener("mousedown", buttonpress);
document.addEventListener("touchstart", buttonpress);

//mouse up handler
document.addEventListener("mouseup", buttonoff);
document.addEventListener("touchend", buttonoff);

//load the settings
fetch("./users/"+compLetters+".json").then(response => response.json()).then(data => {
  config=data;
  if (config.simplelights) {
    nb.style.display="none";
    ny.style.display="none";
  }
});


function buttonpress(e) {
  if (!e.target.classList.contains("ref-btn")) return 0;
  clearTimeout(btnTimeout);
  btnTimeout = setTimeout(function() {doClick(e.target)}, holdTime);
  e.target.classList.add("dn");
} //end function buttonpress

function buttonoff(e) {
  if (!e.target.classList.contains("ref-btn")) return 0;
  e.target.classList.remove("dn");
  clearTimeout(btnTimeout);
} //end function buttonoff



function doClick(e) {
var pos="";
//  d=JSON.stringify(d);
  if (e.id=="bl") pos="&p=timeTo";
  fetch("updatelights.php?d="+e.dataset.val+pos, {
  }).then(response => response.json())
  .then(result => {
  if (result[0]=="success") {navigator.vibrate([100])} else {navigator.vibrate([100,50,100])};
  });

} //end function doClick

</script>
</html>

