let templateElement = document.getElementById("quiz-card-template");
let questionSection = document.querySelector(".question-section");  // holds question cards

let totalQuestions = 0; //holds current number of questions on the page

function addQuestion(event){
    event.preventDefault(); // don't submit
    ++totalQuestions;

    // Holds values to be placed into template
    let card = {
        'question-num': totalQuestions
    };

    // Render template
    let templateText = templateElement.innerHTML;
    let rendered = Mustache.render(templateText, card);

    //Create new div element to append quizcard to document
    let newElem = document.createElement('div');
    newElem.classList.add("row");
    newElem.classList.add("main-section");

    //set new element's inner html to the rendered text
    newElem.innerHTML = rendered;

    //add new element to dom
    questionSection.appendChild(newElem);
}

// Redirect home when user clicks cancel button
function redirectHome(event){
    event.preventDefault(); // don't submit form
    window.location.href='../home.php';
}

document.getElementById("addquiz").addEventListener('click', addQuestion);
document.getElementById("cancel").addEventListener('click', redirectHome);