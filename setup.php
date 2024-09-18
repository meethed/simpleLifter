<?php include_once("./includes/config.inc");
if (!isset($_SESSION["compLetters"])) {header("Location: index.php");};
$sql = "select seshs,fed,compName from comp where compLetters='".$_SESSION["compLetters"]."'";
$result=$conn->query($sql)->fetch_assoc();

$dsp=explode(",",$result["seshs"]);
foreach ($dsp as $d) {
if (substr($d,0,1) > $maxd) {$maxd=(int) substr($d,0,1);};
if (substr($d,1,1) > $maxp) {$maxp=(int) substr($d,1,1);};
if (substr($d,2,1) > $maxs) {$maxs=(int) substr($d,2,1);};
}
if (!isset($_GET["c"])) {$c=$result["fed"];}else{$c=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);};
?>

<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body onbeforeunload="saveMe();">
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">
<div class="heading">Competition Setup - <?php echo $_SESSION["compName"];?></div>
<div class="setupbox" id="configbox">
  <div class="heading">Competition parameters</div>
  <label for "inFed">International ruleset: <select name="inFed" id="inFed" onchange="changeFed()">
  <?php
  foreach (glob("./simpleLifter2/feds/*.js") as $f) {
    echo "<option value='".substr(basename($f),0,3)."'>".substr(basename($f),0,3)."</option>";
  }

  ?>

  </select></label>
  <?php if ($_SESSION["sheet"]==1) { ?>
    <h1>Tech Desk Spreadsheet options</h1>
    <label for="auto">Auto referee (good/no lift based on referee lights)?<input type="checkbox" name="auto" id="auto" onchange="saveConfig();"></label>
    <label for="countup">Progress groups from A-Z (off is from Z-A)<input type="checkbox" name="countup" id="countup" onchange="saveConfig();"></label>
    <label for="showlights">Show the lights & timers box (top right)<input type="checkbox" name="showlights" id="showlights" onchange="saveConfig();"></label>
    <label for="autobreaks">Automatic break timer? (20 mins if 1 flight, 10 minutes multiple flights)<input type="checkbox" name="autobreaks" id="autobreaks" onchange="saveConfig();"></label>
    <label for="simplelights">Simplified referee lights? (Red and White only)<input type="checkbox" name="simplelights" id="simplelights" onchange="saveConfig();"></label>
    <label for="sqw">Bar + Collars for Squat<input type="number" name="sqw" id="sqw" onchange="saveConfig();" value="25"></label>
    <label for="bpw">Bar + Collars for Bench Press<input type="number" name="bpw" id="bpw" onchange="saveConfig();" value="25"></label>
    <label for="dlw">Bar + Collars for Deadlift<input type="number" name="dlw" id="dlw" onchange="saveConfig();" value="25"></label>  <?php }?>

</div>
<div class="setupbox">
  <div class="heading">Session management</div>
  <p>Click on a session name to delete it. Session starting time should be in local time using the DD-MM-YY HH:mm or YYYY-MM-DD HH:mm formats, and will facilitate a countdown timer on the displays. It will refresh to show UTC time in the YYYY-MM-DD HH:mm format. The youtube link is optional but will facilitate viewers to watch the youtube stream and competition results live.</p>
  <p>Note: you can't delete the first session!</p>
  <div id="tblSession" class="setup-table">
    <div class="tr">
    <div class="th">Session Name</div>
    <div class="th">Start Time</div>
    <div class="th">Youtube livestream link</div>
  </div>
  <?php
  $sql = "select * from compstatus where compLetters='".$_SESSION["compLetters"]."' order by session";
  $result=$conn->query($sql)->fetch_all(MYSQLI_ASSOC);

  foreach ($result as $r) { 
    $d="";
    if ($maxd>1) {$d.="Day ".substr($r["session"],0,1);}
    if ($maxp>1) {$d.=" Platform ".substr($r["session"],1,1);}
    if ($maxs>1) {$d.=" Session ".substr($r["session"],2,1);}

    echo "<div class='tr'>";
    echo "<div class='td session-name' id='s".$r["session"]."'>".$_SESSION["compName"]." - " .$d."</div>";
    echo "<div class='td session-time' contenteditable>".$r["timeThree"]."</div>";
    echo "<div class='td session-URL' contenteditable>".$r["streamURL"]."</div>";
    echo "</div>";
  }
  ?>
  </div>
  </div>

