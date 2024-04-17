<?php include_once("./includes/config.inc"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>simpleLifter</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="./resources/newstyles.css">
</head>
<body>
<!-- navbar -->
<?php include("./includes/header.php"); ?>
<!-- end navbar -->

<!-- Content -->
<div class="content">
<div class="heading">Administrative Tools</div>
<h1>File Upload<?php if (isset($_SESSION["filemessage"])) {echo " - ".$_SESSION["filemessage"];unset($_SESSION["filemessage"]);}?></h1>
<form name="frmUp" action="./utils/up.php" enctype="multipart/form-data" method="post">
<input type="file" name="uploadedFile"><input type="submit" value="Upload">
</form>
<hr>
<h1>Edit Dates<?php if (isset($_SESSION["datemessage"])) {echo " - ".$_SESSION["datemessage"];unset($_SESSION["datemessage"]);}?></h1>
<form name="frmDate" action="./utils/dates.php" method="post">
<input type="text" name="frmLetters" placeholder="Comp Letters"><br>
<input type="password" name="frmAccessLetters" placeholder="Comp Access Code"><br>
New Start Date: <input type="date" name="frmStart"><br>
New End Date: <input type="date" name="frmEnd"><br>
<input type="submit" value="Change Dates">
</form>
<hr>
<h1>Edit Access Code<?php if (isset($_SESSION["accessmessage"])) {echo " - ".$_SESSION["accessmessage"];unset($_SESSION["accessmessage"]);}?></h1>
<form name="frmAccess" action="./utils/changePwd.php" method="post">
<input type="text" name="frmAccessLetters" placeholder="Comp Letters"><br>
<input type="password" name="frmOldPwd" placeholder="Old Access Code"><br>
<input type="password" name="frmNewPwd" placeholder="New Access Code"><br>
<input type="submit" value="Change Access Code">
</form>
<?php if ($_SERVER['REMOTE_ADDR']=="49.176.180.136") { ?>
<div class="heading">Email contacts</div>
<div class="emailbox">
<?php
$c=glob("./utils/contacts/*.txt");
rsort($c, SORT_NATURAL);
foreach($c as $f) {
  $content = file($f);
  $d=date("d m y",basename($f,".txt"));
  echo "<h2 style='text-decoration:underline'>".$d." - ".$content[0]."</h2>";
  array_shift($content);
   foreach($content as $lines) {
    echo $lines."<br>";
  };
  echo "<br><a href='#top'>Top</a><hr width='50%'><br><br>";

}
?>
</div>
<?php } ?>
</div>
<!-- End Content -->

<!-- Footer -->
<?php include("./includes/footer.php"); ?>
<!-- End Footer -->

</body>
<script>
</script>
</html>

