<?php 

    // Calculate and output results of the quiz

    $score = 0;
    $quiz_title = "";
    $quiz_subject = "";

    if( empty($_POST['quizid']) || empty($_POST['quiz-title']) 
        || empty($_POST['quiz-subject']) ){
        echo "Error";
    } else {
        $quiz_id = $_POST['quizid'];
        $quiz_title = $_POST['quiz-title'];
        $quiz_subject = $_POST['quiz-subject'];
        $useranswers = [];

        // Build useranswers array from form array
        //   $_POST = { question1 => 'Belarus', question2 => 'Jefferson'}
        //   useranswers = { 1 => 'Belarus' 2 => 'Washington'}
        foreach ($_POST as $key => $elem){
            if(strlen($key) > 8 && substr($key, 0, 8) === 'question'){
                // $key[8] holds number after 'question' in key
                $useranswers[$key[8]] = $elem;
            }
        }

        // connect to database
        $dbconn = mysqli_connect("localhost", "guest", "guestpass123", "quizband");

        // check connection
        if($dbconn){
            //process score
            $dbanswers = getAnswersFromDatabase($dbconn, $quiz_id);
            $score = processScore($dbanswers, $useranswers);
        } else {
            echo "Error connecting to database: " . mysqli_connect_error();
        }

        mysqli_close($dbconn);
    } 

    

    // get correct answer choices corresponding to the quiz from the database
    //      in the form [questionnum] => "Correct choice text"
    function getAnswersFromDatabase($dbconn, $quiz_id){
        $querysql = "SELECT question_num, choice_text FROM answer_choice 
                     WHERE quiz_id = ? AND correct = 1
                     ORDER BY question_num";
        $stmt = mysqli_stmt_init($dbconn);
        if(mysqli_stmt_prepare($stmt, $querysql)){
            mysqli_stmt_bind_param($stmt, "i", $quiz_id);
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                return mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
    }

    

    // gets score in percentage 
    function processScore($dbanswers, $useranswers){
        $total_questions = sizeof($dbanswers);
        $total_correct = getNumCorrect($dbanswers, $useranswers);
    
        //Calculate score to 0 decimal places
        return floor($total_correct / $total_questions * 100);
    }



    // Helper function for processScore - 
    //      gets the amount of correct answers based on 
    //      user answers
    function getNumCorrect($dbanswers, $useranswers) {
        $total_correct = 0;

        //For each question, check if the correct answer text
        //is equal to the text submitted by the user
        foreach ($dbanswers as $question){
            $question_num = $question['question_num'];
            if($question['choice_text'] === $useranswers[$question_num]){
                $total_correct++;
            } 
        }

        return $total_correct;
    }
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
                            <h5><?php echo htmlspecialchars($quiz_title); ?></h5>
                            <h6 class="tag tag-<?php echo htmlspecialchars($quiz_subject); ?> text-dark"><?php echo htmlspecialchars($quiz_subject); ?></h6>
                        </div>
                        <div class="score-score-box">
                            <h6>Your score:</h6>
                            <h1 id="score" class="text-dark"><?php echo htmlspecialchars($score);?>%</h1>
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