<div class="setupbox">
  <div class="heading"><span onclick="saveMe();">💾</span> Lifter Enrolment<span id="savestatus" style="float:right">Unsaved changes</span></div>
  <fieldset><legend>Import CSV</legend>
  <div>
  <input type="file" id="importFile" style="display:block;padding-bottom:5px;"></input>
  <a href="./uploads/simpleLifter Importer.csv">Click here for the CSV template</a>
  </div>
  <div class="btn smol-btn" id="import" onclick="importData(0)">Import & Add</div><div class="btn smol-btn" onclick="importData(1)">Clear all & import</div>
</fieldset>

  <fieldset id="validdescriptions"><legend>NOTE you can only enter Gender, Equipment and Lifts using the following values:</legend>
    <div class="setup-valid-entry" id="genders">Valid Genders are:<br>M - Male<br>F - Female<br>X - Mx <br></div>
    <div class="setup-valid-entry" id="gears">Valid equipment for this comp is:<div id="gearlist"></div></div>
    <div class="setup-valid-entry" id="lifts">Valid lifts for this comp are:<div id="liftslist"></div></div>
    <div class="setup-valid-entry" id="wc">Valid weight classes for this comp are:<div id="weightslist"></div></div>
    Custom values can be used, and even if the fail validation in the table below they will still work. They will result in the lifter being placed in a separate category for results management - note they will not work for IPF points. Only 1 (gender) or 2 (equipment/lifts) characters can be used. Only valid entries will be expanded correctly on the livestream.
  </fieldset>
  <fieldset><legend>Table Options</legend><div class="btn smol-btn" id="randombygp" onclick="randomise(1);">Randomise lot number (by group)</div><div class="btn smol-btn" id="randombyall" onclick="randomise(0);">Randomise lot number (by all)</div></fieldset>
  <div id="setuptable" class="setup-table">
    <div class="tr">
      <div class="th">X</div>
      <div class="th">Lifter Name</div>
      <div class="th">Lot Number</div>
      <div class="th">Year/DOB</div>
      <div class="th">Flight</div>
      <div class="th">Gender</div>
      <div class="th">Equipment</div>
      <div class="th">Lifts</div>
      <div class="th">WC</div>
      <div class="th">PB Squat</div>
      <div class="th">PB Bench</div>
      <div class="th">PB Dead</div>
      <div class="th">PB Total</div>
      <?php foreach ($result as $r) {
      echo "<div class='th'>".$r["session"]."</div>";
      } ?> 
    </div>
  <div id="newRow" class="new-row"> + New Row </div>
  </div>
</div>

<div class="setupbox">
  <div class="heading">Livestream config</div>
  <p>To set up multiple days/sessions/platforms without having to change OBS (ie: copy and paste so you can set and forget), you need to choose which platforms or sessions you will be streaming from each computer / setup. The filter applies to platforms, as you can't run concurrent sessions on the same platform!</p>
  <p>If there's only one platform then you don't need a filter and simpleLifter will just pull the active data without any extra work!!</p>
  <p> eg. If you have 2 platforms and one computer per platform, then the filter will choose platform 1 for one stream and platform 2 for the other, and simpleLifter will know which session is currently active.</p>
  <ul>
  <li>For a single platform: <a href="./multiStream.php?c=<?php echo $_SESSION["compLetters"];?>">multiStream.php?c=<?php echo $_SESSION["compLetters"];?></a></li>
  <li>For a 2 platform competition:
  <ol>
  <li>Platform 1: <a href="./multiStream.php?c=<?php echo $_SESSION["compLetters"];?>&f=1">multiStream.php?c=<?php echo $_SESSION["compLetters"];?>&f=1</a></li>
  <li>Platform 2: <a href="./multiStream.php?c=<?php echo $_SESSION["compLetters"];?>&f=2">multiStream.php?c=<?php echo $_SESSION["compLetters"];?>&f=2</a></li>
  </ol>
  </ul>
