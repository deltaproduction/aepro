import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../modal_window.js";

function makeRequest(formData) {
    $.ajax({
        url: '/new_contest',
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
    $("#newContest_form").on("submit", function(event) {
        event.preventDefault();

        let contestTitle = $.trim($('[name="contest_title"]').val());

        if (contestTitle) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new_contest_item").click(function (event) {
        $(".mw-newContest").show();
        $(".mw-newParticip").hide();
        setModalWindowTitle("Новое испытание");
        openModalWindow(event);
    });

    $(document).on('click', closeModalWindow);
});
