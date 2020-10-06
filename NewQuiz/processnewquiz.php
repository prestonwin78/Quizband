<?php 
    include("./config.php");

    $user_id = 1;
    //exit if user is not signed in
    session_start();
    if(empty($_SESSION['user_id'])){
        header("Location: ../home.php");
        exit(); 
    } else {
        $user_id = $_SESSION['user_id'];
    }

    $quiz_title = "";
    $quiz_subject = "No Subject";
    $quiz_id = null;

    if(isset($_POST['quiz-title'])){
        $quiz_title = $_POST['quiz-title']; 
        $quiz_description = "No Description"; // default value
        if(isset($_POST['quiz-description'])){
            $quiz_description = $_POST['quiz-description'];
        }
        if(isset($_POST['quiz-subject'])){
            $quiz_subject = $_POST['quiz-subject'];
        }
        

        $quiz_arr = getArraysFromPOST();
        $answers = $quiz_arr['answers'];
        $questions = $quiz_arr['questions'];
        $answer_choices = $quiz_arr['answer_choices'];


        /* If query is valid, connect to database and insert quiz data */

        $dbconn = mysqli_connect(HOST, DBUSERNAME, DBPASSWORD, DBNAME);

        if($dbconn){
            $quiz_id = insertQuizInDatabase($dbconn, $quiz_title, $quiz_description, $quiz_subject, $user_id);
            insertQuestionsInDatabase($dbconn, $quiz_id, $questions);
            insertAnswerChoicesIntoDatabase($dbconn, $quiz_id, $answer_choices, $answers);
        }

        mysqli_close($dbconn);
    }


    function getArraysFromPOST(){
        // go through post array and store submitted values
        // based on the key:
        $answers = [];
        $questions = [];
        $answer_choices = [];
        foreach($_POST as $key => $value) {
            if(preg_match('/question[1-9]answer/', $key)){
                // question num is stored at 9th character of key
                // answer num is stored at 4th character of value
                $answers[$key[8]] = $value[3];          
            } else if(preg_match('/question[1-9]/', $key)){
                $questions[$key[8]] = $value;
            } else if(preg_match('/q[1-9]a[1-9]text/', $key)){
                // the key is, for example, 'q4a5 - answer choice 5
                // for question 4
                $answer_choices[$key[1]][$key[3]] = $value;
            }
        }
        return array(   
            'answers' => $answers,
            'questions' => $questions,
            'answer_choices' => $answer_choices
        );
    }


    // Inserts a new quiz entity in the database and returns the quiz ID generated
    function insertQuizInDatabase($dbconn, $quiz_title, $quiz_description, $quiz_subject, $user_id){
        /*  Make quiz query to insert a new quiz 
            quiz: (quiz_id, title, description, subject, creator) */
        $insert_quiz_query = "INSERT INTO quiz (title, description, subject, creator)
                            VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($dbconn);
        $quiz_id = null;
        if(!mysqli_stmt_prepare($stmt, $insert_quiz_query)){
            echo "error";
        } else {
            mysqli_stmt_bind_param($stmt, "ssss", 
                        $quiz_title, $quiz_description, $quiz_subject, $user_id);
            mysqli_stmt_execute($stmt);
            //get the last AUTO INCREMENT value from the database
            $quiz_id = mysqli_insert_id($dbconn);  
        }
        return $quiz_id;
    }


    function insertQuestionsInDatabase($dbconn, $quiz_id, $questions){
        /*  Make question query to insert each question
            question: (question_num, quiz_id, text) */
        $insert_question_query = "INSERT INTO question
                                VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($dbconn);

        if(!mysqli_stmt_prepare($stmt, $insert_question_query)){
            echo "error";
        } else {
            $q_num = 0;
            $q_text = "";

            mysqli_stmt_bind_param($stmt, "sss", $q_num, $quiz_id, $q_text);
            
            foreach($questions as $q_num => $q_text){
                mysqli_stmt_execute($stmt);
            }
        }
    }


    function insertAnswerChoicesIntoDatabase($dbconn, $quiz_id, $answer_choices, $answers){
        /* Make answer choice queries to input each answer choice
            answer_choice: (question_num, quiz_id, 
            answer_choice_num, choice_text, correct ) */
        $insert_answer_choice_query = "INSERT INTO answer_choice 
                                    VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($dbconn);

        if(!mysqli_stmt_prepare($stmt, $insert_answer_choice_query)){
            echo "error";
        } else {
            $q_num = 0;
            $c_arr = null;
            $c_num = 0;
            $c_text = "";
            $correct = false;

            mysqli_stmt_bind_param($stmt, "sssss",
                        $q_num, $quiz_id, $c_num, $c_text, $correct);
           
            foreach($answer_choices as $q_num => $c_arr){
                foreach($c_arr as $c_num => $c_text){
                    $correct = 0;   //False

                    //Check if answer choice has been set to correct
                    if($c_num == $answers[$q_num]){
                        $correct = 1;   //True
                    }
                    
                    mysqli_stmt_execute($stmt);
                }
            }
        }
    }


?>

<!-- show ID back to user -->

<!DOCTYPE html>
<html>
    <head>
        <title>Quizband</title>
        
        <!-- Bootstrap -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../styles.css">
        <link rel="stylesheet" type="text/css" href="./process-styles.css">
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
                <a id="title" class="text-dark" href="../home.php">Quizband</a>
                <a href="../index.html">About</a>
                <a href="../login.php">Sign In</a>
                <a href="../signup.php">Sign Up</a>
            </div>
            <div class="row">
                <div class="col-2 col-lg-4"></div>
                <div class="col-8 col-lg-4">
                    <div class="score-box">
                        <div class="quiz-score-box">
                            <h5><?php echo htmlspecialchars($quiz_title); ?></h5>
                            <h6 class="tag tag-Math text-dark"><?php echo htmlspecialchars($quiz_subject); ?></h6>
                        </div>
                        <div class="score-score-box">
                            <h6 class="text-secondary font-weight-normal">Quiz ID:</h6>
                            <h1 id="score" class="text-dark"><?php echo htmlspecialchars($quiz_id); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2 col-lg-4"></div>
                <div class="col-8 col-lg-4">
                    <div class="button-container text-center">
                        <button onclick="window.location.href='../home.php'" type="button" 
                            class="btn btn-light home-button">Back to home page</button>
                    </div>
                </div>
            </div>
        </div>
    </body>