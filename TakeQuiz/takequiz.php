<?php 
     $quiz_id = 1;  //TODO: this needs to be set beforehand
     $dbquestions = [];
     $choices = [];
     
     $dbconn = mysqli_connect("localhost", "USERNAME", "PASSWORD", "quizband");
     
     //check connection
     if(!$dbconn){
         echo "Error connecting to database: " . mysqli_connect_error();
     } else {
         //Get questions
        $querysql = "SELECT question_num, text, answer_choice_num, choice_text
                     FROM question NATURAL JOIN answer_choice
                     WHERE quiz_id = 1 ORDER BY question_num";
        $query_result = mysqli_query($dbconn, $querysql);
        $dbquestions = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

        //Build answer choices array in form of
        //choices[questionNum][answerChoiceNum] == choice text
        foreach ($dbquestions as $row){
            $choices[$row['question_num']][$row['answer_choice_num']] = $row['choice_text'];
        }

         //TODO: free result
         //TODO: get title from database
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
        <link rel="stylesheet" type="text/css" href="../styles.css">
        <link rel="stylesheet" type="text/css" href="./takequizstyles.css">
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
                <b>Quizband</b>
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
            </div>
        </div>
        <div class="row main-section">
            <div class="col-12 text-center">
                <h1 id="create-quiz-text">General History</h1>
            </div>
        </div>
        
        <form action="results.php">
            <div class="row main-section">
                <div class="col-3"></div>
                <div class="col-6 quizcard bg-light">
                    <h2 class="question-text">What is the capital of Argentina?</h2>
                    <div class="answer-options">
                        <div class="answer-choice">
                            <input type="radio" id="q1a1" name="question1" value="Belarus">
                            <label for="q1a1">Belarus</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q1a2" name="question1" value="Caracas">
                            <label for="q1a2">Caracas</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q1a3" name="question1" value="Buenos Aires">
                            <label for="q1a3">Buenos Aires</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q1a4" name="question1" value="Bolivia">
                            <label for="q1a4">Bolivia</label>
                        </div>
                    </div>
                </div>
                <div class="col-3"></div>
            </div>
            <div class="row main-section">
                <div class="col-3"></div>
                <div class="col-6 quizcard bg-light">
                    <h2 class="question-text">Who was the first president of the United States?</h2>
                    <div class="answer-options">
                        <div class="answer-choice">
                            <input type="radio" id="q2a1" name="question2" value="George Washington">
                            <label for="q2a1">George Washington</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q2a2" name="question2" value="Abraham Lincoln">
                            <label for="q2a2">Abraham Lincoln</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q2a3" name="question2" value="Thomas Jefferson">
                            <label for="q2a3">Thomas Jefferson</label>
                        </div>
                        <div class="answer-choice">
                            <input type="radio" id="q2a4" name="question2" value="Theodore Roosevelt">
                            <label for="q2a4">Theodore Roosevelt</label>
                        </div>
                    </div>
                </div>
                <div class="col-3"></div>
            </div>
            <div class="row main-section">
                <div class="col-6"></div>
                <div class="col-6">
                    <button id="cancel" class="btn btn-light">Cancel</button>
                    <button id="submit" class="btn btn-primary shadow">Submit</button>
                </div>
            </div>
            <div id="blank-row" class="main-section"></div>
            <input type='hidden' id="quizid" name='quizid' value='1'>
        </form>
    <body>