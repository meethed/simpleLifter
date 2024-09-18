var focusElementText="";
const cols=["gp","lot","name","team","year","agediv","division","bw","wc","sr","br","sq1","sq2","sq3","bsq","bp1","bp2","bp3","bbp","st","dl1","dl2","dl3","bdl","total","pt"]; //all columns in the table and their DB equivalent column
const attempts=["sq1","sq2","sq3","bp1","bp2","bp3","dl1","dl2","dl3"]; //array of attempts only
const table=document.getElementById("comp-table"); //table reference 
var gps=[]; //dynamic array of groups for sorting


function addLifterRow(i) { //add a row by cloning the template. Columns are filled by matching with the cols array above
  let l=lifters[i];
  const template=document.getElementById("templateLifterRow").cloneNode(true); //clone template
  template.classList.add("lifter"); //set to lifter (do this now so the template isn't deleted when we select all lifter class)
  template.id="l"+l["idx"]; //make sure it's not called template
  template.dataset.lifter=l["idx"];
  template.children[2].title=l["idx"];
  cols.forEach((e,i) => { //iterate through the cols array
    template.children[i].innerHTML=l[e]; //set the div to the array value
    if (l[e]==-1 && cols[i]=="total") template.children[i].innerHTML="DSQ";
    if (template.children[i].contentEditable) { //if editable set focusin and out events
      template.children[i].addEventListener("focusin",setDivUpdate);
      template.children[i].addEventListener("focusout",updateFromDiv);
      if (template.children[i].classList.contains("attempt")) template.children[i].addEventListener("dblclick",divDblClick);
    }
  });

  document.getElementById("comp-table").appendChild(template); //add row to the table
} //end function addLifterRow

function sortAndSet(lotonly=0) { //fixes the table formatting and generates the lifting order
  //ensure the div colours are right
  setRedGreen();

  //get all of the groups
  gps=[];
  lifters.forEach(e => {
    gps.push(e.gp);
  });
  gps=gps.filter((e,i) => gps.indexOf(e) === i);
  selectgp.innerHTML="";
  gps.forEach(e => {
  g=document.createElement("option");
  g.value=e;g.innerHTML=e;
  selectgp.appendChild(g);
  })
  if (!config.countup) {gps.sort().reverse() ;} else {gps.sort();};

  //sort the lifting order 
  if (!lotonly) {lifters.sort(sortFn)} else {
  lifters.sort((a,b) => {if (a.lot>b.lot) return 1;if (a.lot<b.lot) return -1; if (a.lot==b.lot) return 0;})};

  //sort the table
  lifters.forEach((e,i) => {
    lifters[i].liftidx=i;
    let r=document.getElementById("l"+e.idx);
    table.append(r);
  });

  //save the lfiter order
  saveDisplayOrder();

  checkFedRules();
  drawStatus(); //update the status window

} //end function sortAndSet

