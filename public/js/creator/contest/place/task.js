let activeTaskPrototype = 0;

let taskEditor = ace.edit('task-editor');
let answerEditor = ace.edit('answer-editor');

let contestId = $.trim($('[name="contest_id"]').val());
let levelId = $.trim($('[name="level_id"]').val());
let taskId = $.trim($('[name="task_id"]').val());


function taskSidebarItemClickHandler(object) {
    let tpId = $(object).attr("tp-id");

    getPrototypeDataRequest(tpId, object);
}

function newPrototype(tpId, title) {
    let createdItem = $(".tasks_sidebar-items").append(
        $(`<div class="tasks_sidebar-item">
            ${title}
        </div>`).attr("tp-id", tpId).click(function () {
            taskSidebarItemClickHandler(this)
        })
    );

    taskEditor.setValue("", 1);
    answerEditor.setValue("", 1);

    setActiveItem($(".tasks_sidebar-item").last());
}

function makeNewPrototypeRequest(formData) {
    $.ajax({
        url: '/new_prototype',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let prototypeNumber = response.prototype_number;
            let taskPrototypeId = response.tp_id;

            newPrototype(taskPrototypeId, prototypeNumber);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function setActiveItem(object) {
    $(".tasks_sidebar-item").removeClass("active");
    $(object).addClass("active");
    activeTaskPrototype = $(object).attr("tp-id");
}

function getPrototypeDataRequest(tpId, object) {
    $.ajax({
        url: '/prototype_data',
        type: 'GET',
        data: {
            contest_id: contestId,
            level_id: levelId,
            task_id: taskId,
            tp_id: tpId
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let taskPrototype = response.task_prototype;
            let data = taskPrototype[0];

            if (data) {
                let taskText = data.task_text == null ? "" : data.task_text;
                let taskAnswer = data.task_answer == null ? "" : data.task_answer;

                taskEditor.setValue(taskText, 1);
                answerEditor.setValue(taskAnswer, 1);

                setActiveItem(object);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function setPrototypeDataRequest(formData) {
    formData.append("tp_id", activeTaskPrototype);

    $.ajax({
        url: '/set_tp_data',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {

        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    let firstItem = $('.tasks_sidebar-item').first();
    getPrototypeDataRequest(firstItem.attr("tp-id"), firstItem);

    taskEditor.setTheme('ace/theme/tomorrow');
    answerEditor.setTheme('ace/theme/tomorrow');

    taskEditor.session.setMode('ace/mode/latex');
    answerEditor.session.setMode('ace/mode/latex');

    taskEditor.setOptions({
        fontSize: '16px'
    });

    answerEditor.setOptions({
        fontSize: '16px'
    });

    $(".tasks_sidebar-header__new").click(function () {
        let formData = new FormData();

        formData.append("contest_id", contestId);
        formData.append("level_id", levelId);
        formData.append("task_id", taskId);

        makeNewPrototypeRequest(formData);
    });

    $(".tasks_sidebar-item").click(function () {
        taskSidebarItemClickHandler(this);
    });

    $("#saveData").on("submit", function (event) {
        event.preventDefault();

        let taskText = taskEditor.getValue();
        let taskAnswer = answerEditor.getValue();

        let formData = new FormData();

        formData.append("contest_id", contestId);
        formData.append("level_id", levelId);
        formData.append("task_id", taskId);

        formData.append("task_text", taskText);
        formData.append("task_answer", taskAnswer);

        setPrototypeDataRequest(formData);
    });
});
