<?php include_once("./includes/config.inc"); ?>
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


<div class="heading">Add new competition</div>

<form id="frmNewComp" action="registercomp.php" method="POST">
  <h1>Competition registration</h1>
  <label for="frmName">Comp Name:<input class="frmInput" id="frmName" name="frmName" type="text" placeholder="Competition Name" required=""></label>
  <label for="frmFed">Federation:<select class="frmInput" id="frmFed" name="frmFed" placeholder="IPF" maxlength="3"><option value="IPF">IPF</option><option value="IPL">IPL/USPA</option><option value="WDF">WDFPF</option><option value="APL">APL</option></select></label>
  <label class="frmInputLab" for="frmStart">Start Date: <input id="frmStart" class="frmInputDate" name="frmStart" type="date"></label>
  <label class="frmInputLab" for="frmDays"><input id="frmDays" class="frmInputNum" type=number min=1 max=9 value=1 name="frmDays"> days</label>
  <label class="frmInputLab" for="frmSessions"><input id="frmSessions" class="frmInputNum" type=number min=1 max=9 value=1 name="frmSessions"> sessions</label>
  <label class="frmInputLab" for="frmPlatforms"><input id="frmPlatforms" class="frmInputNum" type=number min=1 max=9 value=1 name="frmPlatforms"> platforms</label>
  <div class="sessionbox" id="sessionBox"></div>
  <input id="frmSesh" name="frmSesh" style="display: none" type="text">
  <label for="frmPWD">Access Code:<input id="frmPWD" class="frmInput" name="frmPWD" type="password" placeholder="Access Code" required=""><label>
  <label for="frmContact">Email Address:<input id="frmContact" class="frmInput" name="frmContact" type="text" placeholder="Contact Email" required=""></label>
  <div class="frmBox">
    <h1>Feature selection</h1>
    <h2>What do you need enabled?</h2>
    <label class="no-pad" for="frmLights"><input id="frmLights" name="frmLights" type="checkbox">Lights and Timers</label>
    <label class="no-pad" for="frmSheet"><input id="frmSheet" name="frmSheet" type="checkbox">Spreadsheet and Results</label>
  </div>
  <button class="frmInput btn" type="submit">Submit</button>

</form>

<br><br><br>





<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script>
var compName="";
var sessions=[];
var startDate=new Date();
document.getElementById("frmName").addEventListener("change",changeName);
document.getElementById("frmStart").addEventListener("change",changeDate);
document.getElementById("frmSessions").addEventListener("change",changeSesh);
document.getElementById("frmDays").addEventListener("change",changeSesh);
document.getElementById("frmPlatforms").addEventListener("change",changeSesh);

document.getElementById("frmStart").valueAsDate= new Date();
changeSesh();
changeDate();
function changeName() {
compName=document.getElementById("frmName").value;
updateSesh();
} //end function change Name

function changeDate() {
startDate = document.getElementById("frmStart").valueAsDate;
updateSesh();
} //end function change date

function changeSesh() {
  var seshs=document.getElementById("frmSessions").value;
  var days=document.getElementById("frmDays").value;
  var platforms=document.getElementById("frmPlatforms").value;

//clear sessions
sessions=[];
document.getElementById("sessionBox").innerHTML="";;
c=0
for (d=1;d<=days;d++)
  for (s=1;s<=seshs;s++) 
 for (p=1;p<=platforms;p++) {
    sessions[c]=""+d+s+p;
    c++;
  }

updateSesh();
} //end function change sesh


function updateSesh() {
  let cn=document.getElementById("frmName").value;
  var seshs=document.getElementById("frmSessions").value;
  var days=document.getElementById("frmDays").value;
  var platforms=document.getElementById("frmPlatforms").value;
  document.getElementById("sessionBox").innerHTML="";
  sessions.forEach((c,i) => {
    let dsp="";
    if (days>1) dsp="Day "+c[0];
    if (seshs>1) dsp+=" Session "+c[1];
    if (platforms>1) dsp+=" Platform "+c[2];

    let d=document.createElement("div");
    var da=new Date(startDate);
    da.setDate(da.getDate()+parseInt(c[0])-1); 
   d.innerHTML= da.toISOString().slice(0,10) +" - " +cn+" "+ dsp;
     d.classList.add("sessionchoice");
    d.dataset.id=i;
     d.addEventListener("click",e => {
       var del = e.target.dataset.id;
       sessions.splice(del,1);
       updateSesh();
     });
    document.getElementById("sessionBox").appendChild(d);
  });
  document.getElementById("frmSesh").value=sessions.toString();
} //end function update sesh
</script>
</html>

