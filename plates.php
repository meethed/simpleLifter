<!DOCTYPE html>
<html>
<head><title>Plates</title>
<link rel="Stylesheet"  href="./resources/styles.css">
</head>
<body style="overflow:hidden">

<div class="plateHolder" id="plates"></div>
<div id="heading"><h1 id="next">Next Load:</h1></div>
</body>

<script>
var globalplates=[25, 20, 15, 10, 5, 2.5, 1.25, 0.5, 0.25];


function drawPlates(w){
  plateDiv=document.getElementById("plates");
  plateDiv.style.display="inline-flex";
  var c=plateDiv.lastElementChild;
  while (c) {
    plateDiv.removeChild(c);
    c=plateDiv.lastElementChild;
  }
  if (!w) return false;
  var plates=getPlates(w);

  for (i=0;i<plates.length;i++){
    var weight=globalplates[i];
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

      if (i==0) {var text=document.createElement("div"); //if>
      text.innerHTML=plates[i]+"x";
      text.classList.add("plateText");
      plateHolder.prepend(text); }
    }
  }
} //end drawplates fuinction


function getPlates(w){
	w=w-25;

	var plateCount=[];

	globalplates.forEach((x) => {
		var plate=Math.floor(w/(x*2));
		w=w-x*plate*2;
		plateCount.push(plate);
	});
	return plateCount
} //end getPlates

function updateNext(w) {
var nextPlates=getPlates(w);
var plateString="";

nextPlates.forEach((x,id) => {
	if (x>1) {plateString+=x + "x" + globalplates[id]+" | "} else
	if (x>0) {plateString+=globalplates[id] + " | "}
	
	});

document.getElementById("next").innerHTML="Next Load | "+ plateString;




}


</script>

</html>

