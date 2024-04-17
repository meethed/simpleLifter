<?php include_once("./includes/config.inc");
  if ($_GET["f"]!="") {
    $f = filter_input(INPUT_GET,f,FILTER_SANITIZE_STRING); //get the comp session from the url
    $_SESSION["sesh"]=$f;
    header("location: weighin.php");
  } else { //if there's nothing in the GET url
    if ($_SESSION["sesh"]=="") {$_SESSION["sesh"]="111"; header("location: weighin.php");} //set session to 111 and reload
  }

if (!isset($_SESSION["idx"])) {
  header("location: index.php");
}
$sql= "select fed from comp where compLetters='".$_SESSION['compLetters']."'";
$fed=$conn->query($sql)->fetch_assoc()["fed"];
if (!$fed) {$fed="IPF";};
$explodeSession="Day ".substr($_SESSION["sesh"],0,1);
$explodeSession.=" Session ".substr($_SESSION["sesh"],1,1);
$explodeSession.=" Platform ".substr($_SESSION["sesh"],2,1);


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
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">
<div class="heading">Weigh In - <?php echo $_SESSION["compName"]."<br>".$explodeSession;?></div>
<form name="frmWeighIn" id="weighin">
<div id="topbit" style="display:flex">
  <div id="prevbtn" class="weighinbtn" onclick="lastLifter()">&lt;</div>
  <select id="ln"></select>
  <div id="nextbtn" class="weighinbtn" style="text-align:right" onclick="nextLifter();">&gt;</div>
</div>
<fieldset><legend>Weigh In:</legend>
<label for="ibw">BW: <input type="number" id="ibw" name="ibw"></input></label>
<label for="sq1">Squat 1: <input type="number" id="sq1" name="sq1"></input></label>
<label for="bp1">Bench 1: <input type="number" id="bp1" name="bp1"></input></label>
<label for="dl1">Deadlift 1: <input type="number" id="dl1" name="dl1"></input></label>
</fieldset>
<button id="submit" action="" class="weighingobtn">Save & Next</button>

<fieldset><legend>Lifter Particulars:</legend>
<label for="lot">Lot #: <input type="number" id="lot" name="lot"></input></label>
<label for="lifterName">Name: <input id="lifterName" name="lifterName"></input></label>
<label for="iwc">Weight class: <select id="iwc" name="iwc"></select></label>
<label for="year">DOB Year: <input type="number" id="year" name="year" min=1900 max=3000></input></label>
<label for="month">DOB Month: <input type="number" id="month" name="month" min=1 max=12></input></label>
<label for="day">DOB Day: <input type="number" id="day" name="day" min=1 max=31></input></label>

<label for="gender">Gender: <select id="gender" name="gender">
  <option id="f" value="F">Female</option><option id="m" value="M">Male</option><option id="x" value="X">Mx</option>
</select></label>
<label for="gear">Equipment: <select id="gear" name="gear">
<option value="CL">Classic</option><option value="eq">Equipped Single Ply</option>
<option value="CS">Classic (Special Olympics)</option><option value="ES">Equipped (Special Olympics)</option>
<option value="AD">Adaptive</option>
<?php if ($fed!="IPF" || $fed=="") {?>
<option value="CR">Classic Raw</option><option value="MP">Equipped Multi Ply</option>
<option value="RT">Tested Raw</option><option value="RU">Untested Raw</option>
<option value="ST">Tested Single ply</option><option value="SU">Untested Single ply</option>
<?php }?>

</select></label>
<label for="lifts">Lifts: <select id="lifts" name="lifts">
  <option value="PL">3 Lift</option>
  <option value="BP">Bench Press</option>
  <option value="PP">Push / Pull</option>
  <option value="--" disabled>--</option>
  <option value="SQ">Squat Only</option>
  <option value="SL">Deadlift Only</option>
  <option value="--" disabled>--</option>
  <option value="SB">Squat / Bench</option>
  <option value="SD">Squat / Dead</option>
