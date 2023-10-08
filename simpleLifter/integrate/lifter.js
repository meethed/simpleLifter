//const fw=[43, 47, 52, 57, 63, 69, 76, 84, 1000]; //IPF female classes
//const mw=[53, 59, 66, 74, 83, 93, 105,120,1000]; //IPF male classes
//const fcw=[44,48,52,56,60,67.5,75,82.5,90,1000];
//const mcw=[52,56,60,67.5,75,82.5,90,100,110,125,140,1000];


class Att{ //this is the 'attempt' class. It contains 3x attemps (a1-3) and 3x statuses (s1-3)
	constructor(a1="",a2="",a3="",s1=0,s2=0,s3=0,json="") {
		if (json) {
		Object.assign(this.json)
		} else {
		this.a1= a1,
		this.a2= a2,
		this.a3= a3,
		this.s1=s1,
		this.s2=s2,
		this.s3=s3;
		}
	}
	get toJson() {
	return JSON.stringify(this) //getter function to convert the lifter to JSON
	}
	
	get best() { //returns the actual best (status needs to be 1)
	return Math.max(this.a1*this.s1,this.a2*this.s2,this.a3*this.s3,0)}
	
	get predicted() { //returns the predicted best (ie successful or future attempt but not failed lifts)
	return Math.max(this.a1*(this.s1>=0),this.a2*(this.s2>=0),this.a3*(this.s3>=0),0)}

	get bombed() { //checks if they've bombed. if bombed=-1 then they've failed all three lifts. 
		var b=0;
		if (Math.max	(this.s1, this.s2, this.s3)==-1) b=1;
		return b;
	}

} //end class Att

class Lifter {
	constructor(group="A",lot=1,name="Lifter1",team="",year=1987,division="M-CL-PL",bw=95.5,sr="15",br="11/5",json=""){ //generic constructor with default data
	if (json) {
	Object.assign(this,json);
	this.sq= new Att(this.sq.a1,this.sq.a2,this.sq.a3,this.sq.s1,this.sq.s2,this.sq.s3);
	this.bp= new Att(this.bp.a1,this.bp.a2,this.bp.a3,this.bp.s1,this.bp.s2,this.bp.s3);
	this.dl= new Att(this.dl.a1,this.dl.a2,this.dl.a3,this.dl.s1,this.dl.s2,this.dl.s3);

} else {
	this.group= group,
	this.lot= parseInt(lot),
	this.name= name,
	this.team= team,
	this.year= year,
	this.division= division,
	this.bw= bw,
	this.sr= sr,
	this.br= br,
	this.idx=lot-1;
	this.place=0; //the lifter place is calculated in the parent class, but duplicated in here when pulling individual lifters
	this.sq=new Att();
	this.bp=new Att();
	this.dl=new Att();
	}
	this.sortOrder=0;
	this.act=0;
	this.row=setDivs(this); //set the divs
	this.updateDiv();
	} //end of the constructor
	
	
	get toJson() { //getter function to convert the lifter to JSON
		
	return JSON.stringify(this);
	} //end of getter function
	
	activeCell(lift){
		switch (lift) {
			case "SQ-1":
				return this.sq.a1;
			break;
			case "SQ-2":
				return this.sq.a2;
			break;
			case "SQ-3":
				return this.sq.a3;
			break;
			case "BP-1":
				return this.bp.a1;
				break;
			case "BP-2":
				return this.bp.a2;
			break;
			case "BP-3":
				return this.bp.a3;
			break;
			case "DL-1":
				return this.dl.a1;
			break;
			case "DL-2":
				return this.dl.a2;
			break;
			case "DL-3":
				return this.dl.a3;
			break;
			case "Weigh In":
				return "-";
			
		}
	}
	
	setLift(lift,stat){
		switch (lift) {
			case "SQ-1":
				this.sq.s1=stat;
			break;
			case "SQ-2":
				this.sq.s2=stat;
			break;
			case "SQ-3":
				this.sq.s3=stat;
			break;
			case "BP-1":
				this.bp.s1=stat;
			break;
			case "BP-2":
				this.bp.s2=stat;
			break;
			case "BP-3":
				this.bp.s3=stat;
			break;
			case "DL-1":
				this.dl.s1=stat;
			break;
			case "DL-2":
				this.dl.s2=stat;
			break;
			case "DL-3":
				this.dl.s3=stat;
			break;
			
		}
		this.updateDiv();
	}
	
