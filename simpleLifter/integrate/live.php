<?php include_once "../../../config.php"; 
$compLetters=filter_input(INPUT_POST,"compName", 
FILTER_SANITIZE_STRING); if ($compLetters=="") {
 $compLetters=filter_input(INPUT_GET, "c", FILTER_SANITIZE_STRING);}
$stmt = $conn->prepare("SELECT isParent, isChild, compName FROM comps WHERE compLetters= ?");
$stmt->bind_param("s",$compLetters);;
$stmt->execute();
$vals = $stmt->get_result()->fetch_assoc();
$compName=$vals["compName"];
$isParent=$vals["isParent"];
$isChild=$vals["isChild"];
if ($isParent) {
$s2 = $conn->prepare("SELECT compLetters from comps where parentComp=?");
$s2->bind_param("s",$compLetters);
$s2->execute();
$comps=$s2->get_result()->fetch_all();
}


$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="./resources/styles.css">
<title>Live scoreboard</title>
</head>
<body>
<?php if ($compLetters=="ADE") {echo "<h1>APU Classic Nationals 2022</h1><h3>Note lifter's actual age groups are shown, however they are all competing as Open lifters</h3>";};
if ($isParent) {echo "<h1>".$compName."</h1>";};
if (!$isParent) {echo "<iframe style='background:#000;' id='lightsiFrame'  src='../../comp.php?pos=lights&s=OBS&compName=".$compLetters."'></iframe>";}; ?>
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
const isParent = <?php if ($isParent) {echo "1";}else {echo "0";}; ?>;
const isChild = <?php if ($isChild) {echo "1";}else {echo "0";}; ?>;

var yr=new Date().getFullYear();
var lifterdata=""
var oldJSON=lifterdata;
var refreshInterval=10000;
var timerRefresh;
var setup;
var reloading=0;
var setupstr;
var bigdata=[];
//get setup data
// if it's a parent comp then get it from the first child
if (!isParent) setupstr = "saveload.php?q=loadsetup&comp=<?php echo $compLetters; ?>";
if (isParent) setupstr = "saveload.php?q=loadsetup&comp=<?php echo $comps[1][0]; ?>";
fetch(setupstr).then(response=>response.json().then(d=>{
	setup=d;
}));
makeTableu(lifterdata);
fRef();

if (!isParent) timerRefresh=setTimeout(fRef,refreshInterval);

function fRef() {

if (!setup) {reloading+=1;reload=setTimeout(fRef,500);return -1};
if (!isParent) {
fetch("saveload.php?q=loadlifter&comp=<?php echo $compLetters; ?>").then(response=>response.json()).then(data=>{
	if (JSON.stringify(data)==JSON.stringify(oldJSON)) {refreshInterval=3000} else {
		makeTableu(data);
		refreshInterval=5000;
	} // if new data
}); //end fetch
timerRefresh=setTimeout(fRef,refreshInterval);
} //end if not is parent
if (isParent) {
var compArray=[<?php foreach ($comps as $v) {echo "'".$v[0]."',";};?>];
compArray.shift(); //get rid of parent comp
compArray.forEach(e  => {
  fetch("saveload.php?q=loadlifter&comp="+e).then(response=>response.json()).then(data=>{
  addTableu(data.liftList);
  }); //end fetch
}); //end for each

} // end if is parent
}

