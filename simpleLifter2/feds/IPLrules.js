var ages= [
{desc: "Jr15-19", min:15, max: 19},
{desc: "Jr20-23", min:20, max: 23},
{desc: "Open", min:24, max: 39},
{desc:"M40-44", min:40, max: 44},
{desc:"M45-49", min:45, max: 49},
{desc:"M50-54", min:50, max: 54},
{desc:"M55-59", min:55, max: 59},
{desc:"M60-64", min:60, max: 64},
{desc:"M55-69", min:65, max: 69},
{desc:"M70-74", min:70, max: 74},
{desc:"M55-79", min:75, max: 79},
{desc:"M80-84", min:80, max: 84},
{desc:"M85+", min:85, max: 99}
];

var bw={
"M": [52,56,60,67.5,75,82.5,90,100,110,125,140,1000],
"F": [44,48,52,56,60,67.5,75,82.5,90,100,110,1000],
"X": [52,56,60,67.5,75,82.5,90,100,110,125,140,1000],
};

var teampoints=[12,9,8,7,6,5,4,3,2,1];

var sjronly=1000;
var yearOfBirth=0;
var allowedEquipment=["CL","EQ","CR","MP"];
var allowedLifts=["PL","BP","PP","DL"];

function getPoints(mfx,g,l,bw,t) {
  if (t==-1) return 0;
  var t=t || 0;
  var bw=bw ||0;

  var a=0,b=0,c=0,d=0,e=0,dots=0;

    if (g!="F") {
    a=0.000001093;
    b=0.0007391293;
    c=0.1918759221
    d=24.0900756;
    e=307.75076;
    } else {
    a=-0.0000010706;
    b=0.0005158568;
    c=-0.1126655495;
    d=13.6175032;
    e=-57.96288;
    }

    dots=t*500/((a*bw**4)+(b*bw**3)+(c*bw**2)+(d*bw)+e);

    return dots.toFixed(3);
   //
} //end function getPoints

function doBreak() { //manages the break between disciplines
  let a = (gps.length>1) ? 10 : 30;
  if (compStatus.thirdLiftIs=="bp1") {doAlertBox([`That was the end of the squats. A ${a} minute break timer will now commence prior to starting the bench press.`],"Start",document.body,breakTimer(a)); postBreak(); drawStatus("b");};
  if (compStatus.thirdLiftIs=="dl1") {doAlertBox([`That was the end of the bench press. A ${a} minute break timer will now commence prior to starting the deadlift.`],"start",document.body,breakTimer(a)); postBreak(); drawStatus("d");};
  if (compStatus.activeLift=="dl3") {doAlertBox(["That was the final lift of the competition. When you click OK, the results will be displayed."],"OK",document.body,generateResults());drawStatus("r");};
} //end function doBreak


function checkFedRules() {
  if (compStatus.activeLift[0]=="s") compStatus.bar=config.sqw ||30;
  if (compStatus.activeLift[0]=="b") compStatus.bar=config.bpw ||25;
  if (compStatus.activeLift[0]=="d") compStatus.bar=config.dlw ||25;
  saveCompStatus("bar",compStatus.bar);
}

function fedSetupPrep() {
}

function fedPrep() {
headings.pt="DOTS";
}
