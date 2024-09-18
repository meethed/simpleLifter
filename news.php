<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
<title>simpleLifter news</title>
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
<!-- Header -->
  <div class="heading">News and updates</div>
<!-- End Header -->

<!-- Add updates here -->

<?php

$c=glob("./news/*.txt");
rsort($c, SORT_NATURAL);
foreach($c as $f) {
  $content = file($f);
  $d=date_create_from_format("ymd",basename($f,".txt"));
  $d=date_format($d,'d M Y');
  echo "<div class='news-entry'>";
  echo "<div class='news-title'>".$content[0]."</div>";
  echo "<p>".$d."</p>";
  array_shift($content);
   foreach($content as $lines) {
    echo $lines;
  };
  echo "<a href='#top'>Top</a>";
  echo "</div>";

}
?>

	<div class="heading"></div>
	<img class="footer-image" src="./resources/happyevan.jpg"></img>

<!-- End Content -->
</div>


<!-- Footer -->
<?php include(ROOT_PATH."/footer.php"); ?>
<!-- End Footer -->
</body>
<script>
</script>
</html>

