<?php
include_once "../../../config.php";
$compLetters=$_POST["compName"];
if ($compLetters=="") {echo "Invalid Login"; die();}
$stmt = $conn->prepare("SELECT compName FROM comps WHERE compLetters= ?");
$stmt->bind_param("s",$compLetters);;
$stmt->execute();
$compName = $stmt->get_result()->fetch_assoc()["compName"];
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./styles.css">
<title>Live scoreboard</title>
</head>
<body>
<h1>Live Scoresheet for <?php echo $compName ?></h1>

<table id="tableu">
<tr>
<th>Gr</th>
<th>Lot</th>
<th>Lifter Name</th>
<th>Team</th>
<th>Birth Year</th>
<th>Age Div</th>
<th>Division</th>
<th>BW</th>
<th>Weight Class</th>
<th>SQ 1</th>
<th>SQ 2</th>
<th>SQ 3</th>
<th>BP 1</th>
<th>BP 2</th>
<th>BP 3</th>
<th>DL 1</th>
<th>DL 2</th>
<th>DL 3</th>
<th>Total</th>
</tr>
</table>
<script src="lifter.js"></script>
<script src="lifters.js"></script>
<script>
var yr=new Date().getFullYear();
var lifterdata=JSON.parse(JSON.stringify(<?php echo file_get_contents("./data/".$compLetters.".json")?>));
var fw=[43, 47, 52, 57, 63, 69, 76, 84, 1000];
var mw=[53, 59, 66, 74, 83, 93, 105,120,1000];
var oldJSON=lifterdata;
var refreshInterval=10000;
var timerRefresh;
makeTableu(lifterdata);
fRef();
timerRefresh=setTimeout(fRef,refreshInterval);

function fRef() {
fetch("saveload.php?q=loadlifter&comp=<?php echo $compLetters; ?>").then(response=>response.json()).then(data=>{
	if (JSON.stringify(data)==JSON.stringify(oldJSON)) {refreshInterval=3000} else {
		makeTableu(data);
		refreshInterval=10000;

	}
});
timerRefresh=setTimeout(fRef,refreshInterval);
}

function makeTableu(lifters) {
oldJSON=lifters;
var activeCol=lifters.activeCol;
var isActive=0;
var activeGp=lifters.activeGp;
var lifters=Object.entries(lifters);
var len=lifters.length;
var r=0;
var c=0;
var newRow,newCol;

while (document.getElementById("tableu").rows.length>1) {
	document.getElementById("tableu").deleteRow(-1);
}
for (c=0;c<len-3;c++) {
	isActive=0;
//	r=0;
//	while (c!=lifters[r][1].sortOrder) {r++ }
	newRow=document.createElement("tr");
	if (lifters[r][1].act!=0 && lifters[r][1].name!="") isActive=1;

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].group;
		if (lifters[r][1].group!=activeGp) newCol.classList.add("offGp");
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].lot;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].name;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].team;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].year;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=ageDiv(lifters[r][1].year);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].division;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].bw;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=weightClass(lifters[r][1].bw,lifters[r][1].division);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

	//squat
		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].sq.a1;
		if (lifters[r][1].sq.s1==1) newCol.classList.add("gl");
		if (lifters[r][1].sq.s1==-1) newCol.classList.add("nl");
		if (activeCol==10) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].sq.a2;
		if (lifters[r][1].sq.s2==1) newCol.classList.add("gl");
		if (lifters[r][1].sq.s2==-1) newCol.classList.add("nl");
		if (activeCol==11) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].sq.a3;
		if (lifters[r][1].sq.s3==1) newCol.classList.add("gl");
		if (lifters[r][1].sq.s3==-1) newCol.classList.add("nl");
		if (activeCol==12) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//bench

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].bp.a1;
		if (lifters[r][1].bp.s1==1) newCol.classList.add("gl");
		if (lifters[r][1].bp.s1==-1) newCol.classList.add("nl");
		if (activeCol==14) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].bp.a2;
		if (lifters[r][1].bp.s2==1) newCol.classList.add("gl");
		if (lifters[r][1].bp.s2==-1) newCol.classList.add("nl");
		if (activeCol==15) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].bp.a3;
		if (lifters[r][1].bp.s3==1) newCol.classList.add("gl");
		if (lifters[r][1].bp.s3==-1) newCol.classList.add("nl");
		if (activeCol==16) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//dead

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].dl.a1;
		if (lifters[r][1].dl.s1==1) newCol.classList.add("gl");
		if (lifters[r][1].dl.s1==-1) newCol.classList.add("nl");
		if (activeCol==19) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].dl.a2;
		if (lifters[r][1].dl.s2==1) newCol.classList.add("gl");
		if (lifters[r][1].dl.s2==-1) newCol.classList.add("nl");
		if (activeCol==20) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r][1].dl.a3;
		if (lifters[r][1].dl.s3==1) newCol.classList.add("gl");
		if (lifters[r][1].dl.s3==-1) newCol.classList.add("nl");
		if (activeCol==21) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getTotal(lifters[r][1]);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

	document.getElementById("tableu").appendChild(newRow);
  r++;	

}
} //end function maketableu


function ageDiv(y){
var ad;
if (yr-y <=18) {ad="S-Jr"; return ad;}
if (yr-y <=23) {ad="Jr"; return ad;}
if (yr-y >=70) {ad="M4"; return ad;}
if (yr-y >=60) {ad="M3"; return ad;}
if (yr-y >=50) {ad="M2"; return ad;}
if (yr-y >=40) {ad="M1"; return ad;}
return "O"


return ad;
}

function weightClass(bw,c){
var wc=0;
if (c.charAt(0)=="F") {
	wc=fw.find(el => el >= bw);
	if (wc==1000) wc="84+";
} else
{
	wc=mw.find(el => el >= bw);
	if (wc==1000) wc="120+";
}
return wc
}

function getTotal(l){
var bs=Math.max(l.sq.a1*l.sq.s1,l.sq.a2*l.sq.s2,l.sq.a3*l.sq.s3,0);
var bb=Math.max(l.bp.a1*l.bp.s1,l.bp.a2*l.bp.s2,l.bp.a3*l.bp.s3,0);
var bd=Math.max(l.dl.a1*l.dl.s1,l.dl.a2*l.dl.s2,l.dl.a3*l.dl.s3,0);

total=bs+bb+bd;
if (total<0) total="DSQ";
return total;
}



</script>
</body>
</html>