	updateDiv() { //update the html
		
		this.row.children[0].innerHTML=this.group;
		this.row.children[1].innerHTML=this.lot;
		this.row.children[2].innerHTML=this.name;
		this.row.children[3].innerHTML=this.team;
		if (isNaN(this.year)) {this.row.children[4].innerHTML=""} else
		this.row.children[4].innerHTML=this.year;
		this.row.children[5].innerHTML=this.ageDiv;
		this.row.children[6].innerHTML=this.division;
		if (isNaN(this.bw)) {this.row.children[7].innerHTML=""} else
		this.row.children[7].innerHTML=this.bw;
		this.row.children[8].innerHTML=this.weightClass;
		this.row.children[9].innerHTML=this.sr;	
		this.row.children[10].innerHTML=this.br;
		validateLifts(this.row,11,this.sq);
		this.row.children[14].innerHTML=this.sq.best;
		validateLifts(this.row,15,this.bp);
		this.row.children[18].innerHTML=this.bp.best;
		this.row.children[19].innerHTML=this.subTotal;
		validateLifts(this.row,20,this.dl);
		this.row.children[23].innerHTML=this.dl.best;
		this.row.children[24].innerHTML=this.total;
		this.row.children[25].innerHTML=this.ipf;

		function validateLifts(r,c,g){ //r is row (division), c is col (div child to start), g is group (group of attempts/statuses)
		var i;
		for (i=0;i<=2;i++){
			r.children[c+i].classList.remove("gl");
			r.children[c+i].classList.remove("nl");
			r.children[c+i].classList.remove("at");
		}
		if (isNaN(g.a1)) {r.children[c].innerHTML=""} else {r.children[c].innerHTML=g.a1;}
		r.children[c].classList.add(gba(g.s1));
		if (isNaN(g.a2)) {r.children[c+1].innerHTML=""} else {r.children[c+1].innerHTML=g.a2;}
		r.children[c+1].classList.add(gba(g.s2));
		if (isNaN(g.a3)) {r.children[c+2].innerHTML=""} else {r.children[c+2].innerHTML=g.a3;}
		r.children[c+2].classList.add(gba(g.s3));
		
		function gba(st){
		switch (st) {
		case 1:
			return "gl";
			break;
		case 0:
			return "at";
			break;
		case -1:
			return "nl";
			break;
		} //end switch
		} //end good/bad/attempt function
		} //end validateLifts function
	} //end updateDiv function


	
	get isBench() { //returns 0 if it's PL not BP
	if (this.division.charAt(this.division.length-1)=="L") return 0; else return 1
	}
	
	get subTotal() {
		return this.sq.best+this.bp.best; //squat and bench sub total based on good attempts
	}
	
	get total() {
		var t=this.sq.best+this.bp.best+this.dl.best; //total based on good attempts
		if (this.sq.bombed || this.bp.bombed || this.dl.bombed) t="DSQ"  //if they've bombed in one of the lifts then return DSQ
		return t
	}
	
	get predictedTotal() { //predicted total based on good or future lifts
		var pt=this.sq.predicted+this.bp.predicted+this.dl.predicted;
		if (this.sq.bombed || this.bp.bombed || this.dl.bombed) pt= "DSQ" 
		return pt;
	}
	