function sortFn(a,b) {  //sort function

  let ga,gb,aa,ab,la,lb,na,nb;
  // first up is by Group (active group on top, next group below them)
  // second (if equal group) is current attempt in rising bar
  // third (if equal attempt) is lot number (lower lot number goes first)
  // fourth ((if halfway through the round) is to sort lifters by their second attempt

  let aidx=lifters[lifters.findIndex(e => e.idx==compStatus.activeLifter)]["liftidx"] ||0; //this is the current sort position of the active lifter (0 means they're on the top)
  let alift=compStatus.activeLift; //this is the active lift (eg "sq1")
  let agp=gps.indexOf(compStatus.activeGp); //active group
  let nlift=alift;
  let flift=alift.slice(0,2)+"1";
  if (alift[2]<3) nlift=nlift.slice(0,2)+(parseInt(nlift.slice(2))+1)


  ga=gps.indexOf(a.gp||99);
  ga =ga==agp ? -1: ga; //group - active group is always on top not just alphabetical sort
  gb=gps.indexOf((b.gp||99))
  gb = gb==agp ? -1: gb;

  //1. Group
  if (ga>gb) {return 1} else if (ga<gb) {return -1}

  //2. If same group, sort by attempt
  //uses the lifters index to get current attempt if they haven't lifted. If not, grabs the next attempt. if in the other flight, default to 1st round
  if (ga>0) {aa=a[flift]; } else { //first attempt if not in active group
    if (a.liftidx>=aidx) aa=a[alift] ||9000; //if the lifter index is equal to or greater than current, it means they haven't gone yet
    if (a.liftidx<aidx) aa=a[nlift] ? a[nlift]-1000 : 0; //if the lifter index is less than current (ie they've already gone this round), sort by the next attempt (-1000 to ensure it's at the top)
  }
  if (gb>0) {ab=b[flift]; } else { //first attempt if not in active group
    if (b.liftidx>=aidx) ab=b[alift] ||9000; //if the lifter index is equal to or greater than current, it means they haven't gone yet
    if (b.liftidx<aidx) ab=b[nlift] ? b[nlift]-1000: 0; //if the lifter index is less than current (ie they've already gone this round), sort by the next attempt (-1000 to ensure it's at the top)
 }
  if (aa>ab) {return 1} else if (aa<ab) {return -1}

  //3. If equal attempt, sort by lot number
  la=a.lot || 9000; //lot number
  lb=b.lot || 9000;
  if (la>lb) {return 1} else if (la<lb) {return -1}

  return 0


} //end sortFn

function setRedGreen() {
  //set green and red classes
  lifters.forEach((e,i) => {
    var l=e.idx;
    for (var c=1;c<=3;c++) {
      let s=document.querySelector("#l"+l+" > .sq"+c);
      let b=document.querySelector("#l"+l+" > .bp"+c);
      let d=document.querySelector("#l"+l+" > .dl"+c);
      s.classList.remove("goodlift","nolift");
      b.classList.remove("goodlift","nolift");
      d.classList.remove("goodlift","nolift");
      if (e["sa"+c]=="1") s.classList.add("goodlift");
      if (e["ba"+c]=="1") b.classList.add("goodlift");
      if (e["da"+c]=="1") d.classList.add("goodlift");
      if (e["sa"+c]=="-1") s.classList.add("nolift");
      if (e["ba"+c]=="-1") b.classList.add("nolift");
      if (e["da"+c]=="-1") d.classList.add("nolift");
    }
    if (e.gp==compStatus.activeGp) e.isActiveGp=1;
  });
}

function setDivUpdate(e) { //when a contenteditable div is focussed, copy the text into a global variable to track it for changes
  focusElementText=e.target.innerHTML;
} //end function setDivUpdate

