config = {};

fetch("./users/"+compLetters+".json").then(response => response.json()).then(data => {

config=data;
if (!config.showlights) {lightsTimers.classList.remove("visible") } else {lightsTimers.classList.add("visible");};
});


function updateConfig() {
fetch("./users/"+compLetters+".json").then(response => response.json()).then(data => {

config=data;
if (!config.showlights) {lightsTimers.classList.remove("visible") } else {lightsTimers.classList.add("visible");};

});
} //end function updateConfig
