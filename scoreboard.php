<?php
include_once("./includes/config.inc");
$cL=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
$f=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);
$sql = "select startdate,fed, compName,seshs from comp where compLetters = '".$cL."'";
$result=$conn->query($sql)->fetch_assoc();
if ($result["seshs"]=="111") {$f=0;};
$fed=$result["fed"];
$cN=$result["compName"];
$startDate=$result["startdate"];?>
<!DOCTYPE HTML>
<html>
<head>

<title>simpleLifter Scoreboard</title>
<link rel="stylesheet" href="./resources/scoreboard.css">
<meta name="robots" content="noindex">
</head>
<body>
<table id="table">
<tr></tr>
</table>
</body>
<script src="./simpleLifter2/feds/<?php echo $fed;?>rules.js"></script>

<script>
let headings={"gp":"Group","lot":"Lot","name":"Lifter Name","team":"Team","agediv":"Age Group","bw":"Weight","wc":"Weight Class","division":"Division","sq1":"SQ1","sq2":"SQ2","sq3":"SQ3","bsq":"Best SQ","bp1":"BP1","bp2":"BP2","bp3":"BP3","bbp":"Best Bench","dl1":"DL1","dl2":"DL2","dl3":"DL3","bdl":"Best Dead","total":"Total","teampoints":"Points","session":"Session","pt":"Points","place":"Place","st":"Subtotal"};
let lifterData=[];
let compStatus=[];
getData();

tick=setInterval(getData,5000);

function getData() {
  fetch("./simpleLifter2/load.php?c=<?php echo $cL; if ($f>0) {echo "&f=".$f;}; ?>").then(response=>response.json().then(d=>{
    lifterData=d;
    fetch("./simpleLifter2/loadsetup.php?c=<?php echo $cL; if ($f>0) {echo "&f=".$f;}; ?>").then(response=>response.json().then(s=>{
      compStatus=s;
      draw();
    }));
  }));
}
function draw() {
  table.innerHTML="";
  hr=document.createElement("tr");
  table.appendChild(hr);
  for (const [key, value] of Object.entries(lifterData[0])) {
    let th=document.createElement("th");
    th.innerHTML=headings[key] || key;
    th.classList=key;
    hr.appendChild(th); 
  };
  lifterData.forEach((e,i) => {
    let lr=document.createElement("tr");
    for (const [key, value] of Object.entries(e)) {
      let td=document.createElement("td");
      td.innerHTML=value;
      if (key=="total" && value==-1) td.innerHTML="DSQ";
      td.id=key+"lifter"+i;
      if (key=="wc" && value==1000) td.innerHTML=bw[e.gender].slice(-2)[0]+"+";
      td.classList=key;
      if (key=="liftidx") lr.dataset.liftidx=value;
      lr.appendChild(td);
      if (key=="idx" && compStatus.activeLifter==value)
       lr.classList.add("al"); 
    };
    table.appendChild(lr);
  });

  lifterData.forEach((e,i) => {
    for (const [key, value] of Object.entries(e)) {
    if (["sa1","sa2","sa3","ba1","ba2","ba3","da1","da2","da3"].includes(key)) {
      let c="";
      if (key[0]=="s") c="sq";
      if (key[0]=="b") c="bp";
      if (key[0]=="d") c="dl";
      let cs=c+key[2]+"lifter"+i;
      let cell=document.getElementById(cs);
      if (value==1) {cell.style.background="green";}
      if (value==-1) {cell.style.background="red";cell.innerHTML+="x";}
    } //end green red filter
  }
  })
  filtercols();

  sortActive();
} //end function

function filtercols() {
  h=document.querySelectorAll(".lot,.year,.gender,.gear,.lifts,.sr,.br,.idx,.formula,.sa1,.sa2,.sa3,.ba1,.ba2,.ba3,.da1,.da2,.da3,.lighthistory,.pbs,.pbb,.pbd,.pbt,.isActive,.liftidx,.session,.teampoints");
  h.forEach(e => e.style.display="none");
}; //end function filtercols

function sortActive() {
  for (i=1;i<lifterData.length;i++)
    if(document.querySelector("[data-liftidx='"+i+"']")) table.appendChild(document.querySelector("[data-liftidx='"+i+"']"));
} //end function sortActive


</script>
</html>