</div>




<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->



<!-- templates for cloning -->
<div id="templates" style="display:none">
<div class="tr setup-row" id="lifterrowtemplate">
<div class="td deleteme" onclick="deleteLifter(this);">&#10008;</div>
<div class="td" data-field="name" contenteditable>Lifter Name</div>
<div class="td" data-field="lot" contenteditable>Lot No</div>
<div class="td" data-field="year" contenteditable>1990</div>
<div class="td" data-field="gp" contenteditable>A</div>
<div class="td" data-field="gender" contenteditable>F</div>
<div class="td" data-field="gear" contenteditable>CL</div>
<div class="td" data-field="lifts" contenteditable>PL</div>
<div class="td" data-field="wc" contenteditable>57</div>
<div class="td" data-field="pbs" contenteditable>100</div>
<div class="td" data-field="pbb" contenteditable>100</div>
<div class="td" data-field="pbd" contenteditable>100</div>
<div class="td" data-field="pbt" contenteditable>100</div>
<?php
foreach ($result as $r) {
$s=$r["session"];
  ?>
  <div class="td" id="s<?php echo $r["session"]; ?>"></div>
<?php
}
?>
</div>
</div>

</body>
<script src="./simpleLifter2/feds/<?php echo $c;?>rules.js"></script>
<script>
let oldcell="",
 cL="<?php echo $_SESSION["compLetters"]; ?>",
 maxp=<?php echo $maxp; ?>,
 sessions=[<?php echo $_SESSION["seshs"]; ?>],
 lifterdata=[],
 newLifterData=[],
 mousedown=0;
 loaddata();

const cols={"del":0,"name":1,"lot":2,"year":3,"gp":4,"gender":5,"gear":6,"lifts":7,"wc":8,"pbs":9,"pbb":10,"pbd":11,"pbt":12,"length":13};

function loaddata() {
  if (lifterdata.length>0)
   while (setuptable.children.length>2) {
     setuptable.removeChild(setuptable.children[1]) 
   }
  fetch("./simpleLifter2/load.php").then(response => response.json()).then(data => {
  lifterdata=data;
  lifterdata.forEach((e,i) => { //iterate through all the lifters currently in the system
    let a=document.getElementById("lifterrowtemplate").cloneNode(true);
    a.id="lifter"+i;
    a.childNodes.forEach(e => e.innerHTML="");
    a.dataset.idx=e.idx;
    a.children[0].innerHTML='\u2718';

    for (const [key, value] of Object.entries(cols)) {

      if (value && key!="length") a.children[value].innerHTML=e[key];
    }
    if (e.wc==1000) a.children[cols.wc].innerHTML=bw[e.gender].slice(-2)[0]+"+";
    if (e.year&&e.year.indexOf("-12-31")) a.children[cols.year].innerHTML=e.year.slice(0,4);

    //get the session the lifter is in
    let offset=sessions.findIndex(s => s==e.session);
    if (offset>=0) a.children[cols.length+offset].innerHTML="X";
    document.getElementById("setuptable").insertBefore(a,newRow);
  }) //next foreach

  //stuff to do once every row is added
  }); //end of fetch

  //tidy up the start times

  st=document.querySelectorAll(".session-time");
  let sd="<?php echo $_SESSION["startDate"];?>";
  st.forEach(e => {
  let utc=new Date(e.innerHTML).valueOf();
  if (isNaN(utc)) 
    e.innerHTML=sd + " 08:00:00";
    e.focus();
  });
  document.body.focus();

//load the sheet config bit
<?php if ($_SESSION["sheet"]==1) { ?>
fetch("./users/"+cL+".json").then(response => response.json()).then(data => {
  config=data;
  for (const [key,value] of Object.entries(config)) {
    let a=document.querySelector(`#${key}`);
    if (a) {
      if (key==a.id && a.type=="checkbox") a.checked=value;
      if (key==a.id && a.type!="checkbox") a.value=parseFloat(value);
    }
  }
});
<?php }; ?>

inFed.selectedIndex=Array.from(inFed.options).map(e=>e.value).findIndex(e=>e=="<?php echo $c;?>");

//setup the valid data
liftslist.innerHTML="";
gearlist.innerHTML="";
weightslist.innerHTML="";
allowedLifts.forEach(e => liftslist.innerHTML+=e+"<br>");
allowedEquipment.forEach(e =>gearlist.innerHTML+=e+"<br>");
Object.entries(bw).forEach(e => {
weightslist.innerHTML+="<br>"+e[0]+": ";
bw[e[0]].forEach(i=>weightslist.innerHTML+= (i!=1000) ? i+", " : e[1][e[1].length-2]+"+");
})

} //end fucntion loaddata


