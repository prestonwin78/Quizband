<?php
  session_start();
  //If the user is signed in, sign out
  if(!empty($_SESSION['user_id'])){
    session_destroy();  //stop the current session
  } 

  header("Location: ./home.php");
?>