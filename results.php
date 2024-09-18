<?php include_once("./includes/config.inc");
$cL=filter_input(INPUT_GET,"c",FILTER_SANITIZE_STRING);
$f=filter_input(INPUT_GET,"f",FILTER_SANITIZE_STRING);
$isAll=filter_input(INPUT_GET,"ia",FILTER_SANITIZE_STRING);
$sql = "select startdate,fed, compName,seshs from comp where compLetters = '".$cL."'";
$result=$conn->query($sql)->fetch_assoc();
if ($result["seshs"]=="111") {$f=0;};
$fed=$result["fed"];
$cN=$result["compName"];
$startDate=$result["startdate"];
?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body>
<!-- Header -->
<?php if (!isset($_GET["iframe"])) {include "./includes/header.php";} ?>
<!-- Content -->
<div class="content">
  <div class="heading" id="heading"><?php echo $cN; ?> </div>
  <div class="lights" id="lights"></div>
  <table id="table">
    <tr></tr>
  </table>

  <div id="qr" class="qrcode"></div>

  <div class="heading">Competition Summary</div>
  <div id="summary"></div>

  <div class="heading">Download Results</div>
  <div id="downloads">
  <p>The link below is in <a href="www.openpowerlifting.org">www.openpowerlifting.org</a> compatible format.</p>
  <ul>
  <li><a id="csv"><?php echo $cN;?>.csv</a></li>
  </li>
  </div>
  <!-- End Content -->
  </div>

<!-- Footer -->
<?php if (!isset($_GET["iframe"])) {include "./includes/footer.php";} ?>

</body>
<script src="./simpleLifter2/feds/<?php echo $fed;?>rules.js"></script>
<script>
let headings={"gp":"Group","lot":"Lot","name":"Lifter Name","team":"Team","agediv":"Age Group","bw":"Weight","wc":"Weight Class","division":"Division","sq1":"SQ1","sq2":"SQ2","sq3":"SQ3","bsq":"Best SQ","bp1":"BP1","bp2":"BP2","bp3":"BP3","bbp":"Best Bench","dl1":"DL1","dl2":"DL2","dl3":"DL3","bdl":"Best Dead","total":"Total","teampoints":"Team","session":"Session","pt":"Points","place":"Place","st":"Subtotal"};
let lifterData=[];
let compStatus=[];
let isAll=<?php if ($isAll) {echo "1";} else {echo "0";};?>;
let isinframe=(window.location !== window.parent.location);

getData();

tick=setInterval(getData,5000);

if (isinframe) { //if we're in an iframe hide some things
  document.getElementById("heading").style.display="none";
  document.getElementById("lights").style.display="none";
  document.getElementById("qr").style.display="none";
  document.querySelector(".content").style.width="100%";
  table.classList.add("inframe");
}

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
      if (key=="liftidx") lr.dataset.liftidx=value || 0;
      lr.appendChild(td);
      if (key=="idx" && compStatus.activeLifter==value && !isAll)
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

  doSummary();
} //end function

function filtercols() {
  h=document.querySelectorAll(".year,.gender,.gear,.lifts,.sr,.br,.idx,.formula,.sa1,.sa2,.sa3,.ba1,.ba2,.ba3,.da1,.da2,.da3,.lighthistory,.pbs,.pbb,.pbd,.pbt,.isActive,.liftidx");
  h.forEach(e => e.style.display="none");
}; //end function filtercols

function sortActive() {
  //if (!isAll)
  for (i=0;i<=1000;i++)
    if(document.querySelector("[data-liftidx='"+i+"']")) table.appendChild(document.querySelector("[data-liftidx='"+i+"']"));
} //end function sortActive

