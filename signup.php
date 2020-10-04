<?php

    /*
    echo "sign up";
    echo "</br></br>";
    echo "submit: " . $_POST["submit"];
    echo "</br>";
    echo "email: " . $_POST["email"];
    echo "</br>";
    echo "password: " . $_POST["password"];
    echo "</br></br>";
    */

    $email = "";
    $password = "";
    $password_error = false;
    $email_error = false;
    $error_text = "";

    $dbconn = connectToDb();

    if(isset($_POST["submit"])){
        unset($_POST["submit"]);

        if(isset($_POST["email"])){
            $email = $_POST["email"];
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                if(isEmailTaken($email, $dbconn)){
                    $email_error = true;
                } 
            } else {
                $email_error = true;
            }
        } else {
            $email_error = true;
        }

        if(isset($_POST["password"])){
            $password = $_POST["password"];
            $password_error = !checkPasswordComplexity($password);
        } else {
            $password_error = true;
        }

        if(!$email_error && !$password_error){
            // Put into db
            createNewUserInDb($email, $password);
        } else {
            exit();
        }
    }

    /* returns true if password meets complexity requirements */
    function checkPasswordComplexity($password){
        $isError = false;

        if(strlen($password) <= 7){
            $isError = true;
        }

        // Validate complexity requirements
        if(!preg_match("/[a-z]/", $password)){
            // Password doesn't have lowercase
            $isError = true;
        }
        
        if(!preg_match("/[A-Z]/", $password)){
            // Password doesn't have uppercase
            $isError = true;
        }
        
        if(!preg_match("/[0-9]/", $password)){
            // Password doesn't have a digit
            $isError = true;
        }

        if(preg_match("/\s/", $password)){
            // Password contains whitespace
            $isError = true;
        }

        return !$isError;
    }

    function connectToDb(){
        $conn = mysqli_connect('localhost', "guest", "guestpass123", "quizband");
    
        if(!$conn){
            echo "Can't connect to database";
        } else {
            return $conn;
        }
    }


    function isEmailTaken($email, $dbconn) {
        $numEmails = 0;
        $sql = "SELECT COUNT(*) FROM users
                WHERE email=?";
        $stmt = mysqli_stmt_init($dbconn);
        if(mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $email);
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                $numEmails = mysqli_fetch_row($result)[0];
            } else {
                exit();
            }
        }
        
        if($numEmails > 0){
            return true;
        } else {
            return false;
        }
    }


    function createNewUserInDb($email, $password){
        echo "in createnewuser";
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
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
                <a>Sign Up</a>
            </div>

            <div class="row">
                <div class="col-2"></div>
                <div class="col-8">
                    <div class="signup-card bg-light">
                        <div class="header-container">
                            <h1 class="text-dark">Sign Up</h1>
                        </div>
                        <form method="post">
                            <div class="input-block">
                                <label for="email">Email</label>
                                <input id="email" type="text" name="email"></input>
                            </div>
                            <div class="input-block">
                                <label for="password">Password</label>
                                <input id="password" type="password" name="password"></input>
                            </div>
                            <button type="button" onclick="window.location.href='index.html'">Back</button>
                            <button type="submit" name="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>