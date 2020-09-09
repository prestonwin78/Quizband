function newQuizRedirect(){
    window.location.href = "./NewQuiz/newquiz.html";
}

let plus = document.getElementById("new-quiz-plus");
plus.addEventListener("click", newQuizRedirect);