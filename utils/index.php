<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../resources/styles.css">
<title>Utilities Menu</title>
</head>
<body>
<h1>Utilities</h1>
<h2>File Upload</h2>
<?php 
 if (isset($_SESSION["message"])) {
  echo "<p class='notification'>".$_SESSION["message"]."</p>";
  unset($_SESSION["message"]);
 };
 print_r($_SESSION);
?>

<form action="./up.php" enctype="multipart/form-data" method="POST">
<input type="file" id="file-upload" name="uploadedFile">
<input type="submit" name="uploadBtn" value="Upload">
</form>
<h2>Rosie Upload</h2>
<form action="./rosie.php" enctype="multipart/form-data" method="POST">
<input type="file" id="file-upload" name="uploadedFile">
<input type="submit" name="uploadBtn" value="Upload">
</form>


<h2>Raw Dump</h2>
<a href="admin.php">DB dump</a>


</body>
</html>
