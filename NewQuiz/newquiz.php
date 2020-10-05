<?php
  session_start();
  //If user is signed out, redirect to signin page
  if(empty($_SESSION['user_id'])){
    header("Location: ../signup.php");
    exit();
  }
?>

<!DOCTYPE html>

<html>

<head>
    <title>Quizband</title>

    <!-- Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <link rel="stylesheet" type="text/css" href="../TakeQuiz/takequizstyles.css">
    <link rel="stylesheet" type="text/css" href="newquizstyles.css">

    <!-- Mustache.js for templating -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/4.0.1/mustache.min.js"></script>
    <script defer src="func.js"></script>
</head>

<body>
    <div id="main-content" class="container-fluid">
        <div id="header" class="row bg-light">
            <a id="title" class="text-dark" href="../home.php">Quizband</a>
            <a href="../index.html">About</a>
            <a href="../login.php">Sign In</a>
            <a href="../signup.php">Sign Up</a>
        </div>
    </div>
    <form action="./processnewquiz.php" method="post">
        <div class="row main-section">
            <div class="col-12 text-center">
                <input type="text" id="quiz-title" class="create-quiz-text text-light" value="Quiz Title Here"
                    name="quiz-title">
            </div>
        </div>

        <div class="row main-section">
            <div class="col-12 text-center">
                <input type="text" id="quiz-description" class="create-quiz-text text-light" placeholder="Description"
                    name="quiz-description">
            </div>
        </div>

        <div class="row main-section">
            <div class="col-12 text-center">
                <input type="text" id="quiz-subject" class="create-quiz-text text-light" placeholder="Subject"
                    name="quiz-subject">
            </div>
        </div>

        <div class="question-section">

            <!-- Template for new quiz cards to be added dynamically -->

            <script id="quiz-card-template" type="x-tmpl-mustache">
                        <div class="col-3"></div>
                        <div class="col-6 quizcard bg-light">
                            <textarea class="question" name="question{{question-num}}" cols="40"></textarea>
                            <div class="answer-options">
                                <div class="answer-choice">
                                    <div class="row">
                                        <div class="col-1">
                                            <input type="radio" id="q{{question-num}}a1}" value="q{{question-num}}a1" name="question{{question-num}}answer">
                                        </div>
                                        <div class="col-11">
                                            <input type="text" name="q{{question-num}}a1text">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-1">
                                            <input type="radio" id="q{{question-num}}a2" value="q{{question-num}}a2" name="question{{question-num}}answer">
                                        </div>
                                        <div class="col-11">
                                            <input type="text" name="q{{question-num}}a2text">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-1">
                                            <input class="b" type="radio" id="q{{question-num}}a3" value="q{{question-num}}a3" name="question{{question-num}}answer">
                                        </div>
                                        <div class="col-11">
                                            <input type="text" name="q{{question-num}}a3text">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-1">
                                            <input type="radio" id="q{{question-num}}a4" value="q{{question-num}}a4" name="question{{question-num}}answer">
                                        </div>
                                        <div class="col-11">
                                            <input type="text" name="q{{question-num}}a4text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3"></div>
                </script>

            <!-- end quiz card template -->

        </div>

        <div class="row main-section">
            <div class="col-12 text-center">
                <button id="addquiz" class="btn btn-light">+</button>
            </div>
        </div>

        <div class="row main-section">
            <div class="col-6"></div>
            <div class="col-6">
                <button id="cancel" class="btn btn-light" type="button">Cancel</button>
                <button id="submit" class="btn btn-primary shadow" type="submit">Save</button>
            </div>
        </div>
        <div id="blank-row" class="main-section"></div>
    </form>

    <body>
</html>