//keydown listener
document.addEventListener("keydown", e=> {
if (e.target.id=="") {};

});

//click listeners
newRow.addEventListener("click", e=> { //new lifter
  addLifter();
});

tblSession.addEventListener("click", e=> { //session management table
  if (e.target.classList.contains("session-name"))
    if (e.target.id.slice(1)!="111") {
    fetch("deletesesh.php?s="+e.target.id.slice(1));
    e.target.parentElement.parentElement.removeChild(e.target.parentElement) ;
    location.reload();
  } else { alert("You can't delete the first session!");}
});

setuptable.addEventListener("click", e=>{
  oldcell=e.target.innerHTML;
});


//session table changers
tblSession.addEventListener("focusout", e=> { //session management table
  if (e.target.classList.contains("session-time")) {

   e.target.innerHTML = e.target.innerHTML.replace(/(&nbsp;|<([^>]+)>)/ig, "");
    let utc=new Date(e.target.innerHTML).toISOString();
    utc=utc.slice(0,10)+" "+utc.slice(12,18);
    fetch("updatestatus.php?s="+e.target.parentElement.children[0].id.slice(1)+"&start="+utc).then(response=>response.text()).then(data=>{});
//    location.reload();
  };
  if (e.target.classList.contains("session-URL")) {
    fetch("updatestatus.php?s="+e.target.parentElement.children[0].id.slice(1)+"&url="+e.target.innerHTML).then(response=>response.text()).then(data=>{});
//    location.reload();

  }
});


//drag listeners (for the session)
setuptable.addEventListener("mousedown", e => {
  if (e.target.id[0]=="s" && e.target.innerHTML=="X") {e.target.innerHTML=""; mousedown=-1; return 1}
  if (e.target.id[0]=="s" && e.target.innerHTML=="") {mousedown=1; e.target.parentElement.querySelectorAll("[id^=s]").forEach(e => e.innerHTML=""); e.target.innerHTML="X"; return 0}
  savestatus.innerHTML="Unsaved changes";
});

setuptable.addEventListener("mouseup", e => {
   mousedown=false;
});

setuptable.addEventListener("mouseover", e => {
  if (mousedown!=0) {
    if (e.target.id[0]=="s" && mousedown==-1) {e.preventDefault(); e.target.innerHTML=""; return 0}
    if (e.target.id[0]=="s" && mousedown==1) {e.preventDefault(); e.target.parentElement.querySelectorAll("[id^=s]").forEach(e => e.innerHTML="");e.target.innerHTML="X"; return 1}
  }
})

