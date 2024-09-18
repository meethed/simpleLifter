var ages= [
{desc: "T1", min:0, max: 15},
{desc: "T2", min:16, max: 17},
{desc: "T3", min:18, max: 19},
{desc: "J", min:20, max: 23},
{desc: "Open", min:24, max: 39},
{desc:"M1", min:40, max: 44},
{desc:"M2", min:45, max: 49},
{desc:"M3", min:50, max: 54},
{desc:"M4", min:55, max: 59},
{desc:"M5", min:60, max: 64},
{desc:"M6", min:65, max: 69},
{desc:"M7", min:70, max: 74},
{desc:"M8", min:75, max: 79},
{desc:"M9", min:80, max: 100}
];

var bw={
"M": [52,56,60,67.5,75,82.5,90,100,110,125,145,1000],
"F": [44,47.5,50,53,55.5,58.5,63,70,80,90,1000],
"X": [52,56,60,67.5,75,82.5,90,100,110,125,145,1000]
};

var teampoints=[1,1,1,1,1,1,1,1,1,1,1,1,1,,1,1];

var sjronly=0;
var yearOfBirth=0;
var allowedEquipment=["CL","EQ","CR"];

function getPoints(mfx,bw,t) {
  if (t==-1) return 0;
  var t=t || 0;
  var bw=bw ||0;

  var a=0,b=0,c=0,smf=0;

  if (mfx!="F"){ //if Male or Mx
    // Code taken from OpenLifter:
    // https://gitlab.com/openpowerlifting/openlifter/~/blob/main/src/logic/coefficients/schwartzmalone.ts
    // Thank you so much
    if (bw <= 126.0) {
      const x0 = 0.631926 * 10.0;
      const x1 = 0.262349 * bw;
      const x2 = 0.51155 * Math.pow(10.0, -2) * Math.pow(bw, 2);
      const x3 = 0.519738 * Math.pow(10.0, -4) * Math.pow(bw, 3);
      const x4 = 0.267626 * Math.pow(10.0, -6) * Math.pow(bw, 4);
      const x5 = 0.540132 * Math.pow(10.0, -9) * Math.pow(bw, 5);
      const x6 = 0.728875 * Math.pow(10.0, -13) * Math.pow(bw, 6);
      return x0 - x1 + x2 - x3 + x4 - x5 - x6;
    } else if (bw <= 136.0) {
      return 0.521 - 0.0012 * (bw - 125.0);
    } else if (bw <= 146.0) {
      return 0.509 - 0.0011 * (bw - 135.0);
    } else if (bw <= 156.0) {
      return 0.498 - 0.001 * (bw - 145.0);
    } else {
      // The final formula as published for this piece does not match
      // the coefficient tables.
      //
      // From the tables, the step is exactly 0.0004 per pound, which
      // has been converted to kg below.
      //
      // For reference, the published original is:
      //   0.4880 - 0.0090 * (bw - 156.0)
      return 0.4879 - 0.00088185 * (bw - 155.0);
    }
  } else { //finished with M and Mx onto female
    // Values calculated by fitting to coefficient tables.
    const A = 106.011586323613;
    const B = -1.293027130579051;
    const C = 0.322935585328304;

    // Lower bound chosen at point where Malone = max(Wilks).

    return A * Math.pow(bw, B) + C;

  } //end if female
  return 0
   //end ipfGL
}

function doBreak() { //manages the break between disciplines
  let a = (gps.length>1) ? 10 : 30;
  if (compStatus.thirdLiftIs=="bp1") {doAlertBox([`That was the end of the squats. A ${a} minute break timer will now commence prior to starting the bench press.`],"Start",document.body,breakTimer(a)); postBreak(); drawStatus("b");};
  if (compStatus.thirdLiftIs=="dl1") {doAlertBox([`That was the end of the bench press. A ${a} minute break timer will now commence prior to starting the deadlift.`],"start",document.body,breakTimer(a)); postBreak(); drawStatus("d");};
  if (compStatus.activeLift=="dl3") {doAlertBox(["That was the final lift of the competition. When you click OK, the results will be displayed."],"OK",document.body,generateResults());drawStatus("r");};
} //end function doBreak

function checkFedRules() {
//none for WDFPF
} //end function checkFedRules

function fedSetupPrep() { //called when loading setup window
  setupPopup.sqw.value=25;
  setupPopup.bpw.value=25;
  setupPopup.dlw.value=25;
  setupPopup.sqw.disabled=true;
  setupPopup.bpw.disabled=true;
  setupPopup.dlw.disabled=true;
} //end function fedSetupPrep

function fedPrep() {
  headings.pt="Points";
}
