<?php 
    include("./config.php");

    session_start();
    $signed_in = false;
    if(!empty($_SESSION['user_id'])){
        $signed_in = true;
    } else {
        $signed_in = false;
    }


    // Get array of quiz data to output
    $quizzes = getArray();

    // returns array of quizzes ready for output
    function getArray(){
        $dbconn = mysqli_connect(HOST, DBUSERNAME, DBPASSWORD, DBNAME);
        if(!$dbconn){
            echo "error connecting";
        } else {
            /* Get 3 random quiz ids of quizzes to display on page */
            $arr = getQuizIds($dbconn);

            //get array holding info about each quiz
            $quiz_data = getQuizData($dbconn, $arr);

            mysqli_close($dbconn);  //close connection

            return $quiz_data;
        }
    }

    /* returns an array of 3 unique quiz IDs */
    function getQuizIds($dbconn){
        $quiz_arr = [];
        $total_quizzes = getTotalQuizzes($dbconn);
        for($i = 0; $i < 3; $i++){
            $rand = random_int(1, $total_quizzes);
            // keep getting random numbers until rand is unique
            while(in_array($rand, $quiz_arr)){
                // get random number from 1 to the total amount
                $rand = random_int(1, $total_quizzes);
            }
            $quiz_arr[$i] = $rand;  //put random value in quiz array
        }
        return $quiz_arr;
    }

    /* returns total number of quizzes in the database */
    function getTotalQuizzes($dbconn){
        $query = "SELECT COUNT(*) FROM quiz";
        $result = mysqli_query($dbconn, $query);
        if(!$result){   
            echo "Error";
            return 0;
        } else {
            return mysqli_fetch_row($result)[0];
        }
    }

    // Returns array of quiz data
    //  in form [quiz-id] => [description, name, ...]
    function getQuizData($dbconn, $quizIDs){
        $query = "SELECT * FROM quiz
                  WHERE quiz_id=?";
        // Use prepared statement to get data from database
        $stmt = mysqli_stmt_init($dbconn);
        if(!mysqli_stmt_prepare($stmt, $query)){
            echo "error";
        } else {
            $q_id = null; 
            $quiz_data = [];    
            mysqli_stmt_bind_param($stmt, "i", $q_id);

            // Build quiz_data array for each quiz ID in 
            // the quiz ID array
            foreach($quizIDs as $q_id){
                if(!mysqli_stmt_execute($stmt)){
                    echo "Error";
                } else {
                    $result = mysqli_stmt_get_result($stmt);
                    $quiz_data[$q_id] = mysqli_fetch_row($result);
                }
            }

            reindex($quiz_data);

            return $quiz_data;
        }
    }

    /* Reindex array to use names as indexes instead of numbers */
    function reindex(&$quiz_data){
        foreach($quiz_data as $q_id => $row){
            foreach($row as $key => $value){
                unset($quiz_data[$q_id][$key]); // unset previous value
                // set new value
                switch($key){
                    case 0:
                        $quiz_data[$q_id]['quiz_id'] = $value;
                        break;
                    case 1:
                        $quiz_data[$q_id]['title'] = $value;
                        break;
                    case 2:
                        $quiz_data[$q_id]['description'] = $value;
                        break;
                    case 3:
                        $quiz_data[$q_id]['subject'] = $value;
                        break;
                    case 4:
                        $quiz_data[$q_id]['creator'] = $value;
                        break;
                }
            }
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
        <script src="./home.js" defer></script>
    </head>
    <body>
        <div id="main-content" class="container-fluid">
            <div id="header" class="row bg-light">
                <a id="title" class="text-dark" href="home.php">Quizband</a>
                <a href="index.html">About</a>
                <a href="login.php" style=<?= $signed_in ? "display:none" : "display:block" ?>>Sign In</a>
                <a href="signup.php" style=<?= $signed_in ? "display:none" : "display:block" ?>>Sign Up</a>
            </div>
            <div class="row main-section">
                <h1>Hello.</h1>
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
                        <div id="id-container" class="col">
                            <form action="./TakeQuiz/takequiz.php">
                                <input type="text" id="quiz-by-id" name="quiz_id" placeholder="By ID"></input>
                            </form>
                        </div>
                    </div>

                    <?php foreach($quizzes as $q_id => $q_data){ ?>

                        <div class="row">
                            <div class="card quiz-card">
                                <div class="card-body">
                                    <a class="card-title text-dark" href="./TakeQuiz/takequiz.php?quiz_id=<?php echo htmlspecialchars($q_id);?>"><?= htmlspecialchars($q_data['title']) ?></a>
                                    <p class="card-text"><?= htmlspecialchars($q_data['description']) ?></p>
                                    <h6 class="tag tag-<?php echo htmlspecialchars($q_data['subject']); ?>"><?= htmlspecialchars($q_data['subject']) ?></h6>
                                </div>
                            </div>
                        </div>

                    <?php } ?>

                </div>
                <div class="col-6 col-lg-4">
                    <div class="row">
                        <h2 class="text-light">My Profile</h2>
                    </div>
                    <div class="row">
                        <div class="card quiz-card">
                            <div class="card-body">
                                <h5 style=<?= $signed_in ? "display:block" : "display:none" ?>>New Quiz</h5>
                                <p style=<?= $signed_in ? "display:none" : "display:block" ?> class="askSignin">
                                    Sign in to create a new quiz.
                                </p>
                                <div id="new-quiz-plus" class="plus-container" style=<?= $signed_in ? "display:block" : "display:none" ?>>
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