function doSummary() { //gets team points and overall best

  if (isAll==1) {
    summary.innerHTML="";

    let overallbest=document.createElement("div");
    let countM;
    let bestm=[];
    let bestf=[];
    let teams={};
    let teamsum={};
    //iterate and grab the data for each
    lifterData.forEach((e,i) => {
      if (!e.gender) e.gender=e.division[0];
      if (e.team==null) e.team="";
      if (e.total>0) {
        if (e.gender=="M") if (bestm.length==0) {bestm.unshift(e);} else if(e.pt>(bestm[0].pt||0)) {bestm.unshift(e);} else {bestm.push(e)};
        if (e.gender=="F") if (bestf.length==0) {bestf.unshift(e);} else if(e.pt>(bestf[0].pt||0)) {bestf.unshift(e);} else {bestf.push(e)};
        if (!teams[e.team] || teams[e.team]==null) teams[e.team]="";
        teams[e.team]+=`,${e.teampoints}`;
      }
      //if (e.gender=="C" && e.pt>bestx.pt) bestx=e;
    });

    //overall best lifter
    if (bestf.length>0) overallbest.innerHTML=`<h2>Best Female Lifter</h2> 1st: ${bestf[0].name} (${bestf[0].team}) - ${bestf[0].total}kg / ${bestf[0].pt} Points<br>`;
    if (bestf.length>1) overallbest.innerHTML+=`2nd: ${bestf[1].name} (${bestf[1].team}) - ${bestf[1].total}kg / ${bestf[1].pt} Points<br>`;
    if (bestf.length>2) overallbest.innerHTML+=`3rd: ${bestf[2].name} (${bestf[2].team}) - ${bestf[2].total}kg / ${bestf[2].pt} Points<br>`;
    if (bestm.length>0) overallbest.innerHTML+=`<h2>Best Male Lifter</h2> 1st: ${bestm[0].name} (${bestm[0].team}) - ${bestm[0].total}kg / ${bestm[0].pt} Points<br>`;
    if (bestm.length>1) overallbest.innerHTML+=`2nd: ${bestm[1].name} (${bestm[1].team}) - ${bestm[1].total}kg / ${bestm[1].pt} Points<br>`;
    if (bestm.length>2) overallbest.innerHTML+=`3rd: ${bestm[2].name} (${bestm[2].team}) - ${bestm[2].total}kg / ${bestm[2].pt} Points<br>`;
    summary.appendChild(overallbest);
    if (Object.keys(teams).length>1) {
      let teamsdiv=document.createElement("div");
      teamsdiv.innerHTML="<h2>Team Points - top 5 lifters per team</h2>";
      for (let [key,value] of Object.entries(teams)) {
        let teampoints=value.split(',').filter(Number);
        teampoints.sort((a,b) => b-a);
        value=teampoints.slice(0,5).join("+");
        teamsum[key]=teampoints.reduce((a,v) => {return a+parseInt(v)||0},0);
        if (teamsum[key] && key!="null" && key!="")
         teamsdiv.innerHTML+=`<div class="teams" data-val=${teamsum[key]}>Team ${key}: ${value} = ${teamsum[key]} team points</div>`;
      };
      summary.appendChild(teamsdiv);
    }
    let list=document.querySelectorAll(".teams");
    [...list].sort((a,b) => parseInt(a.dataset.val) < parseInt(b.dataset.val) ? 1 : -1).forEach(node => teamsdiv.appendChild(node));

    } else {
      summary.innerHTML="Summary is only available for the overall competition, not for each session";
    }
} //end function doSummary

document.addEventListener("click", e => {
if (e.target.id=="csv") {
  let r=[];
  let csvContent = "";
  let csv=["Name","Sex","BodyweightKg","WeightClassKg","Squat1Kg","Squat2Kg","Squat3Kg","Best3SquatKg","Bench1Kg","Bench2Kg","Bench3Kg","Best3BenchKg","Deadlift1Kg","Deadlift2Kg","Deadlift3Kg","Best3DeadliftKg","TotalKg","Place","Equipment","Event","Division"];
  let con=["name","gender","bw","wc","sq1","sq2","sq3","bsq","bp1","bp2","bp3","bbp","dl1","dl2","dl3","bdl","total","place","gear","lifts","division"];

  lifterData.forEach((e,i) => {
    r[i]="";
    con.forEach(a => {
      b=e[a];
      if (a=="wc" && b==1000) b=bw[e.gender][bw[e.gender].length-2]+"+";
      if (a=="division") b=divfilter(b,e.gear,e.agediv);
      if (a=="gear") b=gearfilter(b);
      if (a[2]=="1" || a[2]=="2" || a[2]=="3") {
      let at=a[0]+"a"+a[2];
      if (e[at]==-1) b="-"+b;
      }
      if (b===null) b="";
      r[i]+=`"${b}",`;
    });
  })

  csvContent+=csv.join(",")+"\r\n";
  csvContent+=r.join("\r\n");

  csvContent+="\r\n\r\n";
  csvContent+="Federation,Date,MeetCountry,MeetState,MeetTown,MeetName\r\n";
  csvContent+="<?php echo $fed.",".$startDate;?>,,,,<?php echo $cN;?>\r\n";

  console.log(csvContent);

  let blob = new Blob([csvContent], {type: "text/csv;charset=utf-8;"});
  let url=URL.createObjectURL(blob);
  
  let pom=document.getElementById("csv");
  pom.href=url;
  pom.setAttribute("download","simpleLifter <?php echo $cN;?>.csv");
  //pom.click();

};
});

function divfilter(b,g,a) {
  let d=b[0]||"M";
  if (!g) g="C";
  if (g[0]=="E") d+="-";
  if (g[0]=="C") d+="R-";
  if (g[0]=="R") d+="R-";
  if (g[0]=="W") d+="W-";
  if (!a) a="O";
  if (a[0]=="O") {d+="O"}else{d+=a};
 return d;
}

function gearfilter(g) {

if (g[0]=="E") return "Single-Ply";
if (g[0]=="C") return "Raw";
if (g[0]=="R") return "Raw";
if (g[0]=="W") return "Wraps";
}


</script>
</html>

