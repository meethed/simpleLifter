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
<th>Points</th>
</tr>
</table>
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
var activeCol=lifters.activeCol;
var isActive=0;
var activeGp=lifters.activeGp;
var lifters=lifters.liftList
var len=lifters.length;
var r=0;
var c=0;
var newRow,newCol;

while (document.getElementById("tableu").rows.length>1) {
	document.getElementById("tableu").deleteRow(-1);
}
for (c=0;c<len;c++) {
	isActive=0;
	r=0;
	while (c!=lifters[r].sortOrder) {r++ }

	
	newRow=document.createElement("tr");
	if (lifters[r].act!=0 && lifters[r].name!="") isActive=1;

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].group;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].lot;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].name;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].team;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].year;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=ageDiv(lifters[r].year);
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].division;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bw;
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=weightClass(lifters[r].bw,lifters[r].division,lifters[r].year);
		newRow.appendChild(newCol);

	//squat
		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a1 * lifters[r].sq.s1;
		if (lifters[r].sq.s1==1) newCol.classList.add("gl");
		if (lifters[r].sq.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a2 * lifters[r].sq.s2;
		if (lifters[r].sq.s2==1) newCol.classList.add("gl");
		if (lifters[r].sq.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a3 * lifters[r].sq.s3;
		if (lifters[r].sq.s3==1) newCol.classList.add("gl");
		if (lifters[r].sq.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

//bench

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a1 * lifters[r].bp.s1;
		if (lifters[r].bp.s1==1) newCol.classList.add("gl");
		if (lifters[r].bp.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a2 * lifters[r].bp.s2;
		if (lifters[r].bp.s2==1) newCol.classList.add("gl");
		if (lifters[r].bp.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a3 * lifters[r].bp.s3;
		if (lifters[r].bp.s3==1) newCol.classList.add("gl");
		if (lifters[r].bp.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

//dead

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a1 * lifters[r].dl.s1;
		if (lifters[r].dl.s1==1) newCol.classList.add("gl");
		if (lifters[r].dl.s1==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a2 * lifters[r].dl.s2;
		if (lifters[r].dl.s2==1) newCol.classList.add("gl");
		if (lifters[r].dl.s2==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a3 * lifters[r].dl.s3;
		if (lifters[r].dl.s3==1) newCol.classList.add("gl");
		if (lifters[r].dl.s3==-1) newCol.classList.add("nl");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getTotal(lifters[r]);
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getipf(lifters[r]);
		newRow.appendChild(newCol);
	document.getElementById("tableu").appendChild(newRow);
}
} //end function maketableu


function ageDiv(y){
if (setup.openOnly) return "O";
if (yr-y <=18)  return "S-Jr";
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


</script>
</body>
</html>