	get ipf() { //returns the ipf points for the lifter, based on their total, classic/equipped, 3 lift or bench only, and gender


	var t=this.total;
	var bw=this.bw;
	var d=this.division;
	var team=this.team;
	let cy  = new Date().getFullYear();
	var age = cy-this.year;

	if (team=="***") return 0;
	if (setup.CAPO) { return getGlos(t,bw,d,age);};

	function getGlos(t,bw,d,age) { //internal function for glosbrenner ESTIMATE
	var a,b,c,d,e,f,mam;
	if (d.charAt(0)=="F") {
		a=-0.00000001;
		b=0.00001;
		c=-0.0041;
		d=0.7349;
		e=-65.865;
		f=3041.2;
	} else {
		a=-3.77264e-08;
		b=3e-05;
		c=-0.00933;
		d=1.428460179;
		e=-108.661;
		f=3875.454327;
	}
	if (age>=23 && age <=40) {mam=1} else {
		if (age>40) {mam = (0.0463*age**2 - 2.9491 * age + 144.48)/100};
		if (age<23) {mam = (-0.0253*age**3 + 1.17532*age**2 - 40.94*age + 422.42)/100;}
	}
	var glos = a*bw**5 + b*bw**4 + c*bw**3 + d*bw**2 + e*bw+f;
	glos = glos * t * mam /1000;
	return glos.toFixed(2);
	} //end internal function getGlos

	if (d.slice(-2)=="BP") t=this.bp.best;
	var a=0,b=0,c=0,ipf=0;

	if (d=="M-EQ-PL") {
		a=1236.25115;
		b=1449.21864;
		c=0.01644;
		}
	if (d=="M-CL-PL" || d=="M-SO-PL" || d=="M-SO-PP") {
		a=1199.72839;
		b=1025.18162;
		c=0.00921;
		}
		if	 (d=="M-EQ-BP") {
		a=381.22073;
		b=733.79378;
		c=0.02398;
		}
		if (d=="M-CL-BP") {
		a=320.98041;
		b=281.40258;
		c=0.01008;
		}
		if (d=="F-EQ-PL") {
		a=758.63878;
		b=949.31382;
		c=0.02435;
		}
		if (d=="F-CL-PL" || d=="F-SO-PL" || d=="F-SO-PP") {
		a=610.32796;
		b=1045.89282;
		c=0.03048;
		}
		if (d=="F-EQ-BP") {
		a=221.82209;
		b=357.00377;
		c=0.02937;
		}
		if (d=="F-CL-BP") {
		a=142.40398;
		b=442.52671;
		c=0.04724;
		}
		if (a==0) {ipf=0} else  ipf = (t*100/(a-b*Math.exp(-c*bw))).toFixed(2);
		return ipf
	 //end ipfGL

	}
	
	get ageDiv() { //returns the IPF age division for the lifter based on the current year and their birth year
		if (setup.openOnly) {return "O";} else {
		var age,ageClass;
		let cy  = new Date().getFullYear();
		age = cy-this.year;

		 if (!setup.CAPO) {
		if (age<=18) {ageClass="S-Jr";} else
		if (age<=23) {ageClass="Jr";} else
		if (age>=70) {ageClass="M4";} else
		if (age>=60) {ageClass="M3";} else
		if (age>=50) {ageClass="M2";} else
		if (age>=40) {ageClass="M1";} else
			{ageClass="O";}
	 	} else { 
 		if (age<=18) {ageClass="J15-18";} else 
 		if (age<=23) {ageClass="J18-23";} else 
 		if (age<=80) {ageClass="M80+";} else 
 		if (age<=75) {ageClass="M75-79";} else 
 		if (age<=70) {ageClass="M70-74";} else 
 		if (age<=65) {ageClass="M65-69";} else 
 		if (age<=60) {ageClass="M60-64";} else 
 		if (age<=55) {ageClass="M55-59";} else 
 		if (age<=50) {ageClass="M50-54";} else 
 		if (age<=45) {ageClass="M45-49";} else 
 		if (age<=40) {ageClass="M40-44";} else 
 		{ageClass="O"}; 
 		}
		return ageClass;}

	}
	
	get weightClass() { //returns the Weight class for the lifter based on age, gender and bw

	
	var wc,f,gender,age,bw;
	var d=[0];
	gender = this.division.charAt(0);
	age = this.ageDiv;
	bw = this.bw;

	switch (gender) {
	case "F": d=setup.fW; break;
	case "X": d=setup.xX; break;
	case "M":
	default:
		  d=setup.mW; break;
	}

	if (!d || d.length==1) {
	if (gender==="F") {d=[47,52,57,63,69,76,84,1000];} else {d=[56,59,66,74,83,93,105,120,1000];};
	}
        wc=d.find(el => el >= bw);
	var ix=d.findIndex(el => el >=bw);

	if (ix==d.length-1) wc=d.at(-2)+"+"; //if it's 1000 (the last entry), then they're SHW. So take the previous weight category and add a plus symbol
	if (ix==0 && !age.endsWith("Jr")) wc=d[1];

	if (!wc) wc="";
	return wc+"kg";
	}

