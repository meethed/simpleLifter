<?php
include_once("./includes/config.inc");

$errors = array ();

if (isset($_POST["go"])) { //if they pressed the go button

  $compLetters=filter_input(INPUT_POST, "compLetters", FILTER_SANITIZE_SPECIAL_CHARS);
  $pwd=filter_input(INPUT_POST, "pwd", FILTER_SANITIZE_SPECIAL_CHARS);

  if (empty($compLetters)) {array_push($errors, "No Competition Selected");}
  if (empty($pwd)) {array_push($errors, "Access code is blank");}
  if (empty($_POST)) {array_push($errors, "Access denied");}
  if(empty($errors)) {
    $pwd = crypt($pwd,substr($compLetters,1,2));
    $sql = "SELECT * from comp where compLetters='" . $compLetters . "' and hish='" .$pwd. "' LIMIT 1";

    $result = $conn->query($sql);
    if (mysqli_num_rows($result) >0) {

      $data=$result->fetch_assoc();
      $_SESSION=$data;
      $_SESSION["message"]="Access code recognised";

      //for the qrcode generator
      function generateRandomString($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {  
          $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
      }
      //generate a new random string each time you hit the manager page. So if you ever refresh the page OR 25000 seconds (7 hours) passes then previously logged in people can't$
      $_SESSION["token"]=generateRandomString();
      $_SESSION["tokenexp"]=time()+25000;
      $sql="update comp set token='".$_SESSION["token"]."',tokenexp=".$_SESSION["tokenexp"]. " where compLetters='".$_SESSION["compLetters"]."'";
      $result=$conn->query($sql);

      header("location: manager.php");
      exit(0);
    } else { //end if num rows >0
      array_push($errors, "Wrong access code");
    } //end else 
  } //end if empty errors
} //end if isset post

if (count($errors) > 0) : ?>
  <div class="message error validation_errors" >
        <?php foreach ($errors as $error) : ?>
          <p><?php echo $error ?></p>
        <?php endforeach ?>
  <a href="index.php">Home</a>
  </div>
<?php endif ?>
