<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body>
<!-- navbar -->
<?php include(ROOT_PATH."/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">

<div class="heading">Documentation and How To</div>
<p>Ok here's some simple how to things that might help you.You can try searching to see if that helps</p>
<h1>Competition setup</h1>
<div class="collapsible">New comp starter guide</div>
<div class="collapsiblecontent">
So you want to make a new comp ay?
<ol>
<li>Click on "New Competition" on the menu</li>
<li>Input the competition name, federation, and start date</li>
<li>Choose the number of days, sessions and platforms (from 1-10) that the comp will run</li>
<li>Delete unnecessary sessions by highlighting them and clicking on them (eg if you only need 1 platform on the final day delete the others)</li>
<li>Choose an access code (note this is for all access - officials, referees, tech desk)</li>
<li>Please consider providing an email so I can reach out. This is a mandatory field but if it's not a valid email I won't care</li>
<li>Click submit and you're done!!</li>
</ol>
</div>
<div class="collapsible">Setup a small comp in simpleLifter</div>
<div class="collapsiblecontent">
<ul>
<li>First you need to login:</li>
<ol>
<li>Click on the competition on the <a href="index.php">simpleLifter homepage</a> or by clicking on Active Comps on the menu. </li>
<li>Enter your access code and press enter or click 'Official Access'</li>
</ol>
<li>You're now logged in! The competition manager will display giving you options to run the comp (as tech desk, platform display or referee), as well as setup to enrol lifters. This is ideal for multi session multi day competitions, but not for a simple one or two session competition</li>
<li>Click on your specific session, and then 'Tech Desk' to load simpleLifter spreadsheet.</li>
<li>To add lifters, click on the 'Add Lifter' button at the bottom of the spreadsheet.</li>
<li>Add the lifter entry and weigh in data. For their division and weight class, you can simply right click to change the data. Note to get the weight class you need to enter their gender in the division cell first!</li>
<li>Set the active lift to Squat 1 and set the active Group, then you're good to go!</li>
</ul>
</div>
<div class="collapsible">Setup a large comp</div>
<div class="collapsiblecontent">
<h4>simpleUpload and bulk import via CSV</h4><p>simpleUpload was an excel spreadsheet that allowed bulk data entry for a multi day/session/platform competition. It needed Microsoft Excel and it needed to be running on Windows (thanks to the limitations of VBA). It's no longer used because of the new competition setup system.</p>
<p>Once you've logged in, click on "Competition Setup" in the competition manager. From there you can alter sessions, and add or remove lifter registration data. If you have a lot of lifters in excel or google sheets, you can import via CSV. The order of columns isn't important, but the first (header) row has to be as per the template file.</p>
<h4>Assigning lifters to a session</h4>
<p>To assign lifters to a session, simply click on the day/session/platform (DSP) box on the right to assign a lifter to a particular session. Should you need to make last minute changes - say because a lifter has pulled out and flights or sessions must change - this is now very easy and there's no risk of losing data.</p>
<h4>Lot numbers</h4><p>Lot numbers can be generated randomly either for the entire competition (recommended) or by flights (eg. Flight A gets a lower lot number than Flight B). Note that once you press the number it'll immediately upload everything so please be careful!</p>
</div>
<div class="collapsible">Change weight classes and federation rules</div>
<div class="collapsiblecontent">
<p>At the top of the "Competition Setup" page from competition manager, you can change the federation rules. Rules will determine:
<ul>
<li>Weight Classes</li>
<li>Allowed Equipment</li>
<li>Allowed Lifts</li>
<li>Default bar weight</li>
<li>Break timers</li>
<li>Simple (red/white) or complex (red/blue/yellow/white) lights</li>
</ul>
</div>
<div class="collapsible">Suggested venue setup layout</div>
<div class="collapsiblecontent">
<p>Pictures will go here. But I recommend:</p>
<ul>
<li>A computer running simpleLifter for tech desk, and using the HDMI out to drive the platform display for the platform crew</li>
<li>A computer driving the simpleLifter live scoresheet for the warmup areas and waiting area - it can use an HDMI splitter</li>
<li>A separate computer for OBS - it allows better camera and audio positioning</li>
<li>Another screen for the live scoresheet for the audience</li>
</ul>
</div>
<h1>Referees</h1>
<div class="collapsible">Change referee positions</div>
<div class="collapsiblecontent">
<p>Swiping back from the refere page will no longer log you out. Swipe back and rechoose your new position. Referees won't be logged out, but need to ensure they've picked the correct session.</li>
</div>
<div class="collapsible">Troubleshooting referee problems</div>
<div class="collapsiblecontent">
<p>Tap and hold!!! Don't just peck at the screen like a pigeon would. If you're not logged in correctly you'll get bumped to the homepage. There should therefore be fewer issues with people getting logged out but still accessing the referee page.</p>
</div>
<h1>Live stream</h1>
<div class="collapsible">Setup OBS overlays</div>
<div class="collapsiblecontent">WIP. Short version, add http://cotf.zapto.org/multiStream.php?c=XXX as a browser source to OBS.You can use the platform/session filter to only display the stream data from certain sessions...see below.</div>
<div class="collapsible">Setup OBS scoreboard</div>
<div class="collapsiblecontent">WIP. Short version, add http://cotf.zapto.org/scoreboard.php?c=XXX as a browser source to OBS.</div>
<div class="collapsible">Setup OBS for multiple sessions</div>
<div class="collapsiblecontent">WIP Sorry. As above, but include "&f=1" at the end for either the multiStream or scoreboard. The 1 or 2 or whatever will represent the PLATFORM. If you've only got one platform, multiStream/scoreboard will work out which session is currently active automatically.</div>
<div class="collapsible">Auto trigger scoreboard on 10 minute timer</div>
<div class="collapsiblecontent">In future versions, this will happen at completion of squat or bench but requires a specific OBS setup. Right now the timer is automatic, not the scoreboard scene change. A 20 minute timer will be triggered when there is only one flight. You can disable it in simpleLifter using the settings button.</div>
<div class="collapsible">Add and enable lifter profile pictures</div>
<div class="collapsiblecontent">WIP Sorry</div>
<div class="collapsible">Change the layout of the stream overlay</div>
<div class="collapsiblecontent">To do this, you need to be a gun at CSS:
<ol>
<li>Load up multiStream.php in a browser and press F12 (debug mode) to identify the styles and classes that you want to change.</li>
<li>OBS has the ability to override CSS in a browser source. In the 'Custom CSS' box you can completely tweak the CSS of all of the overlays. Go nuts!! You're limited in what you can do here, which I acknowledge isn't ideal. But it's better than nothing.</li>
</ol>
</div>
<h1>simpleLifter</h1>
<div class="collapsible">simpleLifter quick start guide</div>
<div class="collapsiblecontent">
<p>Depending on who you talk to there isn't much to it. Add lifters, set to Squat 1 then as a tech desk you have very limited need to get involved:</p>
<ul>
<li>If it's on auto ref you don't need to press good lift / no lift. Otherwise, monitor the referees / lights and press goodlift or no lift if necessary.
<li>If the head ref isn't doing it you need to press bar loaded to start the 1 minute timer. NOTE it's now on the timer itself, on the right.</li>
<li>When a lifter submits a second/third attempt or change, enter it in the box. </li>
<li>At completion of the competition, the results will automatically be generated.</li>
</ul>
</div>
<div class="collapsible">Change settings</div>
<div class="collapsiblecontent">Click on setup in simpleLifter, or competition setup on the manager page. There will be more and more settings added as I progress.</div>
<div class="collapsible">Add or delete lifters</div>
<div class="collapsiblecontent">To add a lifter, either do it in the competition setup option before the competition starts (ideal) or simply press "Add Lifter" on the spreadsheet. To delete lifters, delete the lifter name and lot number from either sheet and that row will be removed.</div>
<div class="collapsible">simpleLifter techdesk troubleshooting</div>
<div class="collapsiblecontent">Sometimes things go wrong. It should now be safe to press F5 and refresh the page if anything goes wrong, but i'm not expecting that it will in a predictable way! Usually when it goes wrong its' because someone messed with it.</div>
<h1>Advanced features</h1>
<div class="collapsible">Set auto ref advancement</div>
<div class="collapsiblecontent">If you have enabled auto ref progression in settings or comp setup, when the 3 referees make their decision the comp spreadsheet will automatically goodlift/no lift the lifter based on the number of red or white lights received. ie. If there are 2 or 3 red lights, the lifter will be given a no lift and the spreadsheet will progress to the next lifter. This option can be disabled or enabled whenever, but it's a good idea to let the tech desk know!!</div>
<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script>
var acc = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}




function filter() {
var filter, a, i, items, txtValue;

filter=document.getElementById("archiveSearch").value.toUpperCase();
items = document.getElementById("archiveList").querySelectorAll(".archive-entry");

for (i=0; i< items.length; i++) {
  a = items[i].querySelector(".archive-description").innerText.toUpperCase();
  if (a.indexOf(filter) > -1) {
    items[i].classList.remove("hide"); } else { items[i].classList.add("hide");;
  }
}
}
</script>
</html>

