import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../../../modal_window.js";


function makeRequest(formData) {
    $.ajax({
        url: '/new_auditorium',
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
                window.location.href = response.redirect;
            }
            else if (response.reload) {
                window.location.reload();
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
    $("#newAuditorium_form").on("submit", function(event) {
        event.preventDefault();

        let auditoriumTitle = $.trim($('[name="auditorium_title"]').val());
        let auditoriumColumns = $.trim($('[name="auditorium_columns"]').val());
        let auditoriumRows = $.trim($('[name="auditorium_rows"]').val());

        if (auditoriumTitle && !isNaN(auditoriumColumns) && !isNaN(auditoriumRows)) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new-auditorium").click(function (event) {
        setModalWindowTitle("Новая аудитория");
        openModalWindow(event);
    });

    $(document).on('click', closeModalWindow);
});
