let templateElement = document.getElementById("quiz-card-template");
let questionSection = document.querySelector(".question-section");

let totalQuestions = 0;

function addQuestion(event){
    event.preventDefault();
    ++totalQuestions;

    // Holds values to be placed into template
    let card = {
        'question-num': totalQuestions
    };

    let templateText = templateElement.innerHTML;
    let rendered = Mustache.render(templateText, card);

    //Create new div element to append quizcard to document
    let newElem = document.createElement('div');
    newElem.classList.add("row");
    newElem.classList.add("main-section");
    newElem.innerHTML = rendered;

    questionSection.appendChild(newElem);
}

function redirectHome(event){
    event.preventDefault();
    window.location.href='../home.php';
}

document.getElementById("addquiz").addEventListener('click', addQuestion);
document.getElementById("cancel").addEventListener('click', redirectHome);