<?php

session_start();

$conn = mysqli_connect("localhost","lightsuser","lights","lightsdb");

if (!$conn) {
  die("Error connecting to database: ".mysqli_connect_error());
}



  define("ROOT_PATH", realpath(dirname(__FILE__)));

?>


