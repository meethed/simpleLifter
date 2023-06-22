<?php
include_once "../../../config.php";
$compLetters=filter_var($_GET["c"], FILTER_SANITIZE_STRING);
if ($compLetters=="") {die("no competition selected");};
$stmt = $conn->prepare("SELECT compName,startDate FROM comps WHERE compLetters= ?");
$stmt->bind_param("s",$compLetters);;
$stmt->execute();
$out = $stmt->get_result()->fetch_assoc();
$compName=$out["compName"];
$startDate=$out["startDate"];
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./resources/styles.css">
<title>Archived Scoreboard</title>
</head>
<body>
<h1>Archived Results<br><?php echo $compName ?></h1>
<h2><?php echo $startDate; ?></h2>
<table id="tableu">
<tr>
<th>Place</th>
<th>Name</th>
<th>Sex</th>
<th>BirthDate</th>
<th>Age</th>
<th>Equipment</th>
<th>Division</th>
<th>BodyweightKg</th>
<th>WeightClassKg</th>
<th>Squat1Kg</th>
<th>Squat2Kg</th>
<th>Squat3Kg</th>
<th>Best3SquatKg</th>
<th>Bench1Kg</th>
<th>Bench2Kg</th>
<th>Bench3Kg</th>
<th>Best3BenchKg</th>
<th>Deadlift1Kg</th>
<th>Deadlift2Kg</th>
<th>Deadlift3Kg</th>
<th>Best3DeadliftKg</th>
<th>TotalKg</th>
<th>Event</th>
<th>Points</th>
<th>Team</th>
</tr>
</table>
<br><a style="padding:5px;border:inset 3px black; background-image: linear-gradient(45deg,#777,white,#aaa);" onclick="exportToExcel()">Click to here export as CSV</a>
<p>These archived results are displayed in a format that is compatible with <a href="http://www.openpowerlifting.org">OpenPowerlifting</a>. The table above can be copy+pasted into Excel or Sheets, alternatively the CSV file at the link above can be downloaded to a computer.</p>
<script src="lifter.js"></script>
<script src="lifters.js"></script>
<script>
var cL="<?php echo $compLetters; ?>";
var year=" <?php echo $startDate; ?>";
var yr=year.split("-")[0].trim()
var lifterdata=JSON.parse(JSON.stringify(<?php echo file_get_contents("./data/".$compLetters.".json")?>));
var setup;
//get setup data
fetch("saveload.php?q=loadsetup&comp=<?php echo $compLetters; ?>").then(response=>response.json().then(d=>{
	setup=d;
	makeTableu(lifterdata);
}));

