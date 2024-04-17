<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter help</title>
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

<div class="heading">Documentation / How-to guides / Resources</div>
<p>The documentation is being improved every day as features (and bugs) are identified.</p>
<a href="docs.php">How To Guides</a><br>
<a href="https://www.youtube.com/user/whotookmeethed">Comp Of The Future Youtube Channel</a><br>
<a href="https://github.com/meethed/simpleLifter/">GitHub and source code</a><br>


<div class="heading">About simpleLifter</div>
<div class="slideshow">
<?php
  foreach(glob('resources/slides/*') as $image) {
    echo "<div class='slides'>";
    echo "<img class='slide-image' src='". $image."'></div>";
    ++$i;
 }
?>
  <!-- Next and previous buttons -->
  <a class="prev" onclick="moveSlides(-1)">&#10094;</a>
  <a class="next" onclick="moveSlides(1)">&#10095;</a>
  </div>
  <br>
  <!-- The dots/circles -->
  <div style="text-align:center">
    <?php
    for ($c=0;$c<$i;++$c) {
    echo "<span class='dot' onclick='currentSlide(".$c.")'></span>";
    }
    ?>
  </div>

  <p>simpleLifter is like LiftingCast from Wish.</p>
  <p>simpleLifter is a powerlifting competition management suite that started out as a simple digital light system that required no specialised hardware. It has now grown to include a competition management spreadsheet, a livestream overlay and scoreboard, and a bunch of other features that aim to maximise automation and make running a powerlifting competition easy.</p>

  <h2>Features:</h2>
  <ul>
    <li>Referee lights</li>
    <ul>
      <li>Uses mobile phone - no custom hardware required!</li>
      <li>Support for IPF colours</li>
      <li>Requires 3 referees to input a choice before showing the colours</li>
      <li>Triggers a 1 minute next-attempt submission timer</li>
    </ul>
    <li>Timers</li>
    <ul>
      <li>1 minute Bar Loaded attempt timer</li>
      <li>1 minute next-attempt submission timer</li>
      <li>Automatic 10/20 minute delay between S/B/D</li>
      <li>Custom 1-20 minute timer</li>
    </ul>
    <li>Spreadsheet</li>
    <ul>
      <li>Competition spreadsheet with automatic progression through attempts</li>
      <li>Support for varied bar and collar weights</li>
      <li>Support for Male, Female and MX categories</li>
      <li>Automatic IPF, Glossbrenner, Schwartz/Malone (thanks to OpenPowerlifting!) and DOTS calculations</li>
      <li>Custom age and weight categories</li>
    </ul>
    <li>Livestream overlay</li>
    <li>Livestream scoreboard</li>
    <li>Live results / progression page</li>
    <li>Archived results compatible with <a href="www.openpowerlifting.org">www.openpowerlifting.org</a> format</li>
  </ul>



<!-- Contact form-->
<div id="contact">
  <div class="heading">Contact</div>
  <p>If you've got any questions, hit me up on instagram (@comp.of.the.future) or using the contact form below:
  <?php if (isset($_SESSION["contact"])) {?>
  <script>alert("Thanks for your submission :)");</script>
  <?php unset($_SESSION["contact"]); };?>
  <form id="frmContact" action="contactform.php">
    <input class="frmInput" name="frmName" type="text" placeholder="Name" required>
    <input class="frmInput"name="frmEmail" type="text" placeHolder="Email" required>
    <textarea class="frmInput" name="frmQuery" placeHolder="Your query" required></textarea>
    <button class="btn frmInput" type="submit">Submit</input>
  </form>
</div>
<!-- End Contact Form -->


<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script>

let doingAuto=setInterval(function() {moveSlides(10)},3000);
let restartAuto=0;

let slideIndex = 1;
showSlides(slideIndex);
//slideshow

// Next/previous controls
function moveSlides(n) {
  if (n!=10) {//this means we've been clicked
    clearInterval(doingAuto);
    clearTimeout(restartAuto);
    restartAuto=setTimeout(function() {doingAuto=setInterval(function() {moveSlides(10)},3000)},8000);
  } else {n=1;}
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("slides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}


</script>
</html>

