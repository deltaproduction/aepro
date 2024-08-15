import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../../modal_window.js";

function makeRequest(formData) {
    $.ajax({
        url: '/new_level',
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
    $("#newLevel_form").on("submit", function(event) {
        event.preventDefault();

        let levelTitle = $.trim($('[name="level_title"]').val());

        if (levelTitle) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new-level").click(function (event) {
        $(".mw-newPlace").hide();
        $(".mw-newLevel").show();
        $(".mw-newExpert").hide();
        setModalWindowTitle("Новый уровень");
        openModalWindow(event);
    });

    $(document).on('click', closeModalWindow);
});
