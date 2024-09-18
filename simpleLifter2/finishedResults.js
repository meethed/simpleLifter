let headings={"gp":"Group","lot":"Lot","name":"Lifter Name","team":"Team","agediv":"Age Group","bw":"Weight","wc":"Weight Class","division":"Division","sq1":"SQ1","sq2":"SQ2","sq3":"SQ3","bsq":"Best SQ","bp1":"BP1","bp2":"BP2","bp3":"BP3","bbp":"Best Bench","dl1":"DL1","dl2":"DL2","dl3":"DL3","bdl":"Best Dead","total":"Total","teampoints":"Points","session":"Session","pt":"IPF Points","place":"Place","st":"Subtotal"};
function generateResults() { //generates the results table (for ALL lifters)
  //note globalLifters is already updated! The array is full of objects, so when pushed into
  //lifters (for the particular session), any updates there will update the globalLifters 
  //array also!!
  let resultsTable=document.getElementById("resultsTable");
  resultsTable.innerHTML="";
  globalLifters.forEach((e,i) => e.place = getPlace(e));
  let lifterResults = JSON.parse(JSON.stringify(globalLifters))
   lifterResults.forEach((e,i) => {
    e.cat=e.division+e.agediv+e.wc; //this is just temporary to generate the category for sorting
  })
  lifterResults.sort((a,b) => {

  //sort by gender, gear and lifts (division)
  let ad=a.division || "";
  let bd=b.division || "";
  if (ad>bd) return 1
  if (ad<bd) return -1

    //sort by age division
    let aa=a.agediv || "Open";
    let ba=b.agediv || "Open";
    if (aa!=ba) {
      let ay=a.year ? a.year : 1990;
      let by=b.year ? b.year : 1990;
      return (by-ay)
    }
     //sort by weight class
      let aw=a.wc || 0;
      let bw=b.wc || 0;
      aw=(aw.toString().at(-1)=="+") ? 1000 : aw
      bw=(bw.toString().at(-1)=="+") ? 1000 : bw

      if (aw>bw) return 1
      if (aw<bw) return -1

        //finally, sort by place
        let ap=a.place || 1000;
        let bp=b.place || 1000;
        if (ap>bp) return 1
        if (ap<bp) return -1

  return 0;
  });

  //generate header row
  hr=document.createElement("tr");
  resultsTable.appendChild(hr);
  for (const [key, value] of Object.entries(lifterResults[0])) {
    let th=document.createElement("th");
    th.innerHTML=headings[key] || key;
    th.className="results "+ key;
    hr.appendChild(th);
  };
  let gcat="";

  //iterate through each lifter to generate the table row
  lifterResults.forEach((e,i) => {
    let lr=document.createElement("tr");
    for (const [key, value] of Object.entries(e)) {
      if (key!="isActiveGp"){
      let td=document.createElement("td");
      td.innerHTML=value;
      if (key=="total" && value==-1) td.innerHTML="DSQ";
      if (key=="wc" && value==1000) td.innerHTML=bw[e.gender].slice(-2)[0]+"+";
      td.id="results"+key+"lifter"+i;
      td.className="results "+key;
      lr.appendChild(td);
    }
    };
    resultsTable.appendChild(lr);
  });

  //display
  document.body.appendChild(resultsTable);


  //clean up each lifter
  lifterResults.forEach((e,i) => {
    for (const [key, value] of Object.entries(e)) {
    if (["sa1","sa2","sa3","ba1","ba2","ba3","da1","da2","da3"].includes(key)) {
      let c="";
      if (key[0]=="s") c="sq";
      if (key[0]=="b") c="bp";
      if (key[0]=="d") c="dl";
      let cs="results"+c+key[2]+"lifter"+i;
      let cell=document.getElementById(cs);
      if (value==1) {cell.style.background="green";}
      if (value==-1) {cell.style.background="red";cell.innerHTML+="x";}
    } //end green red filter
    } //end key/value loop
  }) //end row foreach


  //hide the columns we don't care about
  h=document.querySelectorAll(".results.gp,.results.lot,.results.session,.results.cat,results.session,.results.year,.results.gender,.results.gear,.results.lifts,.results.sr,.results.br,.results.idx,.results.formula,.results.sa1,.results.sa2,.results.sa3,.results.ba1,.results.ba2,.results.ba3,.results.da1,.results.da2,.results.da3,.results.lighthistory,.results.pbs,.results.pbb,.results.pbd,.results.pbt,.results.isActive,.results.liftidx");
  h.forEach(e => e.style.display="none");

  //add a header row for the categories
  let cc="";
  let grc=1;
  lifterResults.forEach((e,i) => {
    if (e.cat!=cc) { // if it's a new cat
      cc=e.cat;
      let gr=document.createElement("tr");
      let gd=document.createElement("td");
      wc = (e.wc==1000) ? bw[e.gender].slice(-2)[0]+"+" : e.wc;
      gd.innerHTML=e.division+" - " + e.agediv + " - " + wc +"kg";
      gd.classList.add("heading-division");
      gd.id=cc;
      gd.colSpan="100";
      gr.appendChild(gd);
      resultsTable.insertBefore(gr,resultsTable.children[i+grc]);
      grc+=1;
    } //loop
  });

  a=document.createElement("a");
  a.href="./results.php?c="+compLetters;
  a.innerHTML="Click here for full results in OPL/csv format<br><br>";
  resultsTable.appendChild(a);

} //end function generate results


function showResults() { //shows the results table in place of the current results
resultsTable.style.display="";
} //end function show Results

function hideResults() { //hides the results table and back to the comp spreadsheet
resultsTable.style.display="none";
} //end function hide Results
