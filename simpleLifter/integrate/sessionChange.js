const activeComps=[]; //global scope

function doSessionChange() {
//TODO - all of it

//get list of active comps

fetch("../../getcomps.php")
 .then(x => x.text())
 .then(y => {

 y = y.replace(/\n/g, '').split(",").slice(0,-1);
 var i=0,c=0;
 for (i=0;i<y.length;i+=2){
  activeComps[y[i+1]]=y[i];
  c++;
 }

//cool we have all of the active comps

var accessCode,
    compLetters;

//make a form

var frm = document.createElement("form");
popupBox.prepend(frm);


//show dialog with dropdown of active comps
var box,btnOk,msg;
  document.getElementById("msg").innerHTML=""; 

  //create a new GO button to go with CX
  var goBtn = document.createElement("div");
  goBtn.classList.add("btn");
  goBtn.innerHTML="Go!";
  frm.append(goBtn);

  //create the password input (using prepend do it first)
  var accessInput=document.createElement("input");
  accessInput.placeholder="Insert Access Code";
  accessInput.type="password";
  accessInput.name="pwd";
  frm.prepend(accessInput);

  //create the selector
  var selector = document.createElement("select");
  selector.name="compName";
  frm.prepend(selector);

  //create the selector options
  
  Object.keys(activeComps).forEach(k => {
  var option=document.createElement("option");
  option.value=k;
  option.innerHTML=activeComps[k];
  selector.append(option);
  });

  document.getElementById("popupBox").style.display="block";

  //add a label
  var lab=document.createElement("div");
  lab.innerHTML="<p>Select the competition, input the access code, and then press 'go'. If you don't wanna do this, press 'close'</p>";
  frm.prepend(lab);

//validate  session

goBtn.addEventListener("click",function() {

var compNameTry="",compPWDTry="";

const frmData = new FormData(frm);
fetch("switchComps.php", {
  method: "POST",
  body: frmData, })
 .then(response =>
  response.text()).then(d => {;
console.log(d); //just in case

d=d.replace(/(\r\n|\n|\r)/gm,"").trim();
  if (d==selector.value) {
  compName=selector.value;
  fullComp=selector.options[selector.selectedIndex].text;

  var a=document.querySelectorAll(".tr");
  var i; const l=a.length;
  for (i=1;i<l;i++) {a[i].remove();}
  
  setup = new Setup();
  lifters = new Lifters(0);
  lifters.loadLocal();
  lifters.doSort();
 } //if it's correct
});

frm.innerHTML="";
popupBox.removeChild(frm);
popupBox.style="display:none";
 });
//reload
}); //end of the fetch -  none of this happens without the fetch

}
