var ages= [
{desc: "S-Jr", min:0, max: 18},
{desc: "Jr", min:19, max: 23},
{desc: "Open", min:24, max: 39},
{desc:"M1", min:40, max: 49},
{desc:"M2", min:50, max: 59},
{desc:"M3", min:60, max: 69},
{desc:"M4", min:70, max: 99}
];

var bw={
"M": [53,56,59,66,74,83,93,105,120,1000],
"F": [43,47,52,57,63,69,76,84,1000],
"X": [53,56,59,66,74,83,93,105,120,1000]
};

var teampoints=[12,9,8,7,6,5,4,3,2,1];
var yearOfBirth=1;
var sjronly=19;

var allowedEquipment=["CL","EQ"];
var allowedLifts=["PL","BP","PP","DL"];
function getPoints(mfx,g,l,bw,t) {
  if (t==-1) return 0;
  var t=t || 0;
  var bw=bw ||0;

  var a=0,b=0,c=0,ipf=0;

  if (mfx!="F"){ //if Male or Mx
    if (g=="EQ"){ //if equipped (assume everything else is raw)
      if (l!="BP"){ //if three lift (assume everything that isn't bench is 3 lift)
        a=381.22073;
        b=733.79378;
        c=0.02398;
      } else { //not 3 lift must be bench
        a=1236.25115;
        b=1449.21864;
        c=0.01644;
      } //finished with equipped so now everything else is raw
    } else {
      if (l!="BP") { //if raw 3 lift
        a=1199.72839;
        b=1025.18162;
        c=0.00921;
      } else { //must be raw bench only
        a=320.98041;
        b=281.40258;
        c=0.01008;
      }
    }
  } else { //finished with M and Mx onto female
    if (g=="EQ"){ //if equipped (everything else raw)
      if (l!="BP") { //if not bench only (everything else is 3 lift)
        a=758.63878;
        b=949.31382;
        c=0.02435;
      } else { //if not not bench, must be bench
          a=221.82209;
          b=357.00377;
          c=0.02937;
      }
    } else { //if not equipped must be raw
      if (l!="BP") { //if not bench only
        a=610.32796;
        b=1045.89282;
        c=0.03048;
      } else { //if bench only
        a=142.40398;
        b=442.52671;
        c=0.04724;
      } //end if female raw bench only
    } //end if female raw
  } //end if female

    if (a==0) {ipf=0} else  ipf = (t*100/(a-b*Math.exp(-c*bw))).toFixed(2);
    return ipf;
   //end ipfGL
}

function doBreak() { //manages the break between disciplines
  let a = (gps.length>1) ? 10 : 20;
  if (compStatus.activeLift=="sq3") {doBreakTimer(`That was the end of the squats. A ${a} minute break timer will now commence prior to starting the bench press.`,a)};
  if (compStatus.activeLift=="bp3") {doBreakTimer(`That was the end of the bench press. A ${a} minute break timer will now commence prior to starting the deadlift.`,a)};
  if (compStatus.activeLift[0]=="d") {doAlertBox("That was the final lift of the competition. When you click OK, the results will be displayed.",0,"")};
} //end function doBreak

function checkFedRules() {
compStatus.bar=25;
//none for IPF
} //end function checkFedRules

function fedSetupPrep() { //called when loading setup window
  setupPopup.sqw.value=25;
  setupPopup.bpw.value=25;
  setupPopup.dlw.value=25;
  setupPopup.sqw.disabled=true;
  setupPopup.bpw.disabled=true;
  setupPopup.dlw.disabled=true;
} //end function fedSetPrep

function fedPrep() {
  headings.pt="IPF Points";
}
