function newQuizRedirect() {
    window.location.href = "./NewQuiz/newquiz.php";
}

let plus = document.getElementById("new-quiz-plus");
plus.addEventListener("click", newQuizRedirect);