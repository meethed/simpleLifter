let timers={};
let lights=[0,0,0];
let updated=0;
let oldLights=[];
let offset=0;
let lockout=0;
const convertTime = (t) => t.slice(0,10)+"T"+t.slice(11)+"Z"; //function to add T and Z to the unix timestamp
const colours=["black","white","red","blue","yellow","red"];

function  drawLights(t,l) { //draw [l]ights [l,c,r] into [t]arget. target must be set up with the template
  lights=t.querySelectorAll(".light"); //get all of the lights
  let oldLights=[t.dataset.l,t.dataset.c,t.dataset.r];

  if (JSON.stringify(oldLights) === JSON.stringify(l)) return -1; //if they're the same do nothing


  if (l.every(x => x==0)) { //if they're all zero, turn off lights
   lights.forEach(e => e.className="light");
  return 0;
  }

  if (l.every(x => x>0))  //if they're all on, turn them all on
  {
    lights.forEach(e => e.className="light");
    for (i=0;i<3;i++) {
      if (l[i]>=2) {lights[i+3].classList.add(colours[l[i]]);lights[i].classList.add("red")} else {lights[i].classList.add(l[i]==2 ? "red" :"white")};
     }
  nextAttemptTimer();
  try {if (config?.auto) doAutoRef(l);} catch {};
  return 1
  } else if (l.some(x => x>0)) { //if they're not all on just draw the pending ones
    lights.forEach(e => e.className="light");
    for (i=0;i<3;i++) 
      if (l[i]>0) lights[i].classList.add("pending");
  return 2;
  }
}


function setTimer(t,targettime) { //sets up a timer in DIV target [t], counting down to [targettime], and stores it in the timers array

  //clear
  t.innerHTML="";
  t.className="time-text";

  getheartbeat();
  let d = Date.now();
  let interval=targettime-d+offset;
  if (interval<=0) {
  delete timers[t.id];
  t.dataset.targettime=0;
  if (t.id=="blt") t.innerHTML="01 : 00";
  return 0; //return 0 to ensure that the calling function knows timer is cleared
  }
  //if we've made it here, the target time is in the future so we need to do something

  timers[t.id]=targettime; //stores the timer handle in the object
  t.dataset.targettime=targettime+offset;
  return 1;
}

let offsetTick = setInterval(() => {getheartbeat},30000);

timerTick = setInterval(() => { //this isn't a function. this interval runs constantly and so updates every timer at the same time. Whilst the server has 3 in compStatus, this can run as many timers as needed purely clientside and will be useful for the offline version.
  for (const t in timers) {
    let n=Date.now(); //now
    let d=document.getElementById(t); //div to display
    let tt=d.dataset.targettime-n; //targettime
    if (tt>0) {
      //if it's active

      //hours, minutes, seconds only (not days)

      let hours=Math.floor((tt % (1000 * 3600 * 24)) / (1000*3600));
      let mins=Math.floor((tt % (1000 * 3600)) / (1000*60));
      let secs=Math.floor((tt % (1000 * 60)) / (1000));


      d.style.color="#fff";
      if (hours<1 && mins<1) {
      if (secs<30) d.style.color="#fa0";
      if (secs<10) d.style.color="#f00";
      }
      hours=hours.toString().padStart(2,"0");
      mins=mins.toString().padStart(2,"0");
      secs=secs.toString().padStart(2,"0");
      d.innerHTML=`${hours} : ${mins} : ${secs}`;
      d.innerHTML=`${mins} : ${secs}`;
    } else {
      d.style.color="#fff";
      d.innerHTML="";
      if (d.id=="blt") d.innerHTML="01 : 00";
      d.className="time-text";
      delete d.dataset.targettime;
      delete timers[t];
      checkAllOff();
    }
  };
},250); //end timertick interval

function checkAllOff() {}; //TODO but so far nothing required

function getheartbeat() { //approximates the round time delay to sync up the clocks
  let sd=Date.now();

  fetch("heartbeat.php").then(response =>response.text()).then(data => {
    let ed=Date.now();
    offset= (ed+sd)/2-data+(ed-sd);
  });

} //end function getheartbeat

function breakTimer(t) { //loads up timerThree with a break timer
  console.log(`BREAK. Competition will resume in ${t} minutes`);
  let ts=new Date(Date.now().valueOf()+(60000*t)-offset+500).toISOString(); //the extra 500 miliseconds are in there cause SQL doesn't work in miliseconds. Alternatively i could round it
  timeString=ts.slice(0,10)+" "+ts.slice(11,19);
  saveCompStatus("timeThree",timeString);
} //end function breakTimer

function nextAttemptTimer() { //sets the 1 minute next attempt submission
  if (new Date(compStatus.timeTwo+"Z").valueOf()>Date.now()+50000) return 0; //if it's just been set then quit
  console.log(`lifter has 1 minute to submit next attempt`);
  let ts=new Date(Date.now().valueOf()+(60000)-offset+500).toISOString(); //the extra 500 miliseconds are in there cause SQL doesn't work in miliseconds. Alternatively i could round it
  timeString=ts.slice(0,10)+" "+ts.slice(11,19);
  try {saveCompStatus("timeTwo",timeString)} catch {};
} //end funtion nextAttemptTimer

function doBarLoaded() { //sets the barLoaded timer
  if (parseInt(blt.dataset.targettime)) {clearBarLoaded(); clearT3(); return 0}; //note this also works for T3 when there's a break timer!
  console.log(`Bar Loaded for ${lifters[lifters.findIndex(e => e.idx==compStatus.activeLifter)].name}`);
  let ts=new Date(Date.now().valueOf()+(60000)-offset+500).toISOString(); //the extra 500 miliseconds are in there cause SQL doesn't work in miliseconds. Alternatively i could round it
  timeString=ts.slice(0,10)+" "+ts.slice(11,19);
  fetch ("clearLights.php");
  saveCompStatus("timeTo",timeString);
} //end function doBarLoaded

function clearBarLoaded() { //clear the bar loaded timer
  console.log("Bar Loaded cleared");
  compStatus.timeTo=0;
  saveCompStatus("timeTo","0");
} //end fucntion clear bar loaded

function clearNextAttempt() { //clear next attempt timer
  console.log("Next attempt entry timer cleared");
  compStatus.timeTwo=0;
  saveCompStatus("timeTwo","0");
} //end fucntion clear Next Attempt

function clearT3() { //clear break timer
  console.log("Break timer cleared");
  compStatus.timeThree=0;
  saveCompStatus("timeThree","0");
} //end function clearT3

function doAutoRef(l) {
  if (lockout>Date.now()) return -1; //if we're still locked out don't progress
  lockout=Date.now()+15000; //can't do this again for 15 seconds
  if (l.some(x=>x==0)) return -1; // idiot check if some lights are off

  if (l.filter(x => x>1).length>=2) { //if two or more lights are red
    setTimeout(doNoLift,500); //no lift
  } else { //else if only one light is red
    setTimeout(doGoodLift,500); //good lift
  }

} //end function doAutoRef
