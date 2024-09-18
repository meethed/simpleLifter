<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
<title>simpleLifter Competition Results</title>
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
  <div class="heading">Competition Results</div>
  <p>Only competitions that were configured to use the competition sheet are shown, even if they have no records or attempts. ie. if only the lights were used then there will be no entry for the competition. Please note that legacy simpleLifter competitions do not have all of the fields, and there are no plans to change this as I focus on improving the new version of the tool.</p>
  <p>Competitions are sorted in date order, newest first, and are grouped by month. You can filter by date or by competition name using the box below:</p>
  <!-- Search Bar -->
  <input class="frmInput" type="text" id="archiveSearch" onkeyup="filter()" placeholder="Filter by competition name...">
  <!-- Comp List -->
  <div class="archiveList" id="archiveList">
  	<!-- competitions -->
    <div class="">
    <?php
    if ($conn->connect_error) {
      echo "<div>Connection Failed, please try refreshing the page.</div>";
      die("connection error");
    }

    $sql = "SELECT compLetters, compName, startDate, endDate, sheet from comp WHERE sheet='1' AND endDate<=CURDATE() ORDER BY startDate DESC";
    $result = $conn->query($sql);
    $my="";
    if ($result->num_rows >0) {
      while ($row = $result->fetch_assoc()) {
        $nmy = substr($row["startDate"],0,7);
        if ($nmy!=$my) {echo "</div><div class='month-group'><h2>".$nmy."</h2>"; $my=$nmy;} ?>
        <a class='archive-entry' href='results.php?c=<?php echo $row["compLetters"]; ?>'>
        <div class='archive-description'><?php echo $row["startDate"]." - ".$row["endDate"]." - ".$row["compName"]; ?></div>
        </a>
    <?php }
  }

  //legacy simpleLifter results here
  $sql="select compLetters, compName,startdate,enddate from comps ORDER BY startdate DESC";
  $result=$conn->query($sql);
  $dummy=$result->fetch_assoc();
  if ($result->num_rows>0) {
    while ($row = $result->fetch_assoc()) {
      if (file_exists("./archive/simpleLifter/integrate/data/".$row["compLetters"].".json")) {
      $nmy=substr($row["startdate"],0,7);
      if ($nmy!=$my) {echo "</div><div class='month-group'><h2>".$nmy."</h2>";$my=$nmy;} ?>
      <a class='archive-entry old' href='oldresults.php?c=<?php echo $row["compLetters"]; ?>'>
      <div class='archive-description old'><?php echo $row["startdate"]." - ".$row["enddate"]." - ".$row["compName"]; ?></div>
      </a>
   <?php  }}
  }


?>


    </div>
    <br><br><br>
  </div>
<!-- End Content -->
</div>

<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->

</body>
<script>
sortbymonthgroup();

function sortbymonthgroup() {
let items=document.querySelectorAll(".month-group");
let ir=[];
for (let i in items) 
 if (items[i].nodeType==1) ir.push(items[i]);

ir.sort(function(a,b) {return a.innerHTML==b.innerHTML ? 0: (a.innerHTML > b.innerHTML ? -1: 1);})
for (i=0;i<ir.length;i++) archiveList.appendChild(ir[i]);

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

var mg=document.querySelectorAll(".month-group");

mg.forEach((e) => {
  if (e.querySelectorAll(".archive-entry").length == e.querySelectorAll(".archive-entry.hide").length) {
    e.classList.add("hide");
  } else {
    e.classList.remove("hide");;
  }
});

}//end function filter

</script>
</html>

