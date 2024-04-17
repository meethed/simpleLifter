var ages= [
{desc: "Yth", min:0, max: 14},
{desc: "Teen",min:15, max: 19},
{desc: "Jr", min:20, max: 23},
{desc: "Open", min:24, max: 39},
{desc:"M1", min:40, max: 49},
{desc:"M2", min:50, max: 59},
{desc:"M3", min:60, max: 69},
{desc:"M4", min:70, max: 99}
];


var bw={
"M": [52,56,60,67.5,75,82.5,90,100,110,125,140,1000],
"F": [44,48,52,56,60,67.5,75,82.5,90,100,110,1000],
"X": [52,56,60,67.5,75,82.5,90,100,110,125,140,1000],
};

var teampoints=[1,1,1,1,1,1,1,1,1,1,1,1,1];

var sjronly=0;

var allowedEquipment=["RT","WT","RU","WU","SP","MP","RA","WR"];
var allowedLifts=["SQ","BP","DL","PL","PP","SB","SD"];
var yearOfBirth=0;

function getPoints(mfx,g,l,bw,t) {

    if (mfx!="F") {
    a=-0.000001093;
    b=0.0007391293;
    c=-0.1918759221;
    d=24.0900756;
    e=-307.75076;
    } else {
    a=-0.0000010706;
    b=0.0005158568;
    c=-0.1126655495;
    d=13.6175032;
    e=-57.96288;
    }

    dots=t*500/((a*bw**4)+(b*bw**3)+(c*bw**2)+(d*bw)+e);

    return dots.toFixed(3);
} //end function getPoints

function doBreak() { //manages the break between disciplines
  let a = (gps.length>1) ? 10 : 25;
  if (compStatus.thirdLiftIs=="bp1") {doAlertBox([`That was the end of the squats. A ${a} minute break timer will now commence prior to starting the bench press.`],"Start",document.body,breakTimer(a)); postBreak(); drawStatus("b");};
  if (compStatus.thirdLiftIs=="dl1") {doAlertBox([`That was the end of the bench press. A ${a} minute break timer will now commence prior to starting the deadlift.`],"start",document.body,breakTimer(a)); postBreak(); drawStatus("d");};
  if (compStatus.activeLift=="dl3") {doAlertBox(["That was the final lift of the competition. When you click OK, the results will be displayed."],"OK",document.body,generateResults());drawStatus("r");};
} //end function doBreak

function checkFedRules() {
  if (compStatus.activeLift[0]=="s") compStatus.bar=config.sqw ||25;
  if (compStatus.activeLift[0]=="b") compStatus.bar=config.bpw ||25;
  if (compStatus.activeLift[0]=="d") compStatus.bar=config.dlw ||25;
  saveCompStatus("bar",compStatus.bar);
}

function fedSetupPrep() { //called when loading setup window
  setupPopup.sqw.value=25;
  setupPopup.bpw.value=25;
  setupPopup.dlw.value=25;
  setupPopup.sqw.disabled=true;
  setupPopup.bpw.disabled=true;
  setupPopup.dlw.disabled=true;
} //end function fedSetupPrep

function fedPrep() { //run this last to change things for this fed
let t=document.getElementById("comp-table");
  t.children[0].children[4].innerHTML="DOB";
  t.children[0].children[25].innerHTML="DOTS";
  headings.pt="DOTS";
} //end functino fedPrep
