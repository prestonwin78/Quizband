<?php 

    $dbconn = mysqli_connect("localhost", "testuser1", "pass123", "quizband");
    
    if(!$dbconn){
        echo "error connecting";
    }

    $arr = getQuizIds($dbconn);
    //get array holding info about each quiz
    //  in form     [quiz-id] => [description, name, ...]
    $quiz_data = getQuizData($dbconn, $arr);


    echo print_r($quiz_data);

    /* returns an array of 3 unique quiz IDs */
    /* saves a call to MySQL for 3 random quizzes */
    function getQuizIds($dbconn){
        $quiz_arr = [];
        $total_quizzes = getTotalQuizzes($dbconn);
        for($i = 0; $i < 3; $i++){
            $rand = random_int(1, $total_quizzes);
            //keep getting random numbers until rand is unique
            while(in_array($rand, $quiz_arr)){
                $rand = random_int(1, $total_quizzes);
            }
            $quiz_arr[$i] = $rand;
        }
        return $quiz_arr;
    }

    function getTotalQuizzes($dbconn){
        $query = "SELECT COUNT(*) FROM quiz";
        $result = mysqli_query($dbconn, $query);
        if(!$result){   
            echo "Error";
        } else {
            return mysqli_fetch_row($result)[0];
        }
    }

    //Returns array of quiz data
    function getQuizData($dbconn, $arr){
        $query = "SELECT * FROM quiz
                  WHERE quiz_id=?";
        $stmt = mysqli_stmt_init($dbconn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            echo "error";
        } else {
            $q_id = null; // dummy value
            $quiz_data = [];    
            mysqli_stmt_bind_param($stmt, "i", $q_id);

            foreach($arr as $q_id){
                if(!mysqli_stmt_execute($stmt)){
                    echo "Error";
                } else {
                    $result = mysqli_stmt_get_result($stmt);
                    $quiz_data[$q_id] = mysqli_fetch_row($result);
                    //echo print_r($quiz_data);
                }

                /* make sure to htmlescape when outputting
                    to the browser */
            }
            return $quiz_data;
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
        <link rel="stylesheet" type="text/css" href="home-styles.css">
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
                <b>Quizband</b>
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
            </div>
            <div class="row main-section">
                <h1>Hello, John.</h1>
            </div>
            <div class="row main-section">
                <div class="divider bg-dark">
                </div>
            </div>
            <div class="row main-section">
                <div class="col-0 col-lg-2"></div>
                <div class="col-6 col-lg-4">
                    <div class="row">
                        <h2 class="text-light">Take a Quiz</h2>
                    </div>
                    <div class="row">
                        <div class="card quiz-card">
                            <div class="card-body">
                                <h5 class="card-title">General History</h5>
                                <p class="card-text">A short quiz which has some general history and geography questions. </p>
                                <h6 class="tag tag-history">History</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card quiz-card">
                            <div class="card-body">
                                <h5 class="card-title">General Science</h5>
                                <p class="card-text">A short quiz which has some general science questions. </p>
                                <h6 class="tag tag-science">Science</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="row">
                        <h2 class="text-light">My Profile</h2>
                    </div>
                    <div class="row">
                        <div class="card quiz-card">
                            <div class="card-body">
                                <h5>New Quiz</h5>
                                <div class="plus-container">
                                    <svg width="2em" height="3em" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>