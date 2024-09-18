function recalculateLifter(i) { //recalculates the dynamic properties on a lifter at array index i
  let l=lifters[i];
  let s=[0];
  let b=[0];
  let d=[0];
  // need to calculate age group, division, best lifts, subtotals, points, etc after a change or load

  //age group - default is Open
  l["agediv"]=getAgeGroup(l["year"]);

  //division - default is M-CL-PL
  l["gender"] = l["gender"] || "M";
  l["gear"] = l["gear"] || "CL";
  l["lifts"] = l["lifts"] || "PL";
  l["division"]=l["gender"]+"-"+l["gear"]+"-"+l["lifts"];

  //best lifts
  for (let i=1;i<=3;i++) {
   s[i]= l["sa"+i]>0 ? l["sq"+i] :0; //best
   b[i]= l["ba"+i]>0 ? l["bp"+i] :0; //best
   d[i]= l["da"+i]>0 ? l["dl"+i] :0; //best
  }
  l["bsq"]=Math.max(...s);
  l["bbp"]=Math.max(...b);
  l["bdl"]=Math.max(...d);
  
  //subtotals
  l["st"]=l["bsq"]+l["bbp"];
  l["total"]=hasBombed(l) ? -1 : l["st"]+l["bdl"];

  //points
  l["pt"]=getPoints(l["gender"],l["gear"],l["lifts"],l["bw"],l["total"]);


  l["wc"]=l.wc;

  l["place"]=getPlace(l); //note we pass the lifter index for this one

  let row=table.children[i+1];
  //update the updatable divs

  if (row) cols.forEach((elem,c) =>  { //iterate through the cols array
    row.children[c].innerHTML=lifters[i][elem]; //set the div to the array value
    if (elem=="total" && lifters[i].total==-1) row.children[c].innerHTML="DSQ";
    if (elem=="year" && yearOfBirth) row.children[c].innerHTML=parseInt(lifters[i][elem]);
    if (elem=="wc" && lifters[i].wc==1000) row.children[c].innerHTML=bw[lifters[i].gender].slice(-2)[0]+"+";
  });




} //end function recalculateLifter


function getAgeGroup(dob) {
// note this function depends on the ruleset: IPF rules use age they're turning that year so only needs year of birth
// or other feds use DOB (which is worse from a privacy perspective)
  if (yearOfBirth) dob=parseInt(dob)+"-01-01";
  dob = dob || "1998-01-01"; //this should work for 15 years!!
  const age = Math.floor((Date.now()-Date.parse(dob)) / (1000*60*60*24*365));
  return ages.
    filter(function (a) {
      return age >= a.min && age <=a.max;
    }).
    map(function (a) {
      return a.desc || "Open";
    })[0];
} //end function get age group

function setWeightClass(d) {
  d.classList.remove("error");
  let idx=d.parentNode.dataset.lifter;
  let elementIndex=lifters.findIndex(e => e.idx==idx);
  let l=lifters[elementIndex];
  wc=bw[l["gender"]].find(el => el >= l["bw"]);
  if (bw[l["gender"]].findIndex(el => el ==wc)==0) { //if it's the first weight only for sub juniors in IPF rules
    let age=Math.floor((new Date() - Date.parse(l.year))/1000/365/24/60/60)
    if (age>sjronly) wc=bw[l.gender][1];
  }
  t= (wc==1000) ? bw[l.gender].slice(-2)[0]+"+" : wc;
  d.innerHTML= t;
  lifters[elementIndex].wc=wc;
  save(lifters[elementIndex],"wc");
}

function hasBombed(l) {
  if (!l) return false;
  if (l.sa1+l.sa2+l.sa3==-3 || l.ba1+l.ba2+l.ba3==-3 || l.da1+l.da2+l.da3==-3) return true;
  return false;
}


function getPlace(x) {
  let oldplaces=globalLifters.map(e=>e.place);

  globalLifters.forEach((l,c) => {

    let cat=l.division+l.wc+l.agediv;
    let t=l.total;
    let bw=l.bw;

    if (t<=0) {l.place=0;return 0;};

    let tpl=[];
    globalLifters.forEach((e,c) => {
      let ccat=e.division+e.wc+e.agediv;
      let ct=e.total;
      if (ccat==cat && c!=l) 
       if (ct>t || (ct==t && e.bw<bw)) tpl.push(c);
    });
    l.place=tpl.length+1 ||0;
    l.teampoints=1;
    if (l.place<=9) l.teampoints=teampoints[l.place-1];

  });
  savePlaces(oldplaces);
}

