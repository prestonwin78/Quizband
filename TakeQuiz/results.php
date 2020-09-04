<?php 
    //Get quiz info from database
    //Compare results with form data
    //Calculate score
    //Store user's results in database
    echo "Hello World</br>";

    //connect to database
    $dbconn = mysqli_connect("localhost", "USERNAME", "PASSWORD", "quizband");

    //check connection
    if(!$dbconn){
        echo "Error connecting to database: " . mysqli_connect_error();
    } else {
        echo "Connection successful";
    }

?>


<!-- Output score and other relevant data -->