import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../../modal_window.js";

function makeRequest(formData) {
    $.ajax({
        url: '/new_task',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.redirect) {
                window.location.reload();
                window.location.href = response.redirect;
            } else {
                console.log(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    $("#newTask_form").on("submit", function(event) {
        event.preventDefault();

        let levelTitle = $.trim($('[name="task_max_rate"]').val());

        if (!isNaN(levelTitle) && levelTitle > 0) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new-task").click(function (event) {
        setModalWindowTitle("Новое задание");
        openModalWindow(event);
    });

    $(document).on('click', closeModalWindow);
});
