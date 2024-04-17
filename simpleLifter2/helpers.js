alertBoxOk.addEventListener("click",(e => { //ok button
  e.preventDefault();
  a=document.getElementById("twizzler");
  rv= (a) ? a.value:"ok";
  alertBox.close(rv);
  //do something depending on what type of box it was
}));

alertBox.addEventListener("close",(e => { //cancel button goes here also
  // do nothing - usually!
  let a=alertBox.returnValue;
  alertBoxContainer.innerHTML=""; //tidy up the extra bits

  if (!["ok","default","cancel"].includes(a)) { //if we return something it must be a break timer (that's all we're set up for)
    console.log(a);
    breakTimer(a);
  }
}));

function doAlertBox(s,b) { //[s]tring in message, [b]uttons to display
  switch (b) {
    case 1: 
      alertBoxCx.style.display="none";
    break
    case 0:
    default:
      alertBoxCx.style.display="";
    break;
  }
  alertBoxText.innerHTML=Array.isArray(s) ? s.join("<br>") : s;
  alertBox.showModal();
} //end function doAlertBox

function doGoodLift() { //good lift button click handler
  let i=lifters.findIndex(e => e.idx==compStatus.activeLifter);
  let l=lifters[i];
  let a=compStatus.activeLift[0]+"a"+compStatus.activeLift[2];
  l[a]=1;
  recalculateLifter(i);
  save(lifters[i],a)
  nextLifter();
} //end function doGoodLift

function doNoLift() { // no lift button click handler
  let i=lifters.findIndex(e => e.idx==compStatus.activeLifter);
  let l=lifters[i];
  let a=compStatus.activeLift[0]+"a"+compStatus.activeLift[2];
  l[a]=-1;
  recalculateLifter(i);
  save(lifters[i],a)
  nextLifter();
} //end function doNoLift

function changeSession() { //change sesison

} //end function change Session



/* this is the really big and important status update function
it will load the comp status and check for updates. right now it just does lights and timers
but it could be used for 'slave' tables if someone wanted to run a comp with two simplelifters
*/

function tick() { //tick function - checks the status for updates

  fetch("./simpleLifter2/loadsetup.php").then(response => response.json()).then(json => {
    if (compStatus.updated!=json.updated) { //if nothing has changed then don't bother
      let lightson=parseInt(json.l)+parseInt(json.c)+parseInt(json.r); //lights are on if >0
      let l=drawLights(lightsBox,[json.l,json.c,json.r]);
        if (l==1) clear=setTimeout(() => {
        lightsTimers.classList.remove("visible");
        drawLights(lightsBox,[0,0,0]);
        fetch("clearLights.php");
      },5000);


      //timers
      let a=0;

      //timer 1
      if (compStatus.timeTo!=json.timeTo) {
        let t1=new Date(convertTime(json.timeTo)).valueOf() ||0;
        a+=setTimer(blt,t1);
      }
      //timer 2
      if (compStatus.timeTwo!=json.timeTwo) {
        let t2=new Date(convertTime(json.timeTwo)).valueOf() || 0;
        a+=setTimer(nast,t2); //next attempt submission timer
      }
      //timer 3
      if (compStatus.timeThree!=json.timeThree) {
        let t3=new Date(convertTime(json.timeThree)).valueOf() || 0;
        a+=setTimer(blt,t3);
      }

      compStatus=json; //do this last so we can compare
    }

  });

  nextTick=setTimeout(tick,1000);
} //end function tick


function doBreakTimer(s,v) {
  //clone of the doAlertBox code but with a scroller
  twizzler=document.createElement("input");
  twizzler.type="number";
  twizzler.max="30";
  twizzler.min="0";
  twizzler.value = v>0 ? v : 0;
  twizzler.id="twizzler";
  twizzler.addEventListener("change", (e => {alertBoxOk.value=twizzler.value}));
  alertBoxContainer.append(twizzler);
  doAlertBox(s>"" ? s : "Select the time for the break:",0);
}


function convertHTML(str) {
  let replacements = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",//THIS PROBLEM ME NO MORE THANKS TO ieahleen
    "'": "&apos;",
    "<>": "&lt;&gt;",
  }
  return str.replace(/(&|<|>|"|'|<>)/gi, function(noe) {
    return replacements[noe];
  });
} //end function
