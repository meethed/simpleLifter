table.addEventListener("keydown", (e) => { //catch any time a key is presesd in the comp table
  switch (e.key) {
    case "Enter":
    case "ArrowDown":
      e.preventDefault();
      move(e.target,"d");
      break;
    case "Tab":
      e.preventDefault();
      x= e.shiftKey ? move(e.target,"l") : move(e.target,"r");
      break;
    case "ArrowRight":
      e.preventDefault();
      move(e.target,"r");
      break;
    case "ArrowLeft":
      e.preventDefault();
      move(e.target,"l");
      break;
    case "ArrowUp":
      e.preventDefault();
      move(e.target,"u");
      break;
    default:
  }

}); //end table keydown event listener


function move(t,d) { //t=target, d is direction
  let c=0,r=0;
  switch (d) {
  case "u": r=-1;break;
  case "d": r=1; break;
  case "l": c=-1;break;
  case "r": c=1; break;
  }
  let col=Array.from(t.parentNode.children).indexOf(t)+c;
  let row=Array.from(t.parentNode.parentNode.children).indexOf(t.parentNode)+r;
  if (row==table.children.length) row=1;
  if (row==0) row=table.children.length-1;
  if (col<0) col=table.children[1].children.length-1;
  if (col==table.children[1].children.length) col=0;
  //recursion lulz
  let newt=table.children[row].children[col];
  if (newt.contentEditable!="true") {move(newt,d);}
  //select and focus
  let rg=document.createRange();
  rg.selectNodeContents(newt);
  let sl=window.getSelection();
  sl.removeAllRanges();
  sl.addRange(rg);
  newt.focus();
  newt.focus();
} //end function move

function setMeActiveLifter(e) {
  setActiveLifter(lifters[Array.from(e.parentNode.parentNode.children).indexOf(e.parentNode)-1].idx);
} //end function setMeActiveLifter

function setupTableRightClick() {
document.querySelectorAll(".td.division").forEach(element => element.addEventListener("contextmenu", (e) =>{
  e.preventDefault();
  e.target.contentEditable=true;
  e.target.style.background="#fff";
  e.target.addEventListener("focusout", (f) => {
    f.target.contentEditable=false;
    f.target.style.background="";
    let d=f.target.innerHTML.toUpperCase().split("-");
    let r=Array.from(f.target.parentNode.parentNode.children).indexOf(f.target.parentNode)-1;
    if (d.length!=3) {f.target.innerHTML="M-CL-PL";d=["M","CL","PL"];}
    lifters[r].gender=d[0];
    lifters[r].gear=d[1];
    lifters[r].lifts=d[2];
    lifters[r].division=f.target.innerHTML;
    save(lifters[r],["gender","gear","lifts"]);
  })
})
)

} //end function
