

class Setup {

	constructor(bar=25,startGroup="A",plates=[25,20,15,10,5,2.5,1.25,0.5,0.25],auto=true,countUp=true,showLights=true,CAPO=false){ //generic constructor with default data
		this.bar=bar;
		this.startGroup=startGroup;
		this.plates=plates;
		this.lifterCount=0;
		this.auto=auto;
		this.countUp=countUp;
		this.showLights=showLights;
		this.BP=0;
		this.maxGp="";
		this.CAPO=0;
		this.mW=[53,59,66,74,83,93,105,120,1000];
		this.fW=[43,47,52,57,63,69,76,84,1000];
		this.xW=[56,59,66,74,83,93,105,120,1000];
}
	
	get toJson(){
	
	return JSON.stringify(this);}


	set fromJson(j){
		Object.assign(this,JSON.parse(j));
		

		document.getElementById("inBarWeight").value=setup.bar;
		document.getElementById("inAuto").checked=setup.auto;
		document.getElementById("inAlpha").checked=setup.countUp;
		document.getElementById("inLights").checked=setup.showLights;
		document.getElementById("inAutoRefs").checked=setup.autoRefs;
		document.getElementById("inOBS").checked=setup.stream;
		document.getElementById("inBP").checked=setup.BP;
		document.getElementById("inMW").value=setup.mW;
		document.getElementById("inFW").value=setup.fW;
		document.getElementById("inXW").value=setup.xW;
		document.getElementById("inCAPO").checked=setup.CAPO;
			if (document.getElementById("inLights").checked) {
				document.getElementById("lightsFrame").style.display="inline";
				document.getElementById("btnBar").style.opacity="0";
			} else {
				document.getElementById("lightsFrame").style.display="none"
				document.getElementById("btnBar").style.opacity="1";
			} ;
		setupOk();

	}
} //end load


function doSetup(){ //called when they press the setup new comp button in the utilities menu


	// grey out the screen

	shade=document.createElement("div");
	shade.id="shade";
	shade.style="position: fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.9)";
	document.body.appendChild(shade);

	
	//show the menu popup div using css
	document.getElementById("menu").style.display="block";

}


function setupCx(){
	try {document.body.removeChild(document.getElementById("shade"));}catch{}
	document.getElementById("menu").style.display="none";
	document.getElementById("inBarWeight").value=setup.bar;
	document.getElementById("inAuto").checked=setup.auto;
	document.getElementById("inAlpha").checked=setup.countUp;
	document.getElementById("inLights").checked=setup.showLights;
	document.getElementById("inAutoRefs").checked=setup.autoRefs;
	document.getElementById("inOBS").checked=setup.stream;
	document.getElementById("inBP").checked=setup.BP;
	document.getElementById("inCAPO").checked=setup.CAPO;
		document.getElementById("inMW").value=setup.mW;
		document.getElementById("inFW").value=setup.fW;
		document.getElementById("inXW").value=setup.xW;

	if (!setup.maxGp) setup.maxGp="";
	setBench();
}

function setupReset(){
	//setup the number of lifters
	setup.lifterCount=document.getElementById("inNumLifters").value;

		lifters.liftList.forEach((c) => {
			c.row.remove();
		});
		lifters.liftList=[];
	
	for(var i=0;i<setup.lifterCount;i++){lifters.liftList[i] = new Lifter("",i+1,"","","","","","","","");};
	document.getElementById("btnCx").innerHTML="Close";
}

