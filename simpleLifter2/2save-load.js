function getStatus() {
  fetch("./simpleLifter2/loadsetup.php").then(response => response.json()).then(json => {
    compStatus=json;
    if (compStatus!=[]) {
      console.log("loaded setup info"); 
      if (compStatus.activeLift==null) compStatus.activeLift="sq1";
      if (compStatus.activeGp==null) compStatus.activeGp="A";
    } else { //end if successful fetch
      console.log("error loading setup info");
    }
  });

} //end function loadsetup

function loadfromserver() {
  if (compStatus==[]) {
    getStatus();
    setTimeout(loadfromserver,1000);
    return -1;
  }
  fetch("./simpleLifter2/load.php").then(response => response.json()).then(json => {
    globalLifters=json;
    if (globalLifters!=[]) {
      lifters=[];
      console.log("loaded data");
      globalLifters.forEach(l => {if (l.session==globalSession) lifters.push(l)});
      document.querySelectorAll(".tr.lifter").forEach(e => e.remove());
      lifters.forEach((l,i) => {
          addLifterRow(i);
          l=recalculateLifter(i);
      });

      if (lifters.findIndex(e=>e.idx==compStatus.activeLifter)==-1) compStatus.activeLifter=lifters[0].idx;
      //sort the table and set the active lifter, active group and active lift
      sortAndSet();
      //set active Lift (column)
      setActiveLift(compStatus.activeLift);
      //set active Lifter (row)
      setActiveLifter(compStatus.activeLifter);
      //set active Group (row cluster)
      setActiveGroup(compStatus.activeGp);
      //make sure the bar is right
      checkFedRules();
    } //end if successful fetch
  });
document.getElementById("loading").classList.remove("loading");
setTimeout(setupTableRightClick,500);
} //end function loadfromserver


function replacer(k,v) {
 if (k=="isActiveGp") return undefined;
 return v;
}

function save(l) { //save lifter l to server

  //nah fuck that send the whole thing
  l = JSON.stringify(l,replacer);
  fetch("./simpleLifter2/save.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',
      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => console.log(res)); //"saved lifter"));

} //end function save


function addlifter(l) { //add lifter l to server
  l = JSON.stringify(l);
  fetch("./simpleLifter2/newlifter.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',
      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => console.log("lifter added"));

} //end function addlifter

function addFromBtn() {
  let s= compStatus.session;
  addlifter({"session":s});
  loadfromserver();  
}


function saveCompStatus(s,d) { //sets column S equal to data D for session compLetters
  l = `{"${s}":"${d}"}`;;
  fetch("./simpleLifter2/savecs.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',

      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => console.log(`competition status updated`));

} //end function saveCompStatus


function savePlaces(o) { //globalLifters iterator to save places (noting that some divisions may be across groups/sessions )
  kk="";
  globalLifters.forEach((e,i) => {
  if (e.place!=o[i]) {
  l = JSON.stringify(e,["idx","place"]);
  fetch("./simpleLifter2/save.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',

      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => {});
  }
});
}


function saveDisplayOrder() {
  kk="";
  globalLifters.forEach((e,i) => {
  l = JSON.stringify(e,["idx","liftidx"]);
  fetch("./simpleLifter2/save.php", {
    method: 'POST',
    headers: {
      'Accept': 'application/json, text/plain',

      'Content-Type': 'application/json'
    },
    body: l
  }).then(res => res.text())
  .then(res => {});
});

}
