//click handler for the buttons
window.addEventListener('click',function(e){
	if(e.target.id=="btnGood") { //if it's the good button
		lifters.activeRw.setLift(lifters.activeLi,1);
		lifters.incrementRow;
	}
	if(e.target.id=="btnNo") { //if it's the no lift button
		lifters.activeRw.setLift(lifters.activeLi,-1);
		lifters.incrementRow;
	}	
	if(e.target.id=="btnSave") { //if it's the save button
		lifters.saveLocal();
	}		
	if(e.target.id=="btnLoad") { //if it's the load button
		lifters.loadLocal();
		setupCx();
	}	
	if(e.target.id=="curGroup"){
		showGroups();
	}
	if(e.target.id=="curLift"){
		showLifts();
	}
	if(e.target.id=="btnUtil"){
		showUtils();
	}
  if(e.target.id=="btnTimer"){
    showTimers();
  }
	if(e.target.id=="btnBar"){
		setBarLoaded();
	}
});


//handler for the enter and down keys to move the active cell down one, like you would in a spreadsheet
window.addEventListener('keydown',function(e){
	closeAllLists();
	if(e.target.classList.contains("td"))
		if(e.key=='Enter'||e.key=="ArrowDown"){
			moveDown(e);
} else
	if(e.key=="ArrowUp") {
		e.preventDefault();
		var row=e.target.parentElement;
		//updateFromDiv();
		var col=e.target.id.slice(-2);
		var newrow=row.previousElementSibling.id;
		if (row==row.parentElement.firstElementChild.nextElementSibling) {newrow=row.parentElement.lastElementChild.id}
		var p = document.getElementById(newrow+col),
			s = window.getSelection(),
			r = document.createRange();
			r.setStart(p, 0);
			r.setEnd(p, 0);
			s.removeAllRanges();
			s.addRange(r);
			window.getSelection().selectAllChildren( p);
	} else
	if(e.key=="ArrowLeft"){
		e.preventDefault();
		var p;
		if (e.target==e.target.parentElement.firstElementChild) {return false }else p=e.target.previousElementSibling;
		while (p.contentEditable!="plaintext-only") {
			p = p.previousElementSibling;}
    s = window.getSelection(),
    r = document.createRange();
		r.setStart(p, 0);
		r.setEnd(p, 0);
		s.removeAllRanges();
		s.addRange(r);
		window.getSelection().selectAllChildren( p);
	return false;
	} else
	if(e.key=="ArrowRight") {
		e.preventDefault();
	
		var p = e.target.nextElementSibling;
		while (p.contentEditable!="plaintext-only" && p!=p.parentElement.lastElementChild) {
			p = p.nextElementSibling;}
    s = window.getSelection(),
    r = document.createRange();
		r.setStart(p, 0);
		r.setEnd(p, 0);
		s.removeAllRanges();
		s.addRange(r);
		window.getSelection().selectAllChildren( p);
	return false;
	}
},true); //end arrow / enter handler


function activateCell(row,col){ //this is where we will activate a particular cell for editing

} //end function activateCell


function moveDown(e){
	if (e instanceof Event) {
		e.preventDefault();
		var row=e.target.parentElement;
		var col=e.target.id.slice(-2);
	} else {
		var row=e.parentElement;
		var col=e.id.slice(-2);
	}
	//updateFromDiv();
	
	if (row==row.parentElement.lastElementChild) {row=row.parentElement.firstElementChild}
	var newrow=row.nextElementSibling.id;
	var p = document.getElementById(newrow+col),
	s = window.getSelection(),
	r = document.createRange();
	r.setStart(p, 0);
	r.setEnd(p, 0);
	s.removeAllRanges();
	s.addRange(r);
	window.getSelection().selectAllChildren( p);
	return false;
}