function setupOk(){


	//setup the bar + collar weight, auto progression, etc
	setup.bar=document.getElementById("inBarWeight").value;
	setup.auto=document.getElementById("inAuto").checked;
	setup.autoRefs=document.getElementById("inAutoRefs").checked;
	setup.countUp=document.getElementById("inAlpha").checked;
	setup.showLights=document.getElementById("inLights").checked;
	setup.stream=document.getElementById("inOBS").checked;
	setup.BP=document.getElementById("inBP").checked;
	setup.CAPO=document.getElementById("inCAPO").checked;
	setup.mW=document.getElementById("inMW").value.split(",");if (setup.mW.slice(-1)!=1000) setup.mW.push(1000);
	setup.fW=document.getElementById("inFW").value.split(",");if (setup.fW.slice(-1)!=1000) setup.fW.push(1000);
	setup.xW=document.getElementById("inXW").value.split(",");if (setup.xW.slice(-1)!=1000) setup.xW.push(1000);

	if (setup.CAPO) {document.getElementById("gl").innerHTML="Glos";} else {document.getElementById("gl").innerHTML="IPF Points"};
	setBench();
	document.body.removeChild(document.getElementById("shade"));
	document.getElementById("menu").style.display="none";
	//do we show the lights iframe?
	if (document.getElementById("inLights").checked) {
		document.getElementById("lightsFrame").style.display="inline"
		document.getElementById("btnBar").style.opacity="1";
	} else {
		document.getElementById("lightsFrame").style.display="none"
		document.getElementById("btnBar").style.opacity="0";
	} ;
	if (!setup.mW) {
		setup.mW=[56,59,66,74,83,93,105,120,1000];
		setup.fW=[43,47,52,57,63,69,76,84,1000];
		setup.xW=setup.mW;
	}
} //end function setupOk()

function setBench() {
//clear the appropriate tabs for the bench press etc
    var ls=Object.entries(lifters);

    if (setup.BP) { //if BP only hide columns
      document.getElementById("rs").style.display="none";
      document.getElementById("s1").style.display="none";
      document.getElementById("s2").style.display="none";
      document.getElementById("s3").style.display="none";
      document.getElementById("bs").style.display="none";
      document.getElementById("d1").style.display="none";
      document.getElementById("d2").style.display="none";
      document.getElementById("d3").style.display="none";
      document.getElementById("bd").style.display="none";
      document.getElementById("st").style.display="none";

    ls.forEach((c,index) => {
      if (c[1] instanceof Lifter)
      if (index<ls.length-3)
      {
        c[1].row.children[9].style.display="none";
        c[1].row.children[11].style.display="none";
        c[1].row.children[12].style.display="none";
        c[1].row.children[13].style.display="none";
        c[1].row.children[14].style.display="none";
        c[1].row.children[19].style.display="none";
        c[1].row.children[20].style.display="none";
        c[1].row.children[21].style.display="none";
        c[1].row.children[22].style.display="none";
        c[1].row.children[23].style.display="none";
      }
 });

			//set width to look better
			document.getElementById("tableu").style.width="75%";

    } else { //unhide them (just in case
      document.getElementById("rs").style.display="table-cell";
      document.getElementById("s1").style.display="table-cell";
      document.getElementById("s2").style.display="table-cell";
      document.getElementById("s3").style.display="table-cell";
      document.getElementById("bs").style.display="table-cell";
      document.getElementById("d1").style.display="table-cell";
      document.getElementById("d2").style.display="table-cell";
      document.getElementById("d3").style.display="table-cell";
      document.getElementById("bd").style.display="table-cell";
      document.getElementById("st").style.display="table-cell";

    ls.forEach((c,index) => {
      if (c[1] instanceof Lifter)
      if (index<ls.length-3)
      {
        c[1].row.children[9].style.display="table-cell";
        c[1].row.children[11].style.display="table-cell";
        c[1].row.children[12].style.display="table-cell";

        c[1].row.children[13].style.display="table-cell";
        c[1].row.children[14].style.display="table-cell";
        c[1].row.children[19].style.display="table-cell";
        c[1].row.children[20].style.display="table-cell";
        c[1].row.children[21].style.display="table-cell";
        c[1].row.children[22].style.display="table-cell";
        c[1].row.children[23].style.display="table-cell";
      }
 });
			//set width to look better
 		document.getElementById("tableu").style.width="auto";

    }
	
} //end function setBench();
