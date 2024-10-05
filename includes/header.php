<div class="navbar-top">
  <a href="index.php" class="navbar-item btn"><b>simpleLifter</b> powerlifting management</a>

  <!-- right floating links, hidden on mobile -->
  <div id="navbar-right" class="navbar-right hide-small">
    <a class="btn icon" href="javascript:void(0);" onclick="document.getElementById('navbar-right').classList.toggle('responsive');">&#9776;</a>
    <?php if (isset($_SESSION["compLetters"])) { ?><a class="btn" href="manager.php">&#x1F428 Comp Manager</a><?php ;}; ?>
    <a class="btn" href="index.php#compList">Active Comps</a>
    <a class="btn" href="addcomp.php">New Competition</a>
    <a class="btn" href="archive.php">Competition Results</a>
    <a class="btn" href="news.php">News/Updates</a>
    <a class="btn" href="help.php">Help</a>
  </div>
</div>

<script>
const nbr=document.getElementById("navbar-right");
window.addEventListener("click",f);

function f(e) {
if (e.target.parentElement!=nbr) nbr.classList.remove("responsive");
}
</script>
