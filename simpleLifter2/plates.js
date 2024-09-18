var
 globalPlates = [25, 20, 15, 10, 5, 2.5, 1.25, 0.5, 0.25];

function drawPlates(h,w,b) { //draws plates of total [w]eight minus [b]ar weight into [h]ost div

  w=w-b;
  while (h.firstChild) h.removeChild(h.lastChild);
  if (w<0) return false; // do this after clearing the display so if there's nothing then don't try and display anything
  var plates=getPlates(w);

  for (i=0;i<plates.length;i++){
    var weight=globalPlates[i];
    if (plates[i]>0){
      var plateHolder=document.createElement("div");
      plateHolder.id="plateHolder"+weight;
      plateHolder.classList.add("plateHolder");
      h.appendChild(plateHolder);

      for (c=0;c<plates[i];c++){
        var plate=document.createElement("div");
        plate.id=i+"plate"+c;
        plate.classList.add("plate");
        plate.classList.add("p"+weight*100);
        //plate.innerHTML=weight=" - " + c;
        plateHolder.appendChild(plate);

      } //end add multiple plates loop

      if (i==0) {var text=document.createElement("div"); //if it's a 25kg plate say how many there are
      text.innerHTML=plates[i]+"x";
      text.classList.add("plateText");
      plateHolder.prepend(text); }
    }
  }

} //end function drawPlates

function getPlates(w) { //returns an array with the count of each plate
	var plateCount=[];

	globalPlates.forEach((x) => {
		var plate=Math.floor(w/(x*2));
		w=w-x*plate*2;
		plateCount.push(plate);
	});
	return plateCount
} //end function getPlates

function getLoad(w) {
  var plateString="";
  var nextPlates=getPlates(w);

  nextPlates.forEach((x,id) => {
  	if (x>1) {plateString+=x + "x" + globalPlates[id]+" / "} else
  	if (x>0) {plateString+=globalPlates[id] + " / "}
  	});
  plateString+=" collar";
  return plateString;
} //end function getLoad

