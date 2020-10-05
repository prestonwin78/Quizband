<?php
  /*
  echo $_POST['email'] . "</br>";
  echo $_POST['password'] . "</br>";
  echo $_POST['login-submit'] . "</br>";
  echo print_r($_POST);
  */

  $email = "";
  $password = "";
  $signin_error = false;

  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!empty($_POST['login-submit'])
      && !empty($_POST['email'])
      && !empty($_POST['password'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $dbconn = connectToDb();
        $credentials = getCredentialsFromDb($email, $dbconn);
        if($credentials !== null){
          $dbpassword = $credentials['password'];
          $user_id = $credentials['user_id'];
          if(password_verify($password, $dbpassword) === true){
            //Correct password
            session_start(); 
            $_SESSION['user_id'] = $user_id;
            header("Location: ./home.php");
            exit();
          } else {
            // Wrong password
            $signin_error = true;
          }
        } else {
          $signin_error = true;
        }
    }
  }


  function connectToDb(){
    $conn = mysqli_connect('localhost', "guest", "guestpass123", "quizband");

    if(!$conn){
        echo "Can't connect to database";
    } else {
        return $conn;
    }
  }


  function getCredentialsFromDb($email, $dbconn){
    $sql = "SELECT password, user_id FROM users
            WHERE email=?";
    $stmt = mysqli_stmt_init($dbconn);
    if(mysqli_stmt_prepare($stmt, $sql)){
      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $row = mysqli_fetch_assoc($result);
      if(isset($row['password']) && isset($row['user_id'])){
        return array(
          'password' => $row['password'],
          'user_id' => $row['user_id']
        );
      } else {
        return null;
      }
    }
  }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Quizband</title>
        
        <!-- Bootstrap -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        
        <link rel="stylesheet" type="text/css" href="styles.css">
        <link rel="stylesheet" type="text/css" href="./signupstyles.css">
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
              <a id="title" class="text-dark" href="home.php">Quizband</a>
              <a href="index.html">About</a>
              <a href="signup.php">Sign Up</a>
            </div>

            <div class="row">
                <div class="col-2 col-md-4 col-xl-5"></div>
                <div class="col-8 col-md-4 col-xl-2" >
                    <div class="signup-card bg-light">
                        <div class="header-container">
                            <h1 class="text-dark">Login</h1>
                        </div>
                        <form method="post">
                            <div class="input-block">
                                <input id="email" type="text" name="email" placeholder="Email"></input>
                            </div>
                            <div class="input-block">
                                <input id="password" type="password" name="password" placeholder="Password"></input>
                            </div>
                            <div class="text-block text-danger">
                                <p class="invalid" style=<?=($signin_error ? "display:block" : "display:none")?>>Error signing in.</p>
                            </div>
                            <div class="input-block buttons">
                                <button class="cancel" type="button" onclick="window.location.href='home.php'">Back</button>
                                <button class="submit bg-success text-light" type="submit" name="login-submit" value="submitted">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body> 
</html>