</select></label>
</fieldset>

</form>


</div>
<!-- End Content -->



<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script src="./simpleLifter2/feds/<?php echo $fed?>rules.js"> </script>
<script>
year.max=new Date().getFullYear();
let globalLifters=[];
let lifters=[];
let globalSession=<?php echo $_SESSION["sesh"];?>;
let thisIdx=0;
let fed="<?php echo $fed;?>";
if (fed=="IPF") {
  month.style.display="none";
  month.parentElement.style.display="none";
  month.value=01;
  day.style.display="none";
  day.parentElement.style.display="none";
  day.value=01;

}

loadfromserver();

function loadfromserver() {
  fetch("./simpleLifter2/load.php").then(response => response.json()).then(json => {
    globalLifters=json;
    if (globalLifters!=[]) {
      lifters=[];
      console.log("loaded data");
      globalLifters.forEach(l => {if (l.session==globalSession) lifters.push(l)});
    }
  });

  setTimeout(function() {
  lifters.sort((a,b) => {return a.lot-b.lot});
  setLifter(0);
  
  lifters.forEach(e => {
    let a=document.createElement("option");
    a.value=e.lot;
    a.innerHTML=`#${e.lot} - ${e.name}`;
    ln.appendChild(a);
  })

  },200);

} //end function

function setLifter(idx) {

  if (idx>=lifters.length) idx=0;
  if (idx<0) idx=lifters.length-1;
  try { (lifters[idx]) } catch {idx=0;};
  thisIdx=idx;
  lifterName.value=lifters[idx].name || 'unnamed';
  lot.value=lifters[idx].lot || 999;

  //set weight class
  setWeightClass();

  //set year & division
  iwc.value=lifters[idx].wc || iwc.options[0].value;
  if (!lifters[idx].year) lifters[idx].year="1990-01-01";
  let y=lifters[idx].year.split('-');
  year.value=y[0] ||1990;
  month.value=y[1] ||01;
  day.value=y[2] ||01;
  gender.value=lifters[idx].gender ||"M";
  gear.value=(lifters[idx].gear||"CL");
  lifts.value=(lifters[idx].lifts||"PL");

  //disable the lifts they're not doing
  setLifts(lifters[idx].lifts);

  //just in case there's something there
  ibw.value=lifters[idx].bw || 0;
  sq1.value=lifters[idx].sq1 || 0;
  bp1.value=lifters[idx].bp1 || 0;
  dl1.value=lifters[idx].dl1 || 0;

  thisIdx=idx;
  ln.selectedIndex=idx;
  return 1;
} //end function

function nextLifter() {
  thisIdx+=1;
  if (!setLifter(thisIdx)) nextLifter();
  
} //end function

function lastLifter() {
  thisIdx-=1;
  if (!setLifter(thisIdx)) nextLifter();

} //end function

function setWeightClass() {
  iwc.innerHTML="";
  bw[lifters[thisIdx].gender || "M"].forEach(e => {
    let t=document.createElement("option");
    t.value=e;
    t.innerHTML=e+"kg";
    if (e==1000) t.innerHTML=bw[lifters[thisIdx].gender ||"M"][bw[lifters[thisIdx].gender||"M"].length-2]+"+kg";
    iwc.appendChild(t);
  });

}

