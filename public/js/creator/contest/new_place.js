import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../../modal_window.js";

function makeRequest(formData) {
    $.ajax({
        url: '/new_place',
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
    $("#newPlace_form").on("submit", function(event) {
        event.preventDefault();

        let placeTitle = $.trim($('[name="place_title"]').val());
        let placeLocality = $.trim($('[name="place_locality"]').val());
        let placeAddress = $.trim($('[name="place_address"]').val());

        if (placeTitle && placeLocality) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new-place").click(function (event) {
        $(".mw-newPlace").show();
        $(".mw-newLevel").hide();
        $(".mw-newExpert").hide();
        setModalWindowTitle("Новая площадка");
        openModalWindow(event);
    });


    $(document).on('click', closeModalWindow);
});
