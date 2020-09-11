<?php 
    $questions = [];
    $answer_choices = [];
    $quiz_title = "";
    $quiz_description = "No Description";
    $quiz_subject = "No Subject";
    $answers = [];
    $quiz_id = null;
    $user_id = 1;

    if(isset($_GET['quiz-title'])){

        $quiz_title = $_GET['quiz-title'];
        $quiz_description = $_GET['quiz-description'];
        $quiz_subject = $_GET['quiz-subject'];

        foreach($_GET as $key => $value) {
            if(preg_match('/question[1-9]answer/', $key)){
                $answers[$key[8]] = $value[3];
            } else if(preg_match('/question[1-9]/', $key)){
                $questions[$key[8]] = $value;
            } else if(preg_match('/q[1-9]a[1-9]text/', $key)){
                $answer_choices[$key[1]][$key[3]] = $value;
            }
        }

        echo "Title: " . $quiz_title . "</br></br>";
        echo "Questions: </br>";
        echo print_r($questions);
        echo "</br></br>";
        echo "Answer_choices: </br>";
        echo print_r($answer_choices);
        echo "</br></br>";
        echo "Answers: </br>";
        echo print_r($answers);



        /* If query is valid, connect to database and insert quiz data */
        $dbconn = mysqli_connect("localhost", "testuser1", "pass123", "quizband");



        /*  quiz: (quiz_id, title, description, subject, creator) */
        $insert_quiz_query = "INSERT INTO quiz (title, description, subject, creator)
                              VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($dbconn);

        if(!mysqli_stmt_prepare($stmt, $insert_quiz_query)){
            echo "error";
        } else {
            mysqli_stmt_bind_param($stmt, "ssss", 
                        $quiz_title, $quiz_description, $quiz_subject, $user_id);
            echo "</br></br>";
            echo "quiz_title: $quiz_title, $quiz_description, $quiz_subject, $user_id </br>";
            mysqli_stmt_execute($stmt);
            //get the last AUTO INCREMENT value from the database
            $quiz_id = mysqli_insert_id($dbconn);  
        }

        /*  question: (question_num, quiz_id, text) */
        $insert_question_query = "INSERT INTO question
                                  VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($dbconn);

        if(!mysqli_stmt_prepare($stmt, $insert_question_query)){
            echo "error";
        } else {
            $q_num = 0;
            $q_text = "";

            mysqli_stmt_bind_param($stmt, "sss", $q_num, $quiz_id, $q_text);
            echo "</br></br>";
            foreach($questions as $q_num => $q_text){
                echo "q_num: $q_num, quiz_id: $quiz_id, q_text: $q_text </br>";
                mysqli_stmt_execute($stmt);
            }
        }

        /*  answer_choice: (question_num, quiz_id, 
            answer_choice_num, choice_text, correct )*/
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
            echo "</br></br>";
            foreach($answer_choices as $q_num => $c_arr){
                foreach($c_arr as $c_num => $c_text){
                    $correct = 0;   //False
                    if($c_num == $answers[$q_num]){
                        $correct = 1;   //True
                    }
                    
                    echo "q_num: $q_num, quiz_id: $quiz_id, c_num: $c_num, c_text: $c_text, correct: $correct </br>";
                    mysqli_stmt_execute($stmt);
                }
            }
        }

        mysqli_close($dbconn);
    }
?>