function addTableu(lifterdata) {
if (lifterdata=="") return;
oldJSON=lifterdata;
if (!isParent) {
var activeCol=lifterdata.activeCol;
var isActive=0;
var activeGp=lifterdata.activeGp;
var lifters=lifterdata.liftList
var len=lifters.length;
var r=0;
var c=0;
var newRow,newCol;
}

if (isParent) {
var activeCol=0;
var isActive=-1;
var activeGp="Z";
var lifters=lifterdata;
var len=lifters.length;
var r=0;
var c=0;
var newRow,newCol;
}

for (c=0;c<len;c++) {
	isActive=0;
		r=0;

		if (!isParent) {while (c!=lifters[r].sortOrder) {r++ }} else {r=c;};

	newRow=document.createElement("tr");
	if (lifters[r].act!=0 && !isParent && lifters[r].name!="") isActive=1;

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
		newCol.innerHTML=lifters[r].year;
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
		newCol.innerHTML=weightClass(lifters[r]);
		if (isActive) newCol.classList.add("act");
		newRow.appendChild(newCol);

	//squat
		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a1;
		if (lifters[r].sq.s1==1) newCol.classList.add("gl");
		if (lifters[r].sq.s1==-1) newCol.classList.add("nl");
		if (activeCol==10) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a2;
		if (lifters[r].sq.s2==1) newCol.classList.add("gl");
		if (lifters[r].sq.s2==-1) newCol.classList.add("nl");
		if (activeCol==11) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].sq.a3;
		if (lifters[r].sq.s3==1) newCol.classList.add("gl");
		if (lifters[r].sq.s3==-1) newCol.classList.add("nl");
		if (activeCol==12) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//bench

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a1;
		if (lifters[r].bp.s1==1) newCol.classList.add("gl");
		if (lifters[r].bp.s1==-1) newCol.classList.add("nl");
		if (activeCol==14) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a2;
		if (lifters[r].bp.s2==1) newCol.classList.add("gl");
		if (lifters[r].bp.s2==-1) newCol.classList.add("nl");
		if (activeCol==15) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].bp.a3;
		if (lifters[r].bp.s3==1) newCol.classList.add("gl");
		if (lifters[r].bp.s3==-1) newCol.classList.add("nl");
		if (activeCol==16) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

//dead

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a1;
		if (lifters[r].dl.s1==1) newCol.classList.add("gl");
		if (lifters[r].dl.s1==-1) newCol.classList.add("nl");
		if (activeCol==19) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a2;
		if (lifters[r].dl.s2==1) newCol.classList.add("gl");
		if (lifters[r].dl.s2==-1) newCol.classList.add("nl");
		if (activeCol==20) newCol.style.fontWeight="bold";
		newRow.appendChild(newCol);

		newCol=document.createElement("td");
		newCol.innerHTML=lifters[r].dl.a3;
		if (lifters[r].dl.s3==1) newCol.classList.add("gl");
		if (lifters[r].dl.s3==-1) newCol.classList.add("nl");
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
	document.getElementById("tableu").appendChild(newRow);
} //end for the next lifter

if (isParent) { // if its a compilation, sort properly
var items=document.querySelectorAll("tr");
var itemsArr=[];
var v,i;
Array.from(items).forEach((v,i) => { 
  if (items[i].hasChildNodes())
    if (items[i].childNodes.length>5) itemsArr.push(items[i]);
});
itemsArr.shift();
itemsArr.sort(function(a,b) {
  var sa=a.childNodes[6].innerHTML+a.childNodes[5].innerHTML+a.childNodes[8].innerHTML
  var sb=b.childNodes[6].innerHTML+b.childNodes[5].innerHTML+b.childNodes[8].innerHTML
  return sa == sb ? 0 : (sa > sb? 1: -1);
});

for (i =0; i<itemsArr.length; i++) document.getElementById("tableu").appendChild(itemsArr[i]);
} //do sort
} //end function add tableu


function makeTableu(lifterdata) {
while (document.getElementById("tableu").rows.length>1) {
	document.getElementById("tableu").deleteRow(-1);
}

addTableu(lifterdata);
} //end function maketableu


function ageDiv(y){
if (setup.openOnly || y==0) return "O";
if (yr-y <=18) return "S-Jr";
if (yr-y <=23) return "Jr";
if (yr-y >=70) return "M4";
if (yr-y >=60) return "M3";
if (yr-y >=50) return "M2";
if (yr-y >=40) return "M1";
return "O"


return ad;
}

function weightClass(l){
if (!setup) return;
var bw=l.bw;
var c=l.division;
var y=l.year;
var age=yr-y;
var f,m,x;
if (l.wc) {
l.wc = l.wc.slice(-1)==" " ? l.wc.slice(0,-1)+"+" : l.wc;
return l.wc+"kg"; }
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

