var dolightWindow=0, doplateWindow=0, lightWindow, plateWindow;
//lightWindow=window.open("popupLights.html","","menubar=no,scrollbars=no,status=no,toolbar=no");
lightWindow=window.open("","light_window_popup","menubar=no,scrollbars=no,status=no,toolbar=no,width=1920,height=1080");
if (lightWindow.location.href === "about:blank") {
lightWindow=window.open("../../plateLights.php","light_window_popup","menubar=no,scrollbars=no,status=no,toolbar=no,width=1920,height=1080"); 
}
dolightWindow=1;
//plateWindow=window.open("popupPlates.html","","menubar=no,scrollbars=no,status=no,toolbar=no");
doplateWindow=1;
