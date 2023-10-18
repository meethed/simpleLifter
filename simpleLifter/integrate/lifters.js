class Lifters {

	constructor(c) {
		this.liftList=[];
		var i;
		for (i=0;i<c;i++) {
			this.liftList[i] = new Lifter(i%2==0? "A":"B",i+1,"lifter"+i);
		}
		this.activeRw=this.liftList[0];
		this.activeGp="A"; //active group (at the top)
		this.activeCol=10;

	}

	set activeLi(l){ //setter to show the active Lift

		if (!this.activeCol) this.activeCol=2;
		var tab=document.getElementById("tableu");
		var i;
		for(i=1;i<tab.childElementCount;i++){
			tab.children[i].children[this.activeCol+1].style.fontWeight="";
		}
		var c;
		switch (l){
			case "SQ-1": c=10; break;
			case "SQ-2": c=11; break;
			case "SQ-3": c=12; break;
			case "BP-1": c=14; break;
			case "BP-2": c=15; break;
			case "BP-3": c=16; break;
			case "DL-1": c=19; break;
			case "DL-2": c=20; break;
			case "DL-3": c=21; break;
			case "Weigh In": c=2;break;
		}
		this.activeCol=c;

		for(i=1;i<tab.childElementCount;i++){
			tab.children[i].children[this.activeCol+1].style.fontWeight="bold";
		}

} //end setter 

	get activeLi(){
			var c;
		switch (this.activeCol){
			case 10:c="SQ-1"; break;
			case 11:c="SQ-2"; break;
			case 12:c="SQ-3"; break;
			case 14:c="BP-1"; break;
			case 15:c="BP-2"; break;
			case 16:c="BP-3"; break;
			case 19:c="DL-1"; break;
			case 20:c="DL-2"; break;
			case 21:c="DL-3"; break;
			case 2:c="Weigh In";break;
		}

		return c;
		}

	set activeRow(r) { //set the active row based on the sort order!
		if (this.activeRw) this.activeRw.deactivate();
		if (r=== undefined) return;
		this.liftList.forEach((c) => {
			if (c.sortOrder==r) {
				this.activeRw=c;
				c.activate();
			}
		});
	updateStatus(); //don't update the status here, do it at the end of the increment. no doubling up this way.
  //we have to update it here, for when we go to the top (ie not increment but force set) or when we double click
	} //end activeRow setter


	get activeRow() { //get the active row based on the sort order!
		return this.activeRw.sortOrder	} //end activeRow getter


//increment the active row - this is actually a big deal as it's the competition progression right here
	get incrementRow() {
		var testRow=this.activeRow; //set this now. if we trigger the increment but it doesn't change, it means we're at the end of one flight
		var nextCell=lifters.activeRw.row.children[lifters.activeCol+2];
		this.activeRow+=1; //ok cool that was easy (it includes a get and set)

		//but we need to make sure we didn't go too far
  	//first if it's attempt 1 or 2, we need to detect when we should go back to the top for the next attempt
		//if it's attempt 3, we need to go back to attempt 1 for the next flight
		//if it's the last flight, we need to go back to attempt 1 for the next lift (bench/dead) for the first flight

		if (this.activeLi.slice(-1)!=3 ) { //if it's not the third attempt
			if (this.activeRw.group!=this.activeGp || this.activeRow==testRow) { //alternatively if we're at the end of the box (eg. there's only one flight)
				//increment the lift (ie we're at the end of the group)
				if (setup.auto) {
				this.activeLi=this.activeLi.slice(0,-1)+(parseInt(this.activeLi.slice(-1))+1); //get the next lift
				this.doSort();
				this.activeRow=0;
				} else {this.activeRow-=1;} //if it's not auto then don't move
			} //end if it's moving to the next lift
		} //end if it's attempt 1 or two

		if (setup.auto&&(this.activeLi.slice(-1)==3)) { //if it *is* the third lift, we need to go back to the start and move on to the next group
			if (this.activeRw.group!=this.activeGp  || this.activeRow==testRow) { //if it's moved into the next group
				this.activeLi=this.activeLi.slice(0,-1)+1; //active lift = same starting characters, but back to attempt 1
				if (setup.countUp) {
				this.activeGp=String.fromCharCode(this.activeGp.charCodeAt(0)+1); //increase active group; NO ONLY DO THIS IF WE'RE SUPPOSED TO
				} else {
				this.activeGp=String.fromCharCode(this.activeGp.charCodeAt(0)-1); //decrement the letter
				}
				//but first, what if the next letter doesn't exist?
				var arr=this.liftList;
				if (!arr.some((x) => x.group==this.activeGp)){ //if the group doesn't exist then we're done and need to change lift
					if (this.activeLi=="DL-1") {lifters.saveLocal();doScoreboard(); return 0};//if we've automatically changed back to deadlift 1 then it must be because we're at the end of the competition
					if (this.activeLi=="BP-1")
					if (setup.BP==0) 
						this.activeLi="DL-1";
					if (this.activeLi=="BP-1")
					if (setup.BP)
						{lifters.saveLocal();doScoreboard();return 0}; //if it's bench only
					if (this.activeLi=="SQ-1") this.activeLi="BP-1";

					if (setup.countUp) {this.activeGp="A";}
					if (!setup.countUp) {this.activeGp=setup.maxGp};

					// this is where we pop up saying we're doing a 10 minute (or 20 minute timer)
          if (setup.maxGp=="A") {var t=20;} else {var t=10;}; // if there's only the A group then 20 minute timer
					if (this.activeLi.charAt(0)=="B") {var oldEvent="Squats"} else {var oldEvent="Bench Press"};
					doPopup("That was the last of the  " + oldEvent + ". A " + t + " minute timer will now commence.");
					setTimer2(t);
				}
				this.doSort();
				this.activeRow=0; //set it back to the top but this is pointless if we're about to sort
			}//end if it's the next group / next event
		} //end if it's the third lift
		if (setup.auto&&this.activeRw.activeCell(this.activeLi)=="" && this.activeLi!="DL-3" &&this.activeRow!=0)
		this.incrementRow; //if after all that the next cell is blank, then skip them!
	this.doSort();
	updateStatus(); // i need this one.

	//set the next cell for editing
	if (this.activeLi.charAt(this.activeLi.length-1)!="3") {
	  var s = window.getSelection();
    var r = document.createRange();
		r.setStart(nextCell, 0);
		r.setEnd(nextCell, 0);
		s.removeAllRanges();
		s.addRange(r);
		window.getSelection().selectAllChildren(nextCell);
	}
	} //end incrementRow getter

	get places() {
	var gp;
	let ps=[];
	var cCat, ccCat, at,bt;
	var ls=this.liftList
	ls.forEach((c,index) => { //for each lifter in the lift list
		gp=1;
			var cCat=c.division+c.weightClass; //current category
			if (!setup.openOnly) cCat += c.ageDiv; //if it's open age only then ignore weights

			ls.forEach((cc,iindex) => { //for each lifter for comparison
			  ccCat=cc.division+cc.weightClass; //current cat for the comparison lifter
			  if (!setup.openOnly) ccCat += cc.ageDiv; //add age if not open
			  if (cCat==ccCat && c.idx!=cc.iindex && cc.predictedTotal>c.predictedTotal)  //note this isn't a proper sort. it just works out how many in front of the current lifter there are
			  gp+=1; //if they're bigger then add one
 
			});

			// cool so the places (liftList index) is assigned for each lifter
			ps[index]=gp;

	}) //get the next lifter
	return ps;
	} //end places getter


	doSort(g,l) { //function to sort the children based on the current "g"roup and "l"ift 
		if (!g) g=this.activeGp;
		if (!l) l=this.activeLi;
		g=g.charCodeAt(0);
		var toSort=this.liftList;
		toSort.sort(function(a,b){ //this is the actual sorting bit here
			var nl;
			var al=parseInt(a.lot);
			var bl=parseInt(b.lot);
			var ag=a.group.charCodeAt();
			var bg=b.group.charCodeAt();
			var aa=getAttempt(a,l,ag==g); //note if it's the "off" flight, then we need to select 1st attempts if everything is blank 
			var ba=getAttempt(b,l,bg==g); //as above
			var as=getStatus(a,l);
			var bs=getStatus(b,l);

      //first up just brute force weigh in lot number
      if (l.charAt(0)=="W") {
			if (al>bl) return 1;
			if (bl>al) return -1;
			}

			//fix up blanks so they're at the bottom
			if (aa=="") aa=9000;
			if (ba=="") ba=9000;
			if (setup.countUp) { //ugly but it sorts differently. can be tidied a little
				ag=(ag==g ?1:ag); //if ag=the current group, it's 1 (lower number) otherwise it's much higher
				bg=(bg==g ?1:bg); //if bg=the current group...
				if (ag==1) {a.row.children[0].classList.remove("offGp") }else {a.row.children[0].classList.add("offGp");}; //change colour coding for the active group
				if (bg==1) {b.row.children[0].classList.remove("offGp") }else {b.row.children[0].classList.add("offGp");}; //change colour coding for the active group
			}
			if (!setup.countUp) { //ugly but it sorts differently. can be tidied a little
				ag=(ag==g ?1000:ag); //if ag=the current group, it's 1000 (higher number) otherwise it's much higher
				bg=(bg==g ?1000:bg); //if bg=the current group...
				if (ag==1000) {a.row.children[0].classList.remove("offGp") }else {a.row.children[0].classList.add("offGp");}; //change colour coding for the active group
				if (bg==1000) {b.row.children[0].classList.remove("offGp") }else {b.row.children[0].classList.add("offGp");}; //change colour coding for the active group
			}
			//FIRST SORT - by group / flight
			if (setup.countUp){
			if (ag>bg) return 1; //if a is higher then move it up
			if (ag<bg) return -1 //if a is lower then move it down
			} else {
			if (ag>bg) return -1; //if a is lower then move it up
			if (ag<bg) return 1 //if a is higher then move it down
			}
			if (ag=bg) { 				 //if they're in the same group then we need to sort again

				//SECOND SORT - by attempt

				//first up we'll do the 'get ready for next round' sort
				//any rows that come before the current one are sorted by their next value
				if (lifters.activeLi.slice(-1)!=3) { //if it's not the third round
				nl=l.slice(0,-1)+(parseInt(l.slice(-1))+1); //set up the next attempt by cutting off the last digit and adding 1 to it (SQ-1 to SQ-2 etc)
					if (ag && bg &&as && bs){ //only if the "A" and "B" are in the current group
							aa=getAttempt(a,nl,ag==g); 
							if (aa=="") aa=9000; else aa-=1000;//if they've already done the current lift then sort by the next attempt, by making it much lower to sort it at the top
							ba=getAttempt(b,nl,bg==g);
							if (ba=="") ba=9000; else ba-=1000;//if they've already done the current lift then sort by the next attempt, by making it much lower to sort it at the top
				}}


				if (aa>ba) return 1;  //if a is higher, then move it up
				if (aa<ba) return -1; //if a is lower then move it down
				if (aa=ba) {					//if they're the same, then sort again

					//THIRD SORT - by lot number
					if (al>bl) return 1; 	//if a is lower than move it up (low lot first)
					if (al<bl) return -1; //if a is higher than move it up

				}
			} 

		function getAttempt(l,a,ongp){ //return the attempt based on the "l"ifter and "a"ttempt

			var lift=a.slice(0,2).toLowerCase();
			var num=a.slice(-1);
			if (isNaN(num)) return "";
			var att=l[lift]["a"+num];
			if (!ongp) {
				if (!att) att=l[lift]["a2"];
				if (!att) att=l[lift]["a1"];
				if (!att) att=0;
			}
			return att;

		} //end getAttempt internal function

		function getStatus(l,a){ //return the attempt based on the "l"ifter and "a"ttempt

			var lift=a.slice(0,2).toLowerCase();
			var num=a.slice(-1);
			if (isNaN(num)) return "";
			return l[lift]["s"+num];

		} //end getStatus internal function
			
		}).forEach((el,index)=> {
			document.getElementById("tableu").appendChild(el.row);
			el.sortOrder=index;
		});
	
	} //end doSort function
	
	get nextAtt() { //start of the next attempt getter function
		var t,s,c,i,rr; //yes this is pretty much the same as the increment function :(
		s=this.activeRow+1;
		if (s>setup.lifterCount) s-=1;
		var ls=lifters.liftList;
		ls.forEach((c,index) => {
			if (c.sortOrder==s) {
				i=index; //index is equal to the counter - means we've found our lifter
			} else if (c.sortOrder==0) t=index;
});

		if (this.activeLi.charAt(0)=="S") {rr=9;var ra="SQ Rack: "};
		if (this.activeLi.charAt(0)=="B") {rr=10; var ra="BP Rack: ";};
    if (!rr) {rr=0; var ra="";};
		//a is the attempt as a raw number, n is the lifter name, l is the lot number and r is the rack
		if (this.liftList[i].group==this.activeGp) //if we can progress in the same flight/lift
			return {"n":this.liftList[i].row.children[2].innerHTML,"a":this.liftList[i].row.children[this.activeCol+1].innerHTML,"l":this.liftList[i].row.children[1].innerHTML,"r":ra+this.liftList[i].row.children[rr].innerHTML}
		if (this.liftList[i].group!=this.activeGp) //if we can't progress in the same flight/lift let's increase the lift first
			if (this.activeLi.charAt(3)!="3")
			return {"n":this.liftList[t].row.children[2].innerHTML,"a":this.liftList[t].row.children[this.activeCol+2].innerHTML,"l":this.liftList[t].row.children[1].innerHTML,"r":ra+this.liftList[t].row.children[rr].innerHTML}
			//just move the column over one and from the top
		if (this.activeLi.charAt(3)=="3") //ok now we're fucked. need to increment the row by one, and back to the start.
		//it's not that bad. the rows are sorted properly, so we'll just go down and left haha
			return {"n":this.liftList[i].row.children[2].innerHTML, "a":this.liftList[i].row.children[this.activeCol-1].innerHTML,"l":this.liftList[i].row.children[1].innerHTML,"r":ra+this.liftList[i].row.children[rr].innerHTML} //haha easy.
	} //end next attempt getter


	 saveLocal() { //start of the save local function
		setup.activeLi=this.activeLi;
		setup.activeGp=this.activeGp;
		setup.activeRow=this.activeRow;
//		try {
			saveServer();
//		} catch(err)
//		{alert("Online update failed")};
		var ls=this.liftList;
		if (ls.length<0) return false;
		ls.forEach((el,index)=>{
			localStorage.setItem("simpleLifter"+index,el.toJson);
		});
		setup.lifterCount=ls.length-3;
		localStorage.setItem("simpleSetup",setup.toJson);
	} //emd saveLocal function

	loadLocal() {
	//	try {
	//	} catch(err)
	{
		//first up delete everything
		this.liftList=[];
	  loadServer();

		//get the setup data
	//	var set=localStorage.getItem("simpleSetup");
	//	setup.fromJson=set;

		//iterate adding lifters
//		for (i=0;i<setup.lifterCount;i++){
//			var ldat=localStorage.getItem("simpleLifter"+i);
//			this[i] = new Lifter("","","","","","","","","",JSON.parse(ldat));
//		}
	}
	var loadingPause=setTimeout(function() {
		if (setup.activeLi){	lifters.activeLi=setup.activeLi} else {lifters.activeLi="Weigh In";setup.activeLi=lifters.activeLi};
		if (setup.activeGp){	lifters.activeGp=setup.activeGp} else {lifters.activeGp="A";setup.activeGp=lifters.activeGp};
		lifters.doSort();
		lifters.activeRow=setup.activeRow;
		updateStatus();
		setupCx();
	},500);
	}

	nuke(lifter){ //function to delete a specific lifter - can't use the delete word
		if (!isNaN(lifter)) { //if it's a row
			this.liftList[lifter].row.remove();
			this.liftList.splice(lifter,1);
			this.liftList[lifter];
		}
	this.liftList.forEach((c,i) => c.idx=i); //renumber neatly
	} //end of the nuke lifter function

} //end of the Lifters class

