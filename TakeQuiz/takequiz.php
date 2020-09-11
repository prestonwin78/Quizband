<?php 
     
     $quiz_id = null;

     if(isset($_GET['quiz_id'])){
        $quiz_id = $_GET['quiz_id'];
     } else {
         echo "error";
     }
     
     $dbquestions = [];
     $questions = [];
     $choices = [];
     $quiz_title = "";
     $quiz_subject = "";
     
     $dbconn = mysqli_connect("localhost", "guest", "guestpass123", "quizband");
     
     //check connection
     if(!$dbconn){
         echo "Error connecting to database: " . mysqli_connect_error();
     } else {
         //Get questions
        $querysql = "SELECT question_num, text, answer_choice_num, choice_text
                     FROM question NATURAL JOIN answer_choice
                     WHERE quiz_id = ? ORDER BY question_num";
        $stmt = mysqli_stmt_init($dbconn);
        if(mysqli_stmt_prepare($stmt, $querysql)){
            mysqli_stmt_bind_param($stmt, "i", $quiz_id);
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
                $dbquestions = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_free_result($result);
            }
        }

        /*  Build answer choices array in form of
            choices[questionNum][answerChoiceNum] == choice text
            Build questions array in form of 
            questions[questionNum] = question text  */
        foreach ($dbquestions as $row){
            $choices[$row['question_num']][$row['answer_choice_num']] = $row['choice_text'];
            $questions[$row['question_num']] = $row['text'];
        }

        //Get title, subject of quiz
        $title_query = "SELECT title, subject from quiz
                        WHERE quiz_id = ?";
        $stmt = mysqli_stmt_init($dbconn);
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
     }

     mysqli_close($dbconn);
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
                <h1 id="create-quiz-text"><?php echo $quiz_title; ?></h1>
            </div>
        </div>
        
        <form action="results.php">
            <?php for($i = 1; $i <= sizeof($choices); $i++){ ?>
                <div class="row main-section">
                    <div class="col-3"></div>
                    <div class="col-6 quizcard bg-light">
                        <h2 class="question-text"><?php echo $questions[$i];?></h2>
                        <div class="answer-options">
                            <?php for($j = 1; $j <= sizeof($choices[$i]); $j++){ ?>
                                <div class="answer-choice">                                         
                                    <input type="radio" id="<?php echo "q" . $i . "a" . $j;?>" name="<?php echo "question" . $i;?>" value="<?php echo $choices[$i][$j];?>">
                                    <label for="<?php echo "q" . $i . "a" . $j;?>"><?php echo $choices[$i][$j]?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <div class="col-3"></div>
            </div>
            <?php } ?>
            
            <div class="row main-section">
                <div class="col-6"></div>
                <div class="col-6">
                    <button id="cancel" class="btn btn-light">Cancel</button>
                    <button id="submit" class="btn btn-primary shadow">Submit</button>
                </div>
            </div>
            <div id="blank-row" class="main-section"></div>
            <input type='hidden' id="quizid" name='quizid' value="<?php echo $quiz_id;?>">
            <input type='hidden' id="quizsub" name='quiz-subject' value="<?php echo $quiz_subject;?>">
            <input type='hidden' id="quiztitle" name='quiz-title' value="<?php echo $quiz_title;?>">
        </form>
    <body>