function updateFromDiv(e,forced=0) { //updates the lifters array if the div has been edited
  if (e.target.classList.contains("division")) return 0; //put this right up the start as the right click handler for divisions works differently and bubbling is incorect
  let alertmsg=[];
  //note this is also the primary input checking function
  let value=e.target.innerHTML;
  value = value.replace(/(&nbsp;|<([^>]+)>)/ig, "");
  value = value.replace('\'',"&#039;");
  if (e.target.classList.contains("gp")) value=value[0].toUpperCase();
  e.target.innerHTML=value;
  if (value==focusElementText && !forced) {return 0}; //if just clicking or tabbling but not updating

  let row = Array.prototype.indexOf.call(e.target.parentNode.parentNode.children,e.target.parentNode); //get the index
  let idx=e.target.parentNode.dataset.lifter;
  let colDesc=Array.prototype.indexOf.call(e.target.parentNode.children,e.target);
  let col = cols[colDesc];

  //calculate the lifters array index (lifters array is sorted each sortandset)
  let elementIndex=lifters.findIndex(e => e.idx==idx);

  if (col=="year" & isNaN(Date.parse(value))) {
    value=0;
    e.target.innerHTML=0;
  }

  if (!["year","gp","name","team","sr","br"].includes(col)) { //if it isn't group, name, team, squat or bench rack (ie. if it's a numeric only option)
    clearNextAttempt();
    if(isNaN(value)) {
      e.target.classList.add("error"); //if it should be numeric but isn't make it obvious
      value=0;
    } else {
      value=+value;
      e.target.classList.remove("error"); //if it is numeric then clear the error flag
    } //end if it's numeric
    }
    if (col=="bw") { //add this in because BW and WC work differently now
      let wc=lifters[elementIndex].wc;
      if (bw[lifters[elementIndex].gender].find(e =>e>=value) != lifters[elementIndex].wc)
      e.target.parentElement.children[8].classList.add("error");
    }
    if (attempts.includes(col)) { //if it's specifically one of the attempts
      //for all
      if (value % 2.5 != 0) alertmsg.push("* Not divisible by 2.5, only acceptable for record attempts");
      if (col.charAt(2)!="1") { // if it is a second or third attempt
        var ocol=col.slice(0,2)+(parseInt(col.slice(2))-1);
        if (lifters[elementIndex][ocol] > value) alertmsg.push("* Lower than the previous attempt, only acceptable if an error has been made");
      }
      if (value < compStatus.bar) alertmsg.push("* Lower than the weigh of the bar + collars");

    } //end if it's an attempt input
      if (col=="year") { //if they updated the year of birth we need to do some stuff
        if (!value) value=1990;
        if (yearOfBirth) {
          value=parseInt(value)+"-12-31";
        }
      }


  //if we picked up some errors in the validation of the input:
  if (alertmsg.length) {
    alertmsg.unshift("The number you entered *may* not be valid:<br>");
    doAlertBox(alertmsg,1);
  }
  //update the array with the new value
  lifters[elementIndex][col]=value;

  //recalculate bests and totals and points and things
  recalculateLifter(elementIndex);

  //sort the table and ensure colour coding is correct
  sortAndSet();

  //save here - note the lifting order isn't saved, as that will update ever single cell in the database. only the raw information is.
  elementIndex=lifters.findIndex(e => e.idx==idx); //we have to run this again as the sortAndSet will sort the array
  save(lifters[elementIndex]);

} //end function updateFromDiv


function divDblClick(e) { //if we double click on an editable div, do something

  let d=e.target;
  let colDesc=Array.prototype.indexOf.call(d.parentNode.children,d);
  let col = cols[colDesc];
  col = col[0]+"a"+col[2]; //change sq->sa, bp->ba, dl->da
  //calculate the lifters array index (lifters array is static, whilst the divs are sorted)
  let idx=d.parentNode.dataset.lifter;
  let elementIndex=lifters.findIndex(e => e.idx==idx);

  if (d.classList.contains("attempt")) {

    if (d.classList.contains("goodlift")) {
      lifters[elementIndex][col]=-1;
    } else if (d.classList.contains("nolift")) {
      lifters[elementIndex][col]=0;
    } else {
      lifters[elementIndex][col]=1;
    }
  }
    d.classList.remove("goodlift");
    d.classList.remove("nolift");

  switch (lifters[elementIndex][col]) {
    case 1:
      d.classList.add("goodlift");
    break
    case 0:
    break
    case -1:
      d.classList.add("nolift");
    break
  }
  updateFromDiv(e,true);
} //end function divDblClick



function setActiveLifter(idx) { //sets the active lifter - lifters, css and DB
  setActive(idx,compStatus.activeLift);
} //end function setActiveLifter

function setActiveLift(lift) { //sets the active column - lifters, css and DB
  let first=firstFrom(compStatus.activeGp,lift);
  setActive(first,lift)
} //end function setActiveLift


function setActiveGroup(g) { //sets the active group. var g is a string
  let first=firstFrom(g,compStatus.activeLift);
  setActive(first,compStatus.activeLift);
} //end function setActiveGroup