function makeTableu(lifters) {
var lifters=lifters.liftList
var len=lifters.length;
var r=0;
var c=0;
var newRow,newCol;

while (document.getElementById("tableu").rows.length>1) {
	document.getElementById("tableu").deleteRow(-1);
}
for (c=0;c<len;c++) {
	r=0;
	while (c!=lifters[r].sortOrder) {r++ }
	curDiv=lifters[r].division.split("-");
	newRow=document.createElement("tr");

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].place;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].name;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=curDiv[0];
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].year;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=yr-lifters[r].year;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getOPLEquipment(curDiv[1]);
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		var oplDiv =curDiv[0];
		if (curDiv[1].charAt(0)=="C") oplDiv+="R"
		oplDiv+="-";
		oplDiv+=ageDiv(lifters[r].year);
		newCol.innerHTML=oplDiv;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bw;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=weightClass(lifters[r].bw,lifters[r].division,lifters[r].year);
		newRow.appendChild(newCol);

	//squat
		var bestSq=0,curSq=0;;
		newCol=document.createElement("td");
		curSq=lifters[r].sq.a1 * lifters[r].sq.s1;
		bestSq=Math.max(curSq,0);
		newCol.innerHTML=curSq;
		if (lifters[r].sq.s1==1) newCol.classList.add("gl");
		if (lifters[r].sq.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curSq=lifters[r].sq.a2 * lifters[r].sq.s2;
		bestSq=Math.max(bestSq,curSq,0);
		newCol.innerHTML=curSq;
		if (lifters[r].sq.s2==1) newCol.classList.add("gl");
		if (lifters[r].sq.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curSq=lifters[r].sq.a3 * lifters[r].sq.s3;
		bestSq=Math.max(bestSq,curSq,0);
		newCol.innerHTML=curSq;
		if (lifters[r].sq.s3==1) newCol.classList.add("gl");
		if (lifters[r].sq.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=bestSq;
		newRow.appendChild(newCol);
//bench
		var bestBP=0, curBP=0;
		newCol=document.createElement("td");
		curBP=lifters[r].bp.a1 * lifters[r].bp.s1;
		newCol.innerHTML=curBP;
		bestBP=Math.max(curBP,0);
		if (lifters[r].bp.s1==1) newCol.classList.add("gl");
		if (lifters[r].bp.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curBP=lifters[r].bp.a2 * lifters[r].bp.s2;
		newCol.innerHTML=curBP;
		bestBP=Math.max(curBP,bestBP,0);
		if (lifters[r].bp.s2==1) newCol.classList.add("gl");
		if (lifters[r].bp.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curBP=lifters[r].bp.a3 * lifters[r].bp.s3;
		newCol.innerHTML=curBP;
		bestBP=Math.max(curBP,bestBP,0);
		if (lifters[r].bp.s3==1) newCol.classList.add("gl");
		if (lifters[r].bp.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=bestBP;
		newRow.appendChild(newCol);
//dead
		var bestDL=0, curDL=0;
		newCol=document.createElement("td");
		curDL=lifters[r].dl.a1 * lifters[r].dl.s1;
		newCol.innerHTML=curDL;
		bestDL=Math.max(curDL,0);
		if (lifters[r].dl.s1==1) newCol.classList.add("gl");
		if (lifters[r].dl.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curDL=lifters[r].dl.a2 * lifters[r].dl.s2;
		newCol.innerHTML=curDL;
		bestDL=Math.max(curDL,bestDL,0);
		if (lifters[r].dl.s2==1) newCol.classList.add("gl");
		if (lifters[r].dl.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		curDL=lifters[r].dl.a3 * lifters[r].dl.s3;
		newCol.innerHTML=curDL;
		bestDL=Math.max(curDL,bestDL,0);
		if (lifters[r].dl.s3==1) newCol.classList.add("gl");
		if (lifters[r].dl.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=bestDL;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getTotal(lifters[r]);
		newRow.appendChild(newCol);
		
		newCol=document.createElement("td");
		newCol.innerHTML=eventFilter(curDiv[2]);
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getipf(lifters[r]);
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].team;
		newRow.appendChild(newCol);

	document.getElementById("tableu").appendChild(newRow);
}
} //end function maketableu

function eventFilter(e) {
switch (e) {
case "PL": return "SBD";break;
case "BP": return "B"; break;
case "PP": return "BD"; break;
case "DL": return "D"; break;
case "SQ": return "S"; break;
}
}

function getOPLEquipment(e) {
switch (e) {
  case "CL": return "Raw";break;
  case "EQ": return "Single-ply";break;
  case "MP": return "Multi-ply";break;
  case "CR": return "Raw+Wraps";break;
  default: return "Raw"; break;
}
}

function ageDiv(y){
if (setup.openOnly) return "O";
if (yr-y <=18)  return "SJ";
if (yr-y <=23)  return "Jr";
if (yr-y >=70)  return "M4";
if (yr-y >=60)  return "M3";
if (yr-y >=50)  return "M2";
if (yr-y >=40)  return "M1";
return "O"
}

function weightClass(bw,c,y){
if (!setup) return;
var age=yr-y;
var f,m,x;
var wc=0;
var gender = c.charAt(0);

        switch (gender) {
        case "F": d=setup.fW; break;
        case "X": d=setup.xW; break;
        case "M":
	default:
	d=setup.mW; break;
        }

if (!d || d.length==1) {
        if (gender==="F") {d=[47,52,57,63,69,76,84,1000];} else {d=[56,59,66,74,83,93,105,120,1000]};
        }
        wc=d.find(el => el >= bw);
        var ix=d.findIndex(el => el >=bw);

        if (ix==d.length-1) wc=d.at(-2)+"+"; //if it's 1000 (the last entry), t$
        if (ix==0 && age>23) wc=d[1];

        if (!wc) wc="";
        return wc+"kg";
}

function getTotal(l){
var bs=Math.max(l.sq.a1*l.sq.s1,l.sq.a2*l.sq.s2,l.sq.a3*l.sq.s3,-1);
var bb=Math.max(l.bp.a1*l.bp.s1,l.bp.a2*l.bp.s2,l.bp.a3*l.bp.s3,-1);
var bd=Math.max(l.dl.a1*l.dl.s1,l.dl.a2*l.dl.s2,l.dl.a3*l.dl.s3,-1);

total=bs+bb+bd;
if (bs<0 || bb<0 || bd<0) total="DSQ";
return total;
}


 function getipf(l) { //returns the ipf points for the lifter, based on their total, classic/equipped, 3 lift or bench only, and gender
  var t=getTotal(l);
  var bw=l.bw;
  var d=l.division;
  var a,b,c;


	if (setup.CAPO) return getGlos(t,bw,d,yr-l.year);
        function getGlos(t,bw,d,age) { //internal function for glosbrenner ESTIMATE
        var a,b,c,d,e,f,mam;
        if (d.charAt(0)=="F") {
                a=-0.00000001;
                b=0.00001;
                c=-0.0041;
                d=0.7349;
                e=-65.865;
                f=3041.2;
        } else {
                a=-3.77264e-08;
                b=3e-05;
                c=-0.00933;
                d=1.428460179;
                e=-108.661;
                f=3875.454327;
        }
        if (age>=23 && age <=40) {mam=1} else {
                if (age>40) {mam = (0.0463*age**2 - 2.9491 * age + 144.48)/100};
       }
        var glos = a*bw**5 + b*bw**4 + c*bw**3 + d*bw**2 + e*bw+f;
        glos = glos * t * mam /1000;
        return glos.toFixed(2);
        } //end internal function getGlos

  if (!d) {d="M-CL-PL"};
  if (d=="M-EQ-PL") {
    a=1236.25115;
    b=1449.21864;
    c=0.01644;
    }
  if (d=="M-CL-PL" || d=="M-SO-PL" || d=="M-SO-PP") {
    a=1199.72839;
    b=1025.18162;
    c=0.00921;
    }
    if   (d=="M-EQ-BP") {
    a=381.22073;
    b=733.79378;
    c=0.02398;
    }
    if (d=="M-CL-BP" || d=="M-SO-BP") {
    a=320.98041;
    b=281.40258;
    c=0.01008;
    }
    if (d=="F-EQ-PL") {
    a=758.63878;
    b=949.31382;
    c=0.02435;
    
    }
    if (d=="F-CL-PL" || d=="F-SO-PL" || d=="F-SO-PP") {
    a=610.32796;
    b=1045.89282;
    c=0.03048;
    }
    if (d=="F-EQ-BP") {
    a=221.82209;
    b=357.00377;
    c=0.02937;
    }
    if (d=="F-CL-BP" || d=="F-SO-BP") {
    a=142.40398;
    b=442.52671;
    c=0.04724;
    }
if (a==0) {ipf=0} else  ipf = (t*100/(a-b*Math.exp(-c*bw))).toFixed(2);
return ipf   //end ipfGL
}

function exportToExcel(){ //export the table to CSS
	var linky,csv_data=",", dataType='application/vnd.ms-excel';
	var rows= document.getElementsByTagName('tr');
	for (var i=0; i< rows.length; i++) {
		var cols = rows[i].querySelectorAll("td,th");
		for (var j=0; j<cols.length; j++) {
			csv_data += cols[j].innerHTML + ",";
		}
		csv_data += "\n";
	}
	let d= new Date();

	 filename="<?php echo $compName; ?> - " + d.toISOString().split("T")[0]+".csv";
 
	 linky=document.createElement("a");
	 document.body.appendChild(linky);
	 linky.href='data:attachment/csv'+encodeURI(csv_data);
	 linky.download=filename;
	 linky.click();
	 

} //end function exporttoexcel



</script>
</body>
</html>
