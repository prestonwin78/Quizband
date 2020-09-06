let templateElement = document.getElementById("quiz-card-template");
let questionSection = document.querySelector(".question-section");

let totalQuestions = 0;

function addQuestion(event){
    event.preventDefault();
    ++totalQuestions;

    let card = {
        'question-num': totalQuestions
    };

    let templateText = templateElement.innerHTML;
    let rendered = Mustache.render(templateText, card);

    // doesn't work - resets all values in the dom
    /* questionSection.innerHTML = questionSection.innerHTML + rendered; */
   

    
}

document.getElementById("addquiz").addEventListener('click', addQuestion);
