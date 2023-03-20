<!DOCTYPE html>
<html>
<head>
<title>time delay offset test
</title>
</head>
<body>
<div>
<div>The time on this computer is:</div>
<div id="ct">xxx</div>
</div>
<div>
<div>The time on the server is:</div>
<div id="st">xxx</div>
</div>
</div>
</body>
<script>
document.getElementById("ct").innerHTML = Date.now()
document.getElementById("st").innerHTML = <?php echo time(); ?>;
</script>
</html>
