var divisions=["M-CL-PL", "F-CL-PL","M-EQ-PL","F-EQ-PL","M-CL-BP", "F-CL-BP","M-EQ-BP","F-EQ-BP","M-SO-PL","F-SO-PL","M-CR-PL","F-CR-PL","M-CL-DL","F-CL-DL"];
var groups=["A","B","C","D","E","F","G"];
var utils=["Comp Setup...","Save","Load","Generate Results","Change Session...","Help..."];
var lifts=["Weigh In","SQ-1","SQ-2","SQ-3","BP-1","BP-2","BP-3","DL-1","DL-2","DL-3"];
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.innerHTML;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.innerHTML = this.getElementsByTagName("input")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
							moveDown(inp);
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }

}



function showGroups(){
	
	var a,b,val;
	var arr=groups;
	var t=document.getElementById("curGroup");
	a = document.createElement("DIV");
  a.setAttribute("id", t.id + "autocomplete-list");
  a.setAttribute("class", "autocomplete-items");
	t.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        /*create a DIV element for each element:*/
          b = document.createElement("DIV");
          b.innerHTML =  arr[i];
          
          /*insert a input field that will hold the current array item's value:*/
          
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
          /*insert the value for the autocomplete text field:*/
          t.innerHTML = e.target.innerHTML;
					lifters.activeGp=t.innerHTML;
					lifters.doSort();
          /*close the list of autocompleted values,
          (or any other open lists of autocompleted values:*/
          
          });
          a.appendChild(b);
        
     
}	
}

function showLifts(){
	
	var a,b,val;
	var arr=lifts;
	var t=document.getElementById("curLift");
	a = document.createElement("DIV");
  a.setAttribute("id", t.id + "autocomplete-list");
  a.setAttribute("class", "autocomplete-items");
	t.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        /*create a DIV element for each element:*/
          b = document.createElement("DIV");
          b.innerHTML =  arr[i];
          
          /*insert a input field that will hold the current array item's value:*/
          
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
          /*insert the value for the autocomplete text field:*/
          t.innerHTML = e.target.innerHTML;
					//if (e.target.innerHTML=="Weigh In") {document.getElementById("btnNew").style.display="block"} else {document.getElementById("btnNew").style.display="none";};
					lifters.activeLi=t.innerHTML;
					lifters.doSort();
					lifters.activeRow=0; //set it back to the top

          /*close the list of autocompleted values,
          (or any other open lists of autocompleted values:*/
          
          });
          a.appendChild(b);
        
     
}	
}

function showLifters(){
		
	var a,b,val,i,liftlist=[];
	
	for (i=0;i<setup.lifterCount;i++)
		for (a=0;a<setup.lifterCount;a++){
		if (lifters.liftList[a].group==lifters.activeGp)
			if (lifters.liftList[a].sortOrder==i)
		
		liftlist.push(lifters.liftList[a].name);
	};
	
	var arr=liftlist;
	var t=document.getElementById("curLifter");
	a = document.createElement("DIV");
  a.setAttribute("id", t.id + "autocomplete-list");
  a.setAttribute("class", "autocomplete-items");
	t.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        /*create a DIV element for each element:*/
          b = document.createElement("DIV");
          b.innerHTML =  arr[i];
          
          /*insert a input field that will hold the current array item's value:*/
          
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
          /*insert the value for the autocomplete text field:*/
          t.innerHTML = e.target.innerHTML;
					lifters.activeRow=Array.prototype.indexOf.call(a.children,e.target);

          /*close the list of autocompleted values,
          (or any other open lists of autocompleted values:*/
          
          });
          a.appendChild(b);
        
     
}	
} //end function showLifters


function showUtils(){
	
	var a,b,val;
	var arr=utils;
	var t=document.getElementById("btnUtil");
	a = document.createElement("DIV");
  a.setAttribute("id", t.id + "autocomplete-list");
  a.setAttribute("class", "autocomplete-items");
	t.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        /*create a DIV element for each element:*/
          b = document.createElement("DIV");
          b.innerHTML =  arr[i];
          
          /*insert a input field that will hold the current array item's value:*/
          
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
          
						if (e.target.innerHTML=="Save") lifters.saveLocal();
						if (e.target.innerHTML=="Load") lifters.loadLocal();
						if (e.target.innerHTML=="Generate Results") doScoreboard();
						if (e.target.innerHTML=="Comp Setup...") doSetup();
					        if (e.target.innerHTML=="Change Session...") doSessionChange();
						if (e.target.innerHTML=="Help...") doHelp();
						if (e.target.innerHTML=="Hide Results") hideScoreboard();
          });
          a.appendChild(b);
        
     
}	
} //end function showUtils


function doPopup(text){
	var box,btnOk,msg;
	document.getElementById("msg").innerHTML=text;	
	document.getElementById("popupBox").style.display="block";
}
	
function doHelp(){
		lifters.saveLocal();
	doPopup("Welcome to simpleLifter Web v0.9<br>It's like nextLifter and openLifter but it does a few other things and is really easy to use.<br><br>To get started and add lifters, set up your competition using the 'utilities' button, then start entering data. The weigh in and competition all happen from here with no need to change tabs, so if you need to you can edit the data at any time!<br><br>When you're ready, change 'weigh in' (under the lifter details up top) to SQ-1. It automatically updates attempts, flights and lifts so you only really need to input the next attempt, and click goodlift/no lift!<br><br>Use the utilities to generate the results at the completion of the competition, and to save the data locally.<br><br>You can set the lift by clicking on the header row, and you can delete rows by clearing the 'Name' and 'Lot' information. To change a good/no lift if you get it wrong, just double click on the cell to toggle.<br><br>More help will be coming soon but you'll work it out!"); 
}

function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      //if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    //}
  }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});

