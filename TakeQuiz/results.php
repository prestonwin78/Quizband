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
    $dbanswers = [];
    $useranswers = [];

    $quiz_title = $_GET['quiz-title'];
    $quiz_subject = $_GET['quiz-subject'];

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
    $dbconn = mysqli_connect("localhost", "testuser1", "pass123", "quizband");

    //check connection
    if(!$dbconn){
        echo "Error connecting to database: " . mysqli_connect_error();
    } else {
        $querysql = "SELECT question_num, choice_text FROM answer_choice 
                     WHERE quiz_id = ? AND correct = 1
                     ORDER BY question_num";
        $stmt = mysqli_stmt_init($dbconn);
        if(mysqli_stmt_prepare($stmt, $querysql)){
            mysqli_stmt_bind_param($stmt, "i", $quiznumber);
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                $dbanswers = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
    }

    mysqli_close($dbconn);

    $total_questions = sizeof($dbanswers);

    foreach ($dbanswers as $question){
        $question_num = $question['question_num'];
        if($question['choice_text'] === $useranswers[$question_num]){
            $total_correct++;
        } else {
            $total_incorrect++;
        }
    }

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
            <a id="title" class="text-dark" href="../home.php">Quizband</a>
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <div class="score-box">
                        <div class="quiz-score-box">
                            <h5><?php echo $quiz_title; ?></h5>
                            <h6 class="tag tag-<?php echo $quiz_subject; ?> text-dark"><?php echo $quiz_subject; ?></h6>
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
                        <button onclick="window.location.href='../home.php'" type="button" 
                            class="btn btn-light home-button">Back to home page</button>
                    </div>
                </div>
            </div>
        </div>
    </body>