// make the setup table work like excel
setuptable.addEventListener("keydown", e => {
  switch (e.key) {
    case "Enter":
  if (Array.from(e.target.parentNode.parentNode.children).indexOf(e.target.parentNode)+1==e.target.parentNode.parentNode.children.length-1)
      addLifter();
    case "ArrowDown":
      e.preventDefault();
      move(e.target,"d");
      break;
 case "Tab":
      e.preventDefault();
      x= e.shiftKey ? move(e.target,"l") : move(e.target,"r");
      break;
    case "ArrowRight":
      e.preventDefault();
      move(e.target,"r");
      break;
    case "ArrowLeft":
      e.preventDefault();
      move(e.target,"l");
      break;
    case "ArrowUp":
      e.preventDefault();
      move(e.target,"u");
      break;
    default:
  }
});

function move(t,d) { //move the selection from the [t]arget in the [d]irection
  if (t.innerHTML!=oldcell) {
  t.innerHTML= t.innerHTML.replace(/(&nbsp;|<([^>]+)>)/ig, "");
  if (t.dataset.field=="name" && t.parentElement.children[cols.pbs].innerHTML+t.parentElement.children[cols.pbb].innerHTML+t.parentElement.children[cols.pbd].innerHTML+t.parentElement.children[cols.pbt].innerHTML==0) getPB(Array.from(t.parentNode.parentNode.children).indexOf(t.parentNode));
  validateStuff();
  savestatus.innerHTML="Changes made";
  }
  let c=0,r=0;
  switch (d) {
    case "u": r=-1;break;
    case "d": r=1; break;
    case "l": c=-1;break;
    case "r": c=1; break;
  }
  let col=Array.from(t.parentNode.children).indexOf(t)+c;
  let row=Array.from(t.parentNode.parentNode.children).indexOf(t.parentNode)+r;
  if (row==setuptable.children.length-1) {row=1}
  if (row==0) row=setuptable.children.length-1;
  if (col<0) col=setuptable.children[1].children.length-1;
  if (col==setuptable.children[1].children.length) col=0;
  //recursion lulz
  let newt=setuptable.children[row].children[col];
  if (newt.contentEditable!="true") {move(newt,d);}
  //select and focus
  let rg=document.createRange();
  rg.selectNodeContents(newt);
  let sl=window.getSelection();
  sl.removeAllRanges();
  sl.addRange(rg);
  newt.focus();
  oldcell=newt.innerHTML;
  saveMe();
} //end function move

function saveUpdate() { //saves everything. It's less than ideal but names and things might change

    fetch("setuplifters.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(lifterdata)
    }).then(response=>response.text()).then(data => {console.log(data)});
} //end function saveUpdate


function addLifter() {
  fetch("newlifter.php").then(response=>response.json()).then(data => { //add new liftre
    let a=document.getElementById("lifterrowtemplate").cloneNode(true);
    a.dataset.idx=data.idx;
    a.querySelectorAll("*").forEach(e => e.innerHTML="");
    a.children[cols.length].innerHTML="X";
    a.children[0].innerHTML="&#10008";
    a.children[cols.name].innerHTML="Lifter Name";
    a.children[cols.lot].innerHTML=setuptable.childElementCount-1;
    a.children[cols.year].innerHTML=1990;
    a.children[cols.gp].innerHTML="A";
    a.children[cols.gender].innerHTML="F";
    a.children[cols.gear].innerHTML=allowedEquipment[0];
    a.children[cols.lifts].innerHTML=allowedLifts[0];
    a.children[cols.wc].innerHTML=bw.F[0];
    document.getElementById("setuptable").insertBefore(a,newRow);
    saveMe();
  });
} //end function addlifter

