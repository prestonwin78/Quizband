<?php 
    //Get quiz info from database
    //Compare results with form data
    //Calculate score
    //TODO: Store user's results in database
    //echo print_r($_GET);

    //echo "</br></br>";

    $score = 0;
    $total_correct = 0;
    $total_incorrect = 0;
    $total_questions = 0;
    $quiznumber = -1;
    $useranswers = [];


    if(isset($_GET)){
        $quiznumber = $_GET['quizid'];

        //Build useranswers array from form array
        // $_GET = { question1 => 'Belarus' question2 => 'Jefferson'}
        // useranswers = { 1 => 'Belarus' 2 => 'Washington'}
        foreach ($_GET as $key => $elem){
            if(strlen($key) > 8 && substr($key, 0, 8) === 'question'){
                //$key[8] holds number after 'question' in key
                $useranswers[$key[8]] = $elem;
            }
        }
    } 

    //connect to database
    $dbconn = mysqli_connect("localhost", "USERNAME", "PASSWORD", "quizband");

    //check connection
    if(!$dbconn){
        echo "Error connecting to database: " . mysqli_connect_error();
    } else {
        $querysql = "SELECT question_num, choice_text FROM answer_choice 
                     WHERE quiz_id = $quiznumber AND correct = 1
                     ORDER BY question_num";
        $query_result = mysqli_query($dbconn, $querysql);
        $dbanswers = mysqli_fetch_all($query_result, MYSQLI_ASSOC);
        //echo print_r($dbanswers);
    }

    $total_questions = sizeof($dbanswers);

    foreach ($dbanswers as $question){
        $question_num = $question['question_num'];
        if($question['choice_text'] === $useranswers[$question_num]){
            $total_correct++;
        } else {
            $total_incorrect++;
        }
    }

    /*
    echo "Total questions: " . $total_questions . "</br></br>";
    echo "Total correct: " . $total_correct . "</br></br>";
    echo "Total incorrect: " . $total_incorrect . "</br></br>";
    */

    //Calculate score to 0 decimal places
    $score = floor($total_correct / $total_questions * 100);
    //echo "Score: " . $score;
?>

<!-- Output score and other relevant data -->

<!DOCTYPE html>
<html>
    <head>
        <title>Quizband</title>
        
        <!-- Bootstrap -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../styles.css">
        <link rel="stylesheet" type="text/css" href="resultstyles.css">
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
                <b>Quizband</b>
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <div class="score-box">
                        <div class="quiz-score-box">
                            <h5>General History</h5>
                            <h6 class="tag tag-history text-dark">History</h6>
                        </div>
                        <div class="score-score-box">
                            <h6>Your score:</h6>
                            <h1 id="score" class="text-dark"><?php echo $score?>%</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <div class="button-container text-center">
                        <button type="button" class="btn btn-light home-button">Back to home page</button>
                    </div>
                </div>
            </div>
        </div>
    </body>