		activate() { //set the active lifter row (note this just takes the row pointer)
		//first up get rid of the bold on the current active row
		this.row.childNodes.forEach((c) => {
			c.classList.add("act")});
		
		this.act=l;
		
	} //end activeRow setter

		deactivate() { //set the active lifter row (note this just takes the row pointer)
		//first up get rid of the bold on the current active row
		this.row.childNodes.forEach((c) => {
			c.classList.remove("act")});
		
		this.act=0;
		
	} //end activeRow setter


} //end class definition


//////////////////////////////////////////////////////////////////////////


function setDivs(lifter){
	var newCell,i;
	var newRow = document.createElement("div"); //make a new row Div
	newRow.classList.add("tr");
	newRow.id=lifter.name+lifter.lot;
	

	for (i=0;i<=25;i++) { //simple iterator loop to add the children divs
	
	if (i<11) { //the first ones are easy
		newCell=document.createElement("div");
		if(i!=8 && i!=5) {newCell.contentEditable="plaintext-only";} else newCell.classList.add("calc");
		newCell.addEventListener("focusout",function(e) {updateFromDiv(lifter)});
		if (i==2) {
		newCell.addEventListener("dblclick",function(e) {changeLifter(e.currentTarget)});
		newCell.addEventListener("contextmenu",function(e) {e.preventDefault(); showContext(e.currentTarget);});
		newCell.classList.add("td");
	} 	
	if (i>=11 && i<=25) { //these ones are for the numbers
		newCell=document.createElement("div");
		newCell.classList.add("td");
		if (i!=14 && i!=18 && i!= 19 && i!=23 && i!=24 && i!=25) {
			newCell.contentEditable="plaintext-only";
			newCell.addEventListener("focusout",function(e) {updateFromDiv(lifter,e)});
			newCell.addEventListener("dblclick",function(e) {changeStatus(e.currentTarget)});
			newCell.classList.add("at");
		} else newCell.classList.add("calc");
		
	}

		

	newRow.appendChild(newCell);
	
	}



	
	newRow.children[0].id=newRow.id+"gp";
	newRow.children[1].id=newRow.id+"lt";
	newRow.children[2].id=newRow.id+"nm";
	newRow.children[3].id=newRow.id+"tm";
	newRow.children[4].id=newRow.id+"yr";
	newRow.children[5].id=newRow.id+"ad";
	newRow.children[6].id=newRow.id+"dv";
	autocomplete(newRow.children[6],divisions);
	newRow.children[7].id=newRow.id+"bw";
	newRow.children[8].id=newRow.id+"wc";
	newRow.children[9].id=newRow.id+"sr";
	newRow.children[10].id=newRow.id+"br";
	newRow.children[11].id=newRow.id+"s1";
	newRow.children[12].id=newRow.id+"s2";
	newRow.children[13].id=newRow.id+"s3";
	newRow.children[14].id=newRow.id+"bs";
	newRow.children[15].id=newRow.id+"b1";
	newRow.children[16].id=newRow.id+"b2";
	newRow.children[17].id=newRow.id+"b3";
	newRow.children[18].id=newRow.id+"bb";
	newRow.children[19].id=newRow.id+"st";
	newRow.children[20].id=newRow.id+"d1";
	newRow.children[21].id=newRow.id+"d2";
	newRow.children[22].id=newRow.id+"d3";
	newRow.children[23].id=newRow.id+"bd";
	newRow.children[24].id=newRow.id+"to";	
	newRow.children[25].id=newRow.id+"gl";	
	document.getElementById("tableu").appendChild(newRow);		
	
	return newRow;
	
	} 
	//end function setDivs
	
	
	
	function updateFromDiv(lifter,cell){ //when a cell loses focus it calls this function to update the lifter class / object data. it also calls a sort just in case
		if (cell) {
			var num=cell.target.innerHTML; //this is to check if the number is correct
			if (num % 2.5){doPopup("You just entered a number that isn't divisible by 2.5, which should only be for an appropriate record attempt. Please confirm it's not a typo!")};
			if (cell.target.id.slice(-1)=="2" || cell.target.id.slice(-1)=="3") {
				if (num>0) setTimer2(-1000); //if we've input new data into a second or third attempt, then clear the "1 minute to submit next attempt timer" thing
				if (num>0 && cell.target.previousElementSibling.innerHTML >0 && parseFloat(num) < parseFloat(cell.target.previousElementSibling.innerHTML)) {doPopup("You entered a number that is lower than the previous attempt. You can't go down in weight, please confirm.")};
			}
		}
		if (!lifter.row) return;

		if ((lifter.row.children[2].innerHTML=="") && (lifter.row.children[1].innerHTML=="")){
			lifters.nuke(lifters.liftList.indexOf(lifter));}


		lifter.group=lifter.row.children[0].innerHTML.charAt(0).toUpperCase();
		//set the max group here
		if (lifter.group > setup.maxGp) setup.maxGp=lifter.group;
		lifter.lot=parseInt(lifter.row.children[1].innerHTML);
		lifter.name=lifter.row.children[2].innerHTML;
		lifter.team=lifter.row.children[3].innerHTML;
		lifter.year=parseInt(lifter.row.children[4].innerHTML);
		lifter.division=lifter.row.children[6].innerHTML;
		lifter.bw=parseFloat(lifter.row.children[7].innerHTML);
		lifter.sr=lifter.row.children[9].innerHTML;	
		lifter.br=lifter.row.children[10].innerHTML;
		updateLifts(lifter.row,11,lifter.sq);
		updateLifts(lifter.row,15,lifter.bp);
		updateLifts(lifter.row,20,lifter.dl);
		lifters.doSort();
		
		
		function updateLifts(r,c,g){ //r is row (division), c is col (div child to start), g is group (group of attempts/statuses)
		var i
		
		g.a1=Number.isNaN(parseFloat(r.children[c].innerHTML)) ? "" : parseFloat(r.children[c].innerHTML);
		g.s1=abg(r.children[c]);
		g.a2=Number.isNaN(parseFloat(r.children[c+1].innerHTML)) ? "" : parseFloat(r.children[c+1].innerHTML);
		g.s2=abg(r.children[c+1]);
		g.a3=Number.isNaN(parseFloat(r.children[c+2].innerHTML)) ? "" : parseFloat(r.children[c+2].innerHTML);
		g.s3=abg(r.children[c+2]);
		
		function abg(st){ //backwards
			if (st.classList.contains("gl")) return 1;
			if (st.classList.contains("nl")) return -1;
			if (st.classList.contains("at")) return 0;
			return 0;
		} //end good/bad/attempt function
		} //end validateSquats function
		lifter.updateDiv();
	} //end function updateFromDiv
	
	
	function changeStatus(e) { //good or no lift
		if (e.classList.contains("gl")) {e.classList.remove("gl");e.classList.add("nl");return}; 
		if (e.classList.contains("nl")) {e.classList.remove("nl");e.classList.add("at");return};
		if (e.classList.contains("at")) {e.classList.remove("at");e.classList.add("gl");return};
		e.classList.add("gl");
		
	}

	function changeLifter(e) {
		var t= document.getElementById("curLifter");
		t.innerHTML = e.innerHTML;
		for (i=0;i<setup.lifterCount;i++)
		if (lifters.liftList[i].name==e.innerHTML){
			if (lifters.liftList[i].group!=lifters.activeGp) {lifters.activeGp=lifters.liftList[i].group; lifters.doSort()};
			setBarLoaded(-1); //clear the barloaded timer THEN change the lifter - this will ensure the plates display is updated
			lifters.activeRow=lifters.liftList[i].sortOrder;
			updateStatus();
			}
		}
////////////////////////////////////////////////////////////////////
