<?php 
     $quiz_id = 3;  //TODO: this needs to be set beforehand
     $dbquestions = [];
     $questions = [];
     $choices = [];
     $quiz_title = "";
     
     $dbconn = mysqli_connect("localhost", "USERNAME", "PASSWORD", "quizband");
     
     //check connection
     if(!$dbconn){
         echo "Error connecting to database: " . mysqli_connect_error();
     } else {
         //Get questions
        $querysql = "SELECT question_num, text, answer_choice_num, choice_text
                     FROM question NATURAL JOIN answer_choice
                     WHERE quiz_id = $quiz_id ORDER BY question_num";
        $query_result = mysqli_query($dbconn, $querysql);
        $dbquestions = mysqli_fetch_all($query_result, MYSQLI_ASSOC);
        mysqli_free_result($query_result);

        /*  Build answer choices array in form of
            choices[questionNum][answerChoiceNum] == choice text
            Build questions array in form of 
            questions[questionNum] = question text  */
        foreach ($dbquestions as $row){
            $choices[$row['question_num']][$row['answer_choice_num']] = $row['choice_text'];
            $questions[$row['question_num']] = $row['text'];
        }

        //Get title of quiz
        $title_query = "SELECT title from quiz
                        WHERE quiz_id = $quiz_id";
        $title_result = mysqli_query($dbconn, $title_query);
        $quiz_title = mysqli_fetch_assoc($title_result)['title'];
        mysqli_free_result($title_result);
        
        mysqli_close($dbconn);
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
        </form>
    <body>