function getPB(lifter) { //lifter in this instance is a number that refers to the entry in the setuptable array

  if (lifter) {
  setuptable.children[lifter].children[cols.pbs].style.background="#ccc";
  setuptable.children[lifter].children[cols.pbb].style.background="#ccc";
  setuptable.children[lifter].children[cols.pbd].style.background="#ccc";
  setuptable.children[lifter].children[cols.pbt].style.background="#ccc";
  opl=setuptable.children[lifter].children[1].innerHTML;
  opl = opl.replace(/\s+/g, '').toLowerCase();
  fetch("getpb.php?n="+opl).then(response=>response.json()).then(data => {
  setuptable.children[lifter].children[cols.pbs].innerHTML=data.pbs;
  setuptable.children[lifter].children[cols.pbb].innerHTML=data.pbb;
  setuptable.children[lifter].children[cols.pbd].innerHTML=data.pbd;
  setuptable.children[lifter].children[cols.pbt].innerHTML=data.pbt;
  setuptable.children[lifter].children[cols.pbs].style.background="";
  setuptable.children[lifter].children[cols.pbb].style.background="";
  setuptable.children[lifter].children[cols.pbd].style.background="";
  setuptable.children[lifter].children[cols.pbt].style.background="";

  })
  }
  else {
    let l=setuptable.children.length-1;

    for (i=1;i<l;i++) {
    fetch("getpb.php?n="+opl).then(response=>response.json()).then(data => {

    });
    }
  }
} //end function getPB

function saveMe() {
  validateStuff();
  saveUpdate();
  savestatus.innerHTML="Saved";
} //end function saveme

function saveConfig() {
  config={};
  let a=document.querySelectorAll("#configbox input");
  a.forEach(e => {
    if (e.type=="checkbox") {config[e.id]=e.checked} else {config[e.id]=parseFloat(e.value)}
    })


  l = JSON.stringify(config);
  fetch("./simpleLifter2/saveconfig.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',
      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => {
  });
} //end function config

function changeFed() {
  newFed=inFed.options[inFed.selectedIndex].value;
  saveMe();
  saveConfig();
  fetch("./changeFed.php?c="+newFed).then(res=>res.text()).then(data=> window.location="setup.php");

} //end function changeFed

function validateStuff() {

  //first part of this function is to clone the divs into the array
  let l=setuptable.children.length-1; //exclude the header (by starting at 1) and new row rows
  lifterdata=[];
  for (i=1;i<l;i++) { //loop through each row
    let lifter={};
    let c=cols.length; //hardcoded beacause of the session boxes NO BAD
    for (v=1;v<c;v++) {
      e=setuptable.children[i].children[v];

      // if it needs to be in uppercase
      if ([cols.gp,cols.gender,cols.gear,cols.lifts].includes(v)) e.innerHTML=e.innerHTML.toUpperCase();

      //character limits
      if ([cols.gp,cols.gender].includes(v)) e.innerHTML=e.innerHTML[0];
      if ([cols.lifts,cols.gear].includes(v)) e.innerHTML=e.innerHTML.slice(0,2);
      

      //if it needs to be a float
      if ([cols.pbs,cols.pbb,cols.pbd,cols.pbt].includes(v)) e.innerHTML=parseFloat(e.innerHTML) ||0;

      //if it's a super heavy weight
      if (v==cols.wc) if (e.innerHTML.slice(-1)=="+") {e.innerHTML=(bw[lifter.gender][bw[lifter.gender].length-2])+"+"} else {e.innerHTML=parseFloat(e.innerHTML) || bw[lifter.gender||"M"][0]};

      //if it is lot number
      if (v==cols.lot) e.innerHTML=parseInt(e.innerHTML) || 0;

      //put this into the lifter array
      lifter[e.dataset.field]=e.innerHTML;
      if ([cols.lot,cols.pbs,cols.pbb,cols.pbd,cols.pbt].includes(v)) lifter[e.dataset.field]=parseFloat(e.innerHTML);
      if (v==cols.wc && e.innerHTML.slice(-1)=="+") lifter[e.dataset.field]=1000;
      //if it's a year
      if (v==cols.year && lifter[e.dataset.field].length==4) lifter[e.dataset.field]+="-12-31";



    }; //end for each column
    //find the session
    for (v=cols.length;v<setuptable.children[i].children.length;v++)
     if (setuptable.children[i].children[v].innerHTML=="X") {
       session=setuptable.children[0].children[v].innerHTML;
       lifter["session"]=session;
     } //got the session
    lifter["idx"]=parseInt(setuptable.children[i].dataset.idx); //lifter index
    lifter.division=`${lifter.gender}-${lifter.gear}-${lifter.lifts}`;
    lifterdata.push(lifter);
  } //next lifter

  document.querySelectorAll(".invalid").forEach(e=>e.classList.remove("invalid"));
  //validate weight classes & genders
  if (!Array.isArray(allowedEquipment) && !allowedEquipment.length) allowedEquipment=["CL","EQ"];
  if (!Array.isArray(allowedLifts) && !allowedLifts.length) allowedLifts=["PL","BP","SQ","DL","PP","SB","SD"];
  lifterdata.forEach((e,i) => {
    e.wc=parseFloat(e.wc);
    if (bw[e.gender].indexOf(e.wc)==-1) {setuptable.children[i+1].children[cols.wc].classList.add("invalid")};
    if (allowedEquipment.indexOf(e.gear)==-1) {setuptable.children[i+1].children[cols.gear].classList.add("invalid")};
    if (allowedLifts.indexOf(e.lifts)==-1) {setuptable.children[i+1].children[cols.lifts].classList.add("invalid")};

  });
  //lifts
  //TODO
} //end function validateStuff

function deleteLifter(l) {
  let lifter=l.parentElement.dataset.idx; //get the row
  fetch("./deleteLifter.php?i="+lifter).then(res=>res.text()).then(data=> loaddata());
} //en function delete lifter


function randomise(byGp) {
validateStuff();
let newlots=lifterdata;
lifterdata.forEach((e,i) => e.lot=i+1);
if (!byGp) {
  newlots=shuffle(lifterdata);
  lifterdata.forEach((e,i) => {e.lot=newlots[i].lot;setuptable.children[i+1].children[cols.lot].innerHTML=e.lot;});

}

if (byGp) {
  lifterdata.forEach((e,i) => {e.lot=i+1;e.tabidx=i+1;});
  lifterdata=shuffle(lifterdata);
  if (countup.value=="on") {lifterdata.sort((a,b) => {v=(a.gp>b.gp) ? 1 : (a.gp<b.gp) ? -1 : 0; return v;})};
  if (countup.value!="on") {lifterdata.sort((a,b) => {v=(a.gp<b.gp) ? 1 : (a.gp>b.gp) ? -1 : 0; return v;})};
  lifterdata.forEach((e,i) => {
    e.lot=i+1;
    setuptable.children[e.tabidx].children[cols.lot].innerHTML=e.lot;
  })
}

} //end function randomise

function shuffle(array) {
  let currentIndex = array.length,  randomIndex;

  // While there remain elements to shuffle.
  while (currentIndex > 0) {

    // Pick a remaining element.
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex--;

    // And swap it with the current element.
    [array[currentIndex], array[randomIndex]] = [
      array[randomIndex], array[currentIndex]];
  }

  return array;
} //end function shuffle

function importData(n) {
  let errors=[];
  const file = document.getElementById("importFile").files[0];
  if (!file ) errors.push("No file selected! You must choose a file by pressing 'browse' to the left of this box. No changes have been made.");
  if (newLifterData.length<=0) errors.push("Nothing to import. Please choose a valid file");

  if (errors.length>0) {
    errors.forEach(e => alert(e));
    return
  } else {
    if (n==1) { // delete first
      document.querySelectorAll("#setuptable .setup-row").forEach(e => e.remove());
      fetch("./deleteLifter.php?i=-1").then(res=>res.text()).then(data=>console.log(data));
      lifterdata=[...newLifterData];
    }
    
    newLifterData.forEach(e => { //for each row
      fetch("newlifter.php").then(response=>response.json()).then(data => { //add new liftre
        let a=document.getElementById("lifterrowtemplate").cloneNode(true);
        a.dataset.idx=data.idx;
        a.querySelectorAll("*").forEach(e => e.innerHTML="");
        let seshOffset=sessions.indexOf(e.session);
        a.children[cols.length+seshOffset].innerHTML="X";
        a.children[0].innerHTML="&#10008";

         for (const [key, value] of Object.entries(cols)) {
           if (value && key!="length") a.children[value].innerHTML=e[key];
         }
        document.getElementById("setuptable").insertBefore(a,newRow);
      }); //end within the fetch
    }); //end the foreach

  } //end the if no errors

  setTimeout(checkPBs,1000);

} //end function importData

function checkPBs() {
  document.querySelectorAll(".setup-row").forEach(e => {
    let pb=parseInt(e.children[cols.pbs].innerHTML)||0;
    pb+=parseInt(e.children[cols.pbb].innerHTML)||0;
    pb+=parseInt(e.children[cols.pbd].innerHTML)||0;
    pb+=parseInt(e.children[cols.pbt].innerHTML)||0;
  
    if (!pb) {getPB([...e.parentNode.children].indexOf(e));}
  });
  setTimeout(saveMe,1000);
} //end function checkPBs


document.querySelector("#importFile").addEventListener("change", (e) => {
  const f=e.target.files[0];
  if (f.name.split(".").pop()=="csv") {

  //parse here but don't actually add it to the competition

   importFile.files[0].text().then(e => {
    console.log(e); //dump it into the console
    let csv=[];
    let lines=[];
    lines=e.split("\n");
    lines.forEach(l => {csv.push(l.split(","))});
    console.log(csv);

    let na=csv[0].indexOf("Lifter Name");
    let lo=csv[0].indexOf("Lot Number");
    let fl=csv[0].indexOf("Flight");
    let ge=csv[0].indexOf("Gender");
    let eq=csv[0].indexOf("Equipment");
    let li=csv[0].indexOf("Lifts");
    let wc=csv[0].indexOf("WC");
    let ps=csv[0].indexOf("pbs");
    let pb=csv[0].indexOf("pbb");
    let pd=csv[0].indexOf("pbd");
    let pt=csv[0].indexOf("pbt");
    let se=csv[0].indexOf("Session");
    let yr=csv[0].indexOf("DOB");
    newLifterData=[];
    let i=0;
    csv.shift();
    csv.forEach(l => { //remove the header row
      if (l=="") return;
      newLifterData[i]={};
      newLifterData[i].name=l[na];
      newLifterData[i].lot=parseInt(l[lo]);
      newLifterData[i].gp=l[fl][0].toUpperCase();
      newLifterData[i].gender=l[ge][0].toUpperCase();
      newLifterData[i].gear=l[eq].toUpperCase();
      newLifterData[i].lifts=l[li].toUpperCase();
      if (l[yr].indexOf("-12-31")) l[yr]=l[yr].slice(0,4);
      newLifterData[i].year=l[yr];
      //IPF filter
      if (l[wc][l[wc].length-1]=="+") l[wc]=1000;
      newLifterData[i].wc=parseFloat(l[wc]);
      newLifterData[i].pbs=parseFloat(l[ps]);
      newLifterData[i].pbb=parseFloat(l[pb]);
      newLifterData[i].pbd=parseFloat(l[pd]);
      newLifterData[i].pbt=parseFloat(l[pt]);
      newLifterData[i].session=parseInt(l[se]);
    i++;
    });

});

  } else {
  alert ("the file type must be CSV. Please choose the correct file");
  document.querySelector("#importFile").value=null;
  }
});




</script>
</html>

