<?php 
    include("./config.php");

    session_start();
    $signed_in = false;
    $user_id = -1;
    if(!empty($_SESSION['user_id'])){
        $signed_in = true;
        $user_id = $_SESSION['user_id'];
    } else {
        $signed_in = false;
    }

    $dbconn = mysqli_connect(HOST, DBUSERNAME, DBPASSWORD, DBNAME);
    $quizzes = [];
    $user_quizzes = [];
    if(!$dbconn){
        echo "error connecting";
    } else {
        // Get array of quiz data to output
        $quizzes = getQuizData($dbconn);
        if($signed_in){
            $user_quizzes = getUserQuizzes($dbconn, $user_id);
        }
        
        mysqli_close($dbconn);  //close connection
    }


    // Gets data from 3 random public quizzes
    //  in form [row_num] => [quiz_id, description, name, ...]
    function getQuizData($dbconn){
        /*$query = "SELECT * FROM quiz
                  WHERE quiz_id=?";*/

        $query = "SELECT * FROM quiz
                  WHERE visibility='public'
                  ORDER BY RAND()
                  LIMIT 3";
        $result = mysqli_query($dbconn, $query);
        if(!$result){
            return null;
        } else {
            $quiz_data = [];
            $index = 0;
            while($row = mysqli_fetch_assoc($result)){
                $quiz_data[$index++] = $row;
            }
            return $quiz_data;
        }
    }

    // Gets all quizzes by the user in the database
    //  in form [row_num] => [quiz_id, description, name, ...]
    function getUserQuizzes($dbconn, $user_id){
        $sql = "SELECT * FROM quiz
                WHERE creator=?";
        $stmt = mysqli_stmt_init($dbconn);
        if(mysqli_stmt_prepare($stmt, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $arr;
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
                <a href="login.php"  style=<?= $signed_in ? "display:none" : "display:block" ?>>Sign In</a>
                <a href="signup.php" style=<?= $signed_in ? "display:none" : "display:block" ?>>Sign Up</a>
                <a href="signout.php" style=<?= $signed_in ? "display:block" : "display:none" ?>>Sign Out</a>
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
                        <div id="id-container">
                            <form action="./TakeQuiz/takequiz.php">
                                <input type="text" id="quiz-by-id" name="quiz_id" placeholder="By ID"></input>
                            </form>
                        </div>
                    </div>

                    <?php foreach($quizzes as $row_num => $q_data){ ?>

                        <div class="row">
                            <div class="card quiz-card">
                                <div class="card-body">
                                    <a class="card-title text-dark" href="./TakeQuiz/takequiz.php?quiz_id=<?php echo htmlspecialchars($q_data['quiz_id']);?>"><?= htmlspecialchars($q_data['title']) ?></a>
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

                    <div class="row">
                        <h2 class="text-light">My Quizzes</h2>
                    </div>

                    <?php foreach($user_quizzes as $row_num => $q_data){ ?>

                        <div class="row">
                            <div class="card quiz-card">
                                <div class="card-body">
                                    <a class="card-title text-dark" href="./TakeQuiz/takequiz.php?quiz_id=<?php echo htmlspecialchars($q_data['quiz_id']);?>"><?= htmlspecialchars($q_data['title']) ?></a>
                                    <p class="card-text"><?= htmlspecialchars($q_data['description']) ?></p>
                                    <p class="card-text bold"><strong>ID: <?= htmlspecialchars($q_data['quiz_id']) ?> </strong></p>
                                    <h6 class="tag tag-<?php echo htmlspecialchars($q_data['subject']); ?>"><?= htmlspecialchars($q_data['subject']) ?></h6>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </body>