function updateStatus() { //function to update the statusbar
	//hack to update the bar based on specific CAPO rules (called each change so no biggie)
	// some CAPO comps use a powerbar. Manually change...
	//	if (setup.CAPO)
	//	  if (lifters.activeLi.charAt(0)=="S") {setup.bar=30 } else {setup.bar=25}; //CAPO hack


	if (!lifters.activeRw) return;
	document.getElementById("curLifter").innerHTML=lifters.activeRw.name;
	document.getElementById("curGroup").innerHTML=lifters.activeRw.group;
	document.getElementById("curAtt").innerHTML=lifters.activeRw.activeCell(lifters.activeLi);
	document.getElementById("curLift").innerHTML=lifters.activeLi;
	if (lifters.activeLi.charAt(0)=="S") document.getElementById("staMisc").innerHTML="SQ Rack: "+lifters.activeRw.sr;
		if (lifters.activeLi.charAt(0)=="B") document.getElementById("staMisc").innerHTML="BP Rack: "+lifters.activeRw.br;
			if (lifters.activeLi.charAt(0)=="D") document.getElementById("staMisc").innerHTML="<br>";
	
	drawPlates(lifters.activeRw.activeCell(lifters.activeLi));
	lifters.saveLocal() //save it every time we update the screen
	if (setup.stream) sendStream(); // if we're doing the stream, make sure to send it now
	// NOTE this is currently broken - if stream is disabled then it won't send the current load data

	if (waiting==0) {
	try {lightWindow.drawPlates1(lifters.activeRw.name, lifters.activeRw.activeCell(lifters.activeLi),lifters.activeRw.lot,document.getElementById("staMisc").innerHTML,setup.bar);} catch {};
	var nexts = lifters.nextAtt;
	if (nexts["r"].charAt(0)!="S" && nexts["r"].charAt(0)!="B") {nexts["r"]=""};
	try {lightWindow.drawPlates2(nexts["n"],nexts["a"],nexts["l"],nexts["r"],setup.bar);} catch {};
	}

	// update the lifter places here
	// it's ugly cause you need access to the lifter object to compare each lifter
	// but we want to store the data in each lifter as well
	var pp=lifters.places;
	pp.forEach((e,i) => lifters.liftList[i].place=e);

} //end function updateStatus