function nextFrom(idx,lift) { //This function just spits out the next lifter based on a given start point. If the lift is blank it won't skip it. The parent function needs to do that. If you want second/third, try again until you get what you want!
  //returns [lifterRow, lift]
  let lifterRow=lifters.findIndex(e => e.idx==idx);
  let g=lifters[lifterRow].gp;

  let returns={};
  //first up see if there's someone after us in this group
  let a=lifters.filter(e=> e.gp ==g).sort((a,b) => {return (a[lift]||9000)+(a.lot/1000)>(b[lift]||9000)+(b.lot/1000)});
  let i=a.indexOf(lifters[lifterRow])

  if (i!=a.length-1) { //if it's NOT the last one in this group
    returns= {"idx":lifters[lifters.indexOf(a[i+1])].idx,"lift":lift}; //note we can't refer to lifters[row+1] as that's sorted based on the current status. This function is current status agnostic.
  } //end if we're progressing in the group

  else if (lift.charAt(2)!=3) { //if it's not the third lift go back to the top for the second or third round
    let newlift=lift.slice(0,2)+(parseInt(lift.charAt(2))+1);
    returns= {"idx":firstFrom(g,newlift),"lift":newlift}; //firstFrom is easy
  }
  else { //else if it IS the third round then we need to change groups
    if (gps.indexOf(g)<gps.length-1) { //if it's the third round but not the last group then increment group and back to the start
      let newlift=lift.slice(0,2)+1; //sq1/bp1/dl1
      let newgp=gps.indexOf(g)+1;
      returns= {"idx":firstFrom(gps[newgp],newlift),"lift":newlift};
    } else if (gps.indexOf(g)==gps.length-1) { // if it's the third round AND the last group, then it must be the next lift (bp1,dl1)
      if (lift=="sq3") {newlift="bp1"; nlr=firstFrom(gps[0],newlift);}
      if (lift=="bp3") {newlift="dl1"; nlr=firstFrom(gps[0],newlift);}
      if (lift=="dl3") {newlift="res"; nlr=compStatus.activeLifter;} //end of the competition
      returns={"idx":nlr,"lift":newlift};

      return returns; //skip the iterative bit 
    }
  }

  i=lifters.findIndex(e => e.idx==returns.idx);
  if (!lifters[i][returns.lift]>0 && lift.charAt(0)!="r") {returns=nextFrom(returns.idx,returns.lift)};
  return returns;
} //end iterative function nextFrom

function firstFrom(group,lift) {
  //if any group has only one lifter, then it will struggle. First we need to check this and bump out otherwise it'll fail.
  let a=lifters.filter(e => e.gp==group).sort((a,b) => {return (a[lift]||9000)+(a.lot/1000)>(b[lift]||9000)+(b.lot/1000)})[0];
  return lifters[lifters.indexOf(a)].idx;
} //end function firstFrom


function setNextLifters() {
  let ai=compStatus.activeLifter;
  let l=lifters.findIndex(e => e.idx==ai);
  let ag=lifters[l].gp;
  let al=compStatus.activeLift;
  let nl={}, tl={};;
  nl=nextFrom(ai,al);

  console.log("next "+lifters[lifters.findIndex(e=>e.idx==nl.idx)].name);

  if (compStatus.nextLifter!=nl.idx) {compStatus.nextLifter=nl.idx; saveCompStatus("nextLifter",compStatus.nextLifter)};;
  if (compStatus.nextLiftIs!=nl.lift) {compStatus.nextLiftIs=nl.lift; saveCompStatus("nextLiftIs",compStatus.nextLiftIs)};;

  tl= nextFrom(nl.idx,nl.lift);

  console.log("third "+lifters[lifters.findIndex(e=>e.idx==tl.idx)].name);

  if (compStatus.thirdLifter!=tl.idx) {compStatus.thirdLifter=nl.idx; saveCompStatus("thirdLifter",compStatus.thirdLifter)};;
  if (compStatus.thirdLiftIs!=tl.lift) {compStatus.thirdLiftIs=nl.lift; saveCompStatus("thirdLiftIs",compStatus.thirdLiftIs)};;
} //end function setNextLifters


