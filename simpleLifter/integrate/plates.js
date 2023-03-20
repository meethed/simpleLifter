function getPlates(w) {
w=w-setup.bar; //take away the bar and collars

var plateCount=[];

setup.plates.forEach((x) => {
	var plate=Math.floor(w/(x*2));
	w=w-x*plate*2;
	plateCount.push(plate);
	});

return plateCount
}

function drawPlates(w) {
	plateDiv=document.getElementById("plates");
	var c=plateDiv.lastElementChild;
	while (c) {
		plateDiv.removeChild(c);
		c=plateDiv.lastElementChild;
	}
	var plates=getPlates(w);
	
	for (i=0;i<plates.length;i++){
		var weight=setup.plates[i];
		if (plates[i]>0){
			var plateHolder=document.createElement("div");
			plateHolder.id="plateHolder"+weight;
			plateHolder.classList.add("plateHolder");
			plateDiv.appendChild(plateHolder);
			
			for (c=0;c<plates[i];c++){
				var plate=document.createElement("div");
				plate.id=i+"plate"+c;
				plate.classList.add("plate");
				plate.classList.add("p"+weight*100);
				//plate.innerHTML=weight=" - " + c;
				plateHolder.appendChild(plate);
			
			} //end add multiple plates loop
			if (i==0) {var text=document.createElement("div"); //if there's multiple 25s
			text.innerHTML=plates[i]+"x";
			plateHolder.prepend(text); }
		}
	}
}
	
