<?php 
     
    $dbquestions = [];
    $questions = [];
    $choices = [];
    $quiz_id = -1;       //passed to results page
    $quiz_title = "";
    $quiz_subject = "";

    if(!empty($_GET['quiz_id'])){
        $quiz_id = $_GET['quiz_id'];

        // Connect to database
        $dbconn = mysqli_connect("localhost", "guest", "guestpass123", "quizband");
     
        // check connection
        if($dbconn){
            $quiz_arr = getQuestionsAndAnswers($dbconn, $quiz_id);
            $questions = $quiz_arr['questions'];    // an array of questions
            $choices = $quiz_arr['answer_choices']; // an array of answer choices

            $temp = getTitleAndSubject($dbconn, $quiz_id);
            $quiz_title = $temp['title'];           // the quiz title string 
            $quiz_subject = $temp['subject'];       // the quiz subject string
            
        } else {
            echo "Error connecting to database: " . mysqli_connect_error();
        }   

        mysqli_close($dbconn); // close connection
    } else {
        echo "error";
    }

     


    // Returns two arrays:
    //     first contains quiz questions
    //     second contains answer choices
    function getQuestionsAndAnswers($dbconn, $quiz_id){
        $querysql = "SELECT question_num, text, answer_choice_num, choice_text
                     FROM question NATURAL JOIN answer_choice
                     WHERE quiz_id = ? ORDER BY question_num";
        $stmt = mysqli_stmt_init($dbconn);
        $dbquestions = null;
        if(mysqli_stmt_prepare($stmt, $querysql)){
            mysqli_stmt_bind_param($stmt, "i", $quiz_id);
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                $dbquestions = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_free_result($result);
                return buildQuizArray($dbquestions);
            } else {
                echo "Error executing query";
            }
        } else {
            echo "Error preparing statement";
        }
    }



     /*  Build answer choices array in form of
            choices[questionNum][answerChoiceNum] === choice text
            Build questions array in form of 
            questions[questionNum] === question text  */
     function buildQuizArray($dbquestions){
        $questions = [];
        $answer_choices = [];
        foreach ($dbquestions as $row){
            $answer_choices[$row['question_num']][$row['answer_choice_num']] = $row['choice_text'];
            $questions[$row['question_num']] = $row['text'];
        }
        return array(
            'questions' => $questions, 
            'answer_choices' => $answer_choices
        );
     }



     // Returns an array containing the quiz title and subject
     function getTitleAndSubject($dbconn, $quiz_id){
        // Get title, subject of quiz
        $title_query = "SELECT title, subject from quiz
                        WHERE quiz_id = ?";
        $stmt = mysqli_stmt_init($dbconn);

        $quiz_title = "";
        $quiz_subject = "";
        if(mysqli_stmt_prepare($stmt, $title_query)){
            mysqli_stmt_bind_param($stmt, "i", $quiz_id);
            if(mysqli_stmt_execute($stmt)){
                $title_result = mysqli_stmt_get_result($stmt);
                $result_arr = mysqli_fetch_assoc($title_result);
                $quiz_title = $result_arr['title'];
                $quiz_subject = $result_arr['subject'];
                mysqli_free_result($title_result);
            }
        }
        return array(
            'title' => $quiz_title,
            'subject' => $quiz_subject
        );
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
            <a id="title" class="text-dark" href="../home.php">Quizband</a>
                <a>Contact</a>
                <a>About</a>
                <a>Sign In</a>
            </div>
        </div>
        <div class="row main-section">
            <div class="col-12 text-center">
                <h1 id="create-quiz-text"><?php echo htmlspecialchars($quiz_title); ?></h1>
            </div>
        </div>
        
        <form action="results.php" method="post">

            <!-- Dynamically add a card for each question -->

            <?php for($i = 1; $i <= sizeof($choices); $i++){ ?>
                <div class="row main-section">
                    <div class="col-3"></div>
                    <div class="col-6 quizcard bg-light">
                        <h2 class="question-text"><?php echo htmlspecialchars($questions[$i]);?></h2>
                        <div class="answer-options">

                            <!-- build answer choices -->

                            <?php for($j = 1; $j <= sizeof($choices[$i]); $j++){ ?>
                                <div class="answer-choice">                                         
                                    <input type="radio" id="<?php echo "q" . $i . "a" . $j;?>" name="<?php echo "question" . $i;?>" value="<?php echo htmlspecialchars($choices[$i][$j]);?>">
                                    <label for="<?php echo "q" . $i . "a" . $j;?>"><?php echo htmlspecialchars($choices[$i][$j]);?></label>
                                </div>
                            <?php } ?>

                            <!-- end answer choices -->

                        </div>
                    </div>
                <div class="col-3"></div>
            </div>
            <?php } ?>

            <!-- end card section -->
            
            <div class="row main-section">
                <div class="col-6"></div>
                <div class="col-6">
                    <button id="cancel" class="btn btn-light" onclick="event.preventDefault(); window.location.href = '../home.php';">Cancel</button>
                    <button id="submit" class="btn btn-primary shadow">Submit</button>
                </div>
            </div>
            <div id="blank-row" class="main-section"></div>
            <input type='hidden' id="quizid" name='quizid' value="<?php echo $quiz_id;?>">
            <input type='hidden' id="quizsub" name='quiz-subject' value="<?php echo $quiz_subject;?>">
            <input type='hidden' id="quiztitle" name='quiz-title' value="<?php echo $quiz_title;?>">
        </form>
    <body>