function nextLifter() { //moves to the next lifter
  let lidx=compStatus.nextLifter;
  let b=0;
  setNextLifters(); //calculate who the next and next next lifters are
  if (compStatus.nextLiftIs=="bp1" && compStatus.activeLift=="sq3") b="b"
  if (compStatus.nextLiftIs=="dl1" && compStatus.activeLift=="bp3") b="d" //this is when we're at the end of a lift. the actual next lifter is in the 3rd position, the next lifter is the break
  if (compStatus.nextLiftIs=="res" && compStatus.activeLift=="dl3") b="r" //results
  if (b && config.autobreaks) doBreak();
  setActive(lidx,compStatus.nextLiftIs); //set the active lifter. note this happens even if there's a break, but the timer will come over the top
  drawStatus(b);
} //end function nextLifter

function drawStatus(b) { //updates the status window
  if (b) {
  switch (b) {
    case "b":
    lifterName.innerHTML="<a onclick='clearT3();setActive(compStatus.activeLifter,compStatus.activeLift); setTimeout(function() {drawStatus(0)},100);'>Click here to start the Bench Press</a>";
    lifterRack.innerHTML="";
    nextName.innerHTML="";
    drawPlates(activePlates,0,0);
    break;
    case "d":
    lifterName.innerHTML="<a onclick='clearT3();setActive(compStatus.activeLifter,compStatus.activeLift); setTimeout(function() {drawStatus(0)},100);'>Click here to start the Deadlift</a>";
    lifterRack.innerHTML="";
    nextName.innerHTML="";
    drawPlates(activePlates,0,0);
    case "r":
    break;
  } } else {
    let activelifter=lifters[lifters.findIndex(e => e.idx==compStatus.activeLifter)] ||0;;
    let nextlifter=lifters[lifters.findIndex(e => e.idx==compStatus.nextLifter)] ||0;
    if (!compStatus.bar) compStatus.bar=25;
    drawPlates(activePlates,activelifter[compStatus.activeLift],compStatus.bar);
    lifterName.innerHTML="";
    lifterRack.innerHTML="";
    nextName.innerHTML="";
    lifterName.innerHTML=`${activelifter.name} - ${activelifter[compStatus.activeLift]}kg`;
    nextName.innerHTML=`Next Lifter:<br>${nextlifter.name} - ${nextlifter[compStatus.nextLiftIs]}kg<br>${getLoad(nextlifter[compStatus.nextLiftIs]-compStatus.bar)}<br>`;
    lifterRack.innerHTML=getLoad(activelifter[compStatus.activeLift]-compStatus.bar)+"<br>";
    if (compStatus.activeLift[0]=="s") {lifterRack.innerHTML+=`Squat Rack: ${activelifter.sr}`;nextName.innerHTML+=`<br>Squat Rack: ${nextlifter.sr}`}
    if (compStatus.activeLift[0]=="b") {lifterRack.innerHTML+=`Bench Rack: ${activelifter.br}`;nextName.innerHTML+=`Bench Rack: ${activelifter.br}`}
//  shrink([lifterName,lifterRack,nextName,nextRack]);
  }
}

function shrink(a,goal) {
  a.forEach(e => e.style.fontSize="64px")
  let epx=64;
  for (let i=epx;i>=0; i--) {
    let overflow = a[0].parentElement.parentElement.scrollHeight > 200;
    if (overflow){
      epx--;
      a.forEach(div => div.style.fontSize=epx+"px");
    }
  }
} //end function shrink


function setActive(idx,lift) { //big sort and set and make active. fuck those subroutines they were not working.

  if (lift===null) lift="sq1";
  let lotonly=0;
  let l=lifters.findIndex(e=>e.idx==idx);
  let g=lifters[l].gp;
  let c=lifters[l][lift]+(lifters[l].lot/1000);
  //step 1. update the server

  if (compStatus.activeLifter!=idx) {compStatus.activeLifter=idx;saveCompStatus("activeLifter",idx)};
  if (compStatus.activeLift!=lift) {compStatus.activeLift=lift;saveCompStatus("activeLift",lift)};
  if (compStatus.activeGp!=g) {compStatus.activeGp=g; saveCompStatus("activeGp",g)};

  //step 2. set the competition status and group array

  lifters.forEach(e => e.isActive=0);
  lifters[l].isActive=1;
  gps=lifters.map(e=>e.gp);
  gps=gps.filter((e,i) => gps.indexOf(e) === i);

  selectgp.innerHTML="";
  gps.forEach(e => {
    gpo=document.createElement("option");
    gpo.value=e;gpo.innerHTML=e;
    selectgp.appendChild(gpo);
  })
  if (!config.countup) {gps.sort().reverse() ;} else {gps.sort();}; //sort the groups by if we go A-Z or Z-A

  //step 2. sort the lifter array and the divs

  if (lotonly) {lifters.sort((a,b) => {if (a.lot>b.lot) return 1;if (a.lot<b.lot) return -1; if (a.lot==b.lot) return 0;})}
  else {lifters.sort((a,b) => {
    let aa,bb;

    //1. group
    gg=gps.indexOf(g);
    aa=gps.indexOf(a.gp||99);
    aa=aa===gg ? -1: aa;
    bb=gps.indexOf(b.gp||99);
    bb=bb===gg ? -1 : bb;
    if (aa>bb) {return 1} else if (bb>aa) {return -1}
    let nl, arr,narr;
    //2. same group
    if (lift.charAt(2)!=3) {
      nl=lift.slice(0,2)+(parseInt(lift.charAt(2))+1); //get the next lift
    } else {nl=lift};

    la = (a.gp==g) ? lift : lift.slice(0,2)+"1";
    lb = (b.gp==g) ? lift : lift.slice(0,2)+"1";
    arr=lifters.filter(e=> e.gp ==a.gp).sort((a,b) => {return (a[la]||9000)+(a.lot/1000)>(b[la]||9000)+(b.lot/1000)}); //get an array of the lifting order
    narr=lifters.filter(e=> e.gp ==a.gp).sort((a,b) => {return (a[nl]||9000)+(a.lot/1000)>(b[nl]||9000)+(b.lot/1000)}); //get an array of the lifting order
    aa=(arr[arr.indexOf(a)][la]||9000)+a.lot/1000;
    bb=(arr[arr.indexOf(b)][lb]||9000)+b.lot/1000;

    //but if they're ahead of the active lifter, we need to put them at the top, sorted by the next lift
    if (a.gp==g) {
      if (aa<c) aa=(narr[narr.indexOf(a)][nl]-1000)||9000;
      if (bb<c) bb=(narr[narr.indexOf(b)][nl]-1000)||9000;
    }
    if (aa>bb) {return 1} else if (bb>aa) {return -1};

    return 0
  })}

  //step 3. set the active lift column & active group
  if (lift=="wei") {compStatus.activeLift="sq1";sortAndSet(1);document.querySelector("select.btn").value=lift;return 0}
  if (lift=="res") {generateResults();showResults();return 0}
  hideResults();
  table.querySelectorAll(".td.activeCol").forEach((e) => e.classList.remove("activeCol"));
  table.querySelectorAll(".td."+lift).forEach((e) => e.classList.add("activeCol"));
  document.querySelector("select.btn").value=lift;

  table.querySelectorAll(".td.g").forEach((e) => {
    if (e.innerHTML==g) {e.classList.add("active");
    } else {e.classList.remove("active")};
  });

  //step 4. set the active lifter column
  lifters.forEach((e) => e.isActive=false); // none are active
  table.querySelectorAll(".tr.active").forEach((e) => e.classList.remove("active")); // clear the CSS
  newlifter=lifters[lifters.findIndex(e => e.idx==idx)]; //redo this as the lifter position will have moved after the sort
  newlifter.isActive=true;
  table.querySelector(`[data-lifter="${idx}"]`).classList.add("active");

  //step 5. update the displays
  setNextLifters(); //set the second and third up and save them for the server
  drawStatus();
  selectgp.value=compStatus.activeGp;
  selectlift.value=compStatus.activeLift;  

  //sort the table
  lifters.forEach((e,i) => {
    lifters[i].liftidx=i;
    let r=document.getElementById("l"+e.idx);
    table.append(r);
  });




}

