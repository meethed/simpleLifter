
//scoreboard function
//does a funky sort to start with
//division, weight class and age 
//also needs to sort by total THEN IPF points (in case something went wrong or in a weird group like a push pull)
//add headers
//then we work out placings
//then export to a new file

//TODO: do another one with raw data to enable excel sort/filter
function doScoreboard() {
   utils=["Comp Setup...","Save","Load","Hide Results","Help..."];
	var points=[0,12,9,8,7,6,5,4,3,2,1];
	document.getElementById("tableu").style.display="none";
	if (!!document.getElementById("scoreboard")) document.getElementById("scoreboard").remove();
	var scoreboard = document.createElement("table");
	scoreboard.id="scoreboard";
		var toSort=lifters.liftList;
		
		toSort.sort(function(a,b){ //this is the actual sorting bit here
			//not comp sort - it's different
			
			var nl;
			var al=a.lot;
			var bl=b.lot;
			var aw=a.weightClass.slice(0,-2);
			var bw=b.weightClass.slice(0,-2);
			var at=a.total;
			var bt=b.total;
			var ai=a.ipf; if (a.team=="***") ai=0;
			var bi=b.ipf; if (a.team=="***") ai=0;
			var abw=a.weight;
			var bbw=b.weight;
			var ad=a.division;
			var bd=b.division;
			var ay=a.ageDiv;
			var by=b.ageDiv;
	
			var acat=ad+" - " + ay + " - " + aw;
			var bcat=bd+" - " + by + " - " + bw;
			//fix up blanks so they're at the bottom
			if (at=="DSQ") at=9000;
			if (bt=="DSQ") bt=9000;
			
			//FIRST SORT - by division, year / age cat & weight class

			if (acat>bcat) return 1; //if a is higher then move it up
			if (acat<bcat) return -1 //if a is lower then move it down
			if (acat=bcat) { 				 //if they're in the same group then we need to sort again

			//SECOND SORT - by total ()
				if (at>bt) return -1;  //if a is higher, then move it up
				if (at<bt) return 1; //if a is lower then move it down
				if (at=bt) {					//if they're the same, then sort again

				//THIRD SORT - by ipf points remembering higher is better
				if (ai>bi) return -1;  //if a is higher, then move it up
				if (ai<bi) return 1; //if a is lower then move it down
				if (ai=bi) {					//if they're the same, then sort again

				//FOURTH SORT - by lot number as an emergency. lower lot wins
				if (al>bl) return 1;  //if a is higher, then move it up
				if (al<bl) return -1; //if a is lower then move it down
				if (al=bl) {					//if they're the same, then sort again

		}}}}}).
		forEach((el,index)=> {
		const thisRow = makeTr(el.row);
			scoreboard.appendChild(thisRow);
			//document.getElementById("tableu").appendChild(el[1].row);
			el.sortOrder=index;
		}); //end sorting function
	var csv=document.createElement("div");
  csv.innerHTML="<a href='./dead.php?c=" + compName + "'>Click Here for Open Powerlifting CSV Export</a>";
	document.body.appendChild(scoreboard); //display the scoreboard separately
	document.body.appendChild(csv);
	//OK we've finished sorting now to add in headers
	var headRow = makeTr(document.getElementById("tableu").children[0]);
	headRow.children[18].innerHTML="Place";
  headRow.children[19].innerHTML="Points";
	headRow.childNodes.forEach((c) => {c.style.backgroundColor="#69a"});
	var currentClass="";
	var place=0;
	for (var i=0;i<scoreboard.childElementCount;i++){ //start at 0 because we added the header row
		var newClass="<br>"+scoreboard.children[i].children[4].innerHTML+" "+scoreboard.children[i].children[6].innerHTML+" "+scoreboard.children[i].children[3].innerHTML
		if (newClass!=currentClass){
			place=0;
			var headingDiv=document.createElement("th");
			headingDiv.innerHTML=newClass;
			headingDiv.colSpan=scoreboard.children[2].childElementCount;
			scoreboard.insertBefore(headingDiv,scoreboard.children[i]);
			scoreboard.insertBefore(headRow.cloneNode(true),scoreboard.children[i+1]);

			headingDiv.style.backgroundColor="#fff";
			currentClass=newClass;
			i++;

		} else {
		place+=1;
		if (scoreboard.children[i].children[16].innerHTML=="DSQ") {scoreboard.children[i].children[18].innerHTML="DSQ";place=0;} else{		
			scoreboard.children[i].children[18].innerHTML=place;
			if (place<10) scoreboard.children[i].children[19].innerHTML=points[place];
			if (place>9) scoreboard.children[i].children[19].innerHTML=1;


};

		}

	}

	exportToExcel();
} //end of function doScoreboard
	
function makeTr(row){ //make a table row
	
	var sex=row.children[6].innerHTML.charAt(0);
	var newTr=document.createElement("tr");
	var cols=[2,3,4,5,6,7,8,11,12,13,15,16,17,20,21,22,24,25]; //which cols to make into a thing
	
	cols.forEach((i) => {
			var newTd=document.createElement("td");
			newTd.innerHTML=row.children[i].innerHTML;
			newTd.style.border="1px solid";
			newTd.style.backgroundColor="#ccc";
			newTr.appendChild(newTd);
			if (i>=11 && i<=22) { //if it's an attempt need to make sure we get the colour right
				// openpowerlifting.org prefers negative numbers, not "x" numbers, for fails
				if (row.children[i].classList.contains("nl")) newTd.innerHTML=parseFloat(newTd.innerHTML)*-1; 
			}
	});
	//add an extra column for the dynamically generated placing
	newTd=document.createElement("td");
			newTd.innerHTML=0
			newTd.style.border="1px solid";
			newTr.appendChild(newTd);	
	newTd.style.backgroundColor="#ccc";
 //add an extra column for points
	newTd=document.createElement("td");
		newTd.innerHTML=0;
		newTd.style.border="1px solid";
		newTr.appendChild(newTd);
		newTd.style.backgroundColor="$ccc";

	return newTr;
} //end fucntion makeTr

function exportToExcel(){ //export the table to Excel format
	var linky,
	 dataType='application/vnd.ms-excel',
	 tableu=document.getElementById("scoreboard").outerHTML.replace(/ /g, '%20'),
	 filename='simpleLifter-xx.xls';
	 
	 linky=document.createElement("a");
	 document.body.appendChild(linky);
	 linky.href='data:'+dataType+', '+tableu;
	 linky.download=filename;
	 linky.click();
} //end function exporttoexcel

function hideScoreboard(){
	utils=["Comp Setup...","Save","Load","Generate Results","Help..."];
	document.getElementById("tableu").style.display="block";
	document.body.removeChild(document.getElementById("scoreboard"));
} //end function  hidescoreboard