document.addEventListener("change", e =>{
  e.target.style.background="#cfc"; //change the colour to note that it's changed

  if (e.target.id=="ibw") { //if the lifter BW has changed, make sure the weight class is set properly and colour it properly
    if (ibw.value-0>iwc.value) {ibw.style.background="#fcc"; return 1}
    if (iwc.selectedIndex!=0)
    if (ibw.value-0<=iwc[iwc.selectedIndex-1].value) {ibw.style.background="#fcc"; return -1;}
    ibw.style.background="#cfc";
  }

  if (e.target.id=="lifts") setLifts(e.target.value); //if they've changed the lifts, lock out the wrong ones




  if (e.target.id=="sq1" || e.target.id=="bp1" || e.target.id=="dl1") { //if they've changed or input an opening attempt, check that it's ok
    let messages=[];
    let w=parseFloat(e.target.value);
    if (w % 2.5) messages.push("Weight is not divisible by 2.5");
    if (w< 25 && w!=0) messages.push("Below the minimum weight");
    if (messages.length>0) {alert(messages.join(", "));e.target.style.background="#fcc"};
  }
  if (e.target.id=="ln") { //if the lifter name has changed in the scroller then set the next lifter
    setLifter(e.target.selectedIndex);
    ln.style.background="";
  }

  if (e.target.id=="gender") { //if they've changed the gender, change the weight class list to suit
    lifters[thisIdx].gender=e.target.value;
    iwc.dispatchEvent(new Event('change',{bubbles:true}));
    setWeightClass();
  }
}); //end change


document.addEventListener("click", e => {
  if (e.target.id=="submit") { //if we clicked on the submit button, do the fetch
    e.preventDefault();
    saveCurrent();
    nextLifter();
  }
})


function setLifts(l) { //enables / disables SQ,BP and DL inputs based on the lifters chosen lifts
  sq1.disabled=false;
  bp1.disabled=false;
  dl1.disabled=false;
  switch (l.toUpperCase()) {
  case "BP":
    sq1.dispatchEvent(new Event('change',{bubbles: true}));
    dl1.dispatchEvent(new Event('change',{bubbles: true}));
    sq1.value=0;sq1.disabled=true;
    dl1.value=0;dl1.disabled=true;
    break
  case "SQ":
    bp1.dispatchEvent(new Event('change',{bubbles: true}));
    dl1.dispatchEvent(new Event('change',{bubbles: true}));
    bp1.value=0;bp1.disabled=true;
    dl1.value=0;dl1.disabled=true;
    break
  case "DL":
    bp1.dispatchEvent(new Event('change',{bubbles: true}));
    sq1.dispatchEvent(new Event('change',{bubbles: true}));
    bp1.value=0;bp1.disabled=true;
    sq1.value=0;sq1.disabled=true;
    break
  case "PP":
    sq1.dispatchEvent(new Event('change',{bubbles: true}));
    sq1.value=0;sq1.disabled=true;
    break
  case "SB":
    dl1.dispatchEvent(new Event('change',{bubbles: true}));
    dl1.value=0;dl1.disabled=true;
    break
  case "SD":
    bp1.dispatchEvent(new Event('change',{bubbles: true}));
    bp1.value=0;bp1.disabled=true;
    break

  }

} //end function setLifts

function saveCurrent() { //save the lifter on pressing the submit button
    let a= document.querySelectorAll("form *");
    a.forEach(e => e.style.background=""); //clear the shading
    lifters[thisIdx].name=lifterName.value;
    lifters[thisIdx].lot=lot.value;
    lifters[thisIdx].bw=ibw.value;
    lifters[thisIdx].wc=iwc.value;
    lifters[thisIdx].sq1=sq1.value;
    lifters[thisIdx].bp1=bp1.value;
    lifters[thisIdx].dl1=dl1.value
    lifters[thisIdx].lifts=lifts.value;
    lifters[thisIdx].gender=gender.value.toUpperCase();
    //format the year properly
    let y=year.value;
    let m=month.value.toString().padStart(2,"0");
    let d=day.value.toString().padStart(2,"0");
    lifters[thisIdx].year=`${y}-${m}-${d}`;
    lifters[thisIdx].gear=gear.value.toUpperCase();
    lifters[thisIdx].division=(gender.value+"-"+gear.value+"-"+lifts.value).toUpperCase();
    l=JSON.stringify(lifters[thisIdx]);

      fetch("./simpleLifter2/save.php", {
        method: 'POST',
        headers: {
        'Accept': 'application/json, text/plain',
        'Content-Type': 'application/json'
      },
      body: l
    }).then(res => res.text())
    .then(res => {console.log(res);});
}



</script>
</html>

