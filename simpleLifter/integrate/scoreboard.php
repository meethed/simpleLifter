<?php
include_once "../../../config.php";
$compLetters=filter_input(INPUT_GET, "c", FILTER_SANITIZE_STRING);
$doIPF=filter_input(INPUT_GET,"ipf", FILTER_SANITIZE_NUMBER_INT);
if (empty($doIPF)) {$doIPF=0;};
$stmt = $conn->prepare("SELECT compName FROM comps WHERE compLetters= ?");
$stmt->bind_param("s",$compLetters);;
$stmt->execute();
$compName = $stmt->get_result()->fetch_assoc()["compName"];
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./resources/scoreboard.css">
<title>Live scoreboard</title>
</head>
<body>
<div id="bg"></div>
<div id="tt"><div id="tit"><?php echo $compName; ?></div>
<table id="tableu">
<tr>
<th>Gr</th>
<th>Lot</th>
<th id="n">Lifter Name</th>
<th>Team</th>
<th>Age Div</th>
<th id="d">Division</th>
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
<th>Place</th>
</tr>
</table>
</div>
<script src="lifter.js"></script>
<script src="lifters.js"></script>
<script>
//if it's IPF mode then it will list them all by lot number


var doIPF=<?php echo $doIPF; ?>; //this is in number format
var cL="<?php echo $compLetters; ?>";
var yr=new Date().getFullYear();
var lifterdata=""; //JSON.parse(JSON.stringify(<?php echo file_get_contents("./data/".$compLetters.".json")?>));
var oldJSON=lifterdata;
var timerRefresh;
var refreshInterval=10000;
var setup;
var showGp=0;

//get setup data
fetch("saveload.php?q=loadsetup&comp=<?php echo $compLetters; ?>").then(response=>response.json().then(d=>{
	setup=d;
}));
makeTableu(lifterdata);
fRef();


function fRef() {
fetch("saveload.php?q=loadlifter&comp=<?php echo $compLetters; ?>").then(response=>response.json()).then(data=>{
	if (JSON.stringify(data)==JSON.stringify(oldJSON) && showGp==0) {refreshInterval=5000} else {
		makeTableu(data);
	}
});
// one and a half seconds per lifter
if (setup) refreshInterval = (setup.lifterCount-document.getElementById("tableu").rows.length) * 1500;
timerRefresh=setTimeout(fRef,refreshInterval);
}

function makeTableu(lifters) {
if (lifters=="") return;
if (!setup) return;
oldJSON=lifters;
lifters = lifters.liftList;

var r=0;
var c=0;
var newRow,newCol;
var len=lifters.length;
var activeCol=oldJSON.activeCol;
var isActive=0;
var activeGp=oldJSON.activeGp;

if (!activeGp) activeGp="A";
//clear what we have
while (document.getElementById("tableu").rows.length>1) {
	document.getElementById("tableu").deleteRow(-1);
}

	// if there's more than one full flight (14) then we need to rotate
	// this will ensure there isn't just too much information on the screen
	// to do this, we'll do some simple if/then and swap

	if (setup.lifterCount>14) 
	 if (showGp==activeGp) {showGp="ZZ";} else {showGp=activeGp;};
	if (setup.lifterCount<=14) showGp=0;	
 //end rotation



for (c=0;c<len;c++) {
	isActive=0;
	r=0; //reset the lifters array row counter
	while (c!=lifters[r].sortOrder) {r++; } //arrange by sortorder (ie the current competition lifting order

	//this is the code to skip a lifter
	if (showGp!=0) //if it's not zero means we need to skip some
	 if (showGp=="ZZ") {
	   if (lifters[r].group==activeGp) {continue};
	 } else {
	   if (lifters[r].group!=activeGp) {continue};
	 }
	newRow=document.createElement("tr");
	if (lifters[r].act!=0 && lifters[r].name!="") isActive=1;

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].group;
		if (lifters[r].group!=activeGp) newCol.classList.add("offGp");
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].lot;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].name;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].team;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=ageDiv(lifters[r].year);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].division;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bw;
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=weightClass(lifters[r].bw,lifters[r].division,lifters[r].year);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

	//squat
		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a1;
		newCol.classList.add(gna(lifters[r].sq.s1));
		if (activeCol==10) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a2;
		newCol.classList.add(gna(lifters[r].sq.s2));
		if (activeCol==11) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a3;
		newCol.classList.add(gna(lifters[r].sq.s3));
		if (activeCol==12) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//bench

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a1;
		newCol.classList.add(gna(lifters[r].bp.s1));
		if (activeCol==14) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a2;
		newCol.classList.add(gna(lifters[r].bp.s2));
		if (activeCol==15) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a3;
		newCol.classList.add(gna(lifters[r].bp.s3));
		if (activeCol==16) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//dead

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a1;
		newCol.classList.add(gna(lifters[r].dl.s1));
		if (activeCol==19) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a2;
		newCol.classList.add(gna(lifters[r].dl.s2));

		if (activeCol==20) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a3;
		newCol.classList.add(gna(lifters[r].dl.s3));
		if (activeCol==21) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getTotal(lifters[r]);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=getipf(lifters[r]);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		if (lifters[r].place) {
		newCol.innerHTML=lifters[r].place;}
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);


//ok we've finished adding table cells now add the row and repeat
	document.getElementById("tableu").appendChild(newRow);
}


if (doIPF) { //if it's IPF rules we sort by lot number
var t=document.getElementById("tableu");
var rows,switching,i,x,y,should;
switching=true;

while (switching) {
switching = false;
rows=t.rows;
for (i=1;i<(rows.length-1); i++) {
should=false;
x=rows[i].getElementsByTagName("TD")[1];
y=rows[i+1].getElementsByTagName("TD")[1];
if (parseInt(x.innerHTML) > parseInt(y.innerHTML)) {
should=true;
break;
}
}
if (should) {
rows[i].parentNode.insertBefore(rows[i+1],rows[i]);
switching=true;


}
}

}



} //end function maketableu


function ageDiv(y){
var ad;
if (setup.openOnly) return "O";
if (yr-y <=18) {ad="S-Jr"; return ad;}
if (yr-y <=23) {ad="Jr"; return ad;}
if (yr-y >=70) {ad="M4"; return ad;}
if (yr-y >=60) {ad="M3"; return ad;}
if (yr-y >=50) {ad="M2"; return ad;}
if (yr-y >=40) {ad="M1"; return ad;}
return "O"

return ad;
}

function weightClass(bw,c,y){
  var age=yr-y;
  var gender=c.charAt(0);
  var wc;
  var d=[0];
        switch (gender) {
        case "F": d=setup.fW; break;
        case "X": d=setup.xX; break;
        case "M":
        default:
	d=setup.mW; break;
        }

        if (!d || d.length==1) {
        if (gender==="F") {d=[47,52,57,63,69,76,84,1000];} else {d=[56,59,66,74,83,93,105,120,1000];};
        }
        wc=d.find(el => el >= bw);
        var ix=d.findIndex(el => el >=bw);

        if (ix==d.length-1) wc=d.at(-2)+"+"; //if it's 1000 (the last entry), then they're SHW. So take the previous weight category and add a plus symbol
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
  a=0;

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
if (a==0) {ipf="N/A"} else  ipf = (t*100/(a-b*Math.exp(-c*bw))).toFixed(2);
return ipf   //end ipfGL
}


function gna(stat) {
//good lift no lift attempt
if (stat==1) return "gl";
if (stat==-1) return "nl";
if (stat==0) return "at";

} //end function gna

</script>
</body>
</html>

