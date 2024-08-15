import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../modal_window.js";


function makeGetContestRequest(formData) {
    $.ajax({
        url: '/get_contest',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let contestCode = response.contest_code;
            let levels = response.levels;
            let places = response.places;

            $("#newParticip_form .contest-code").html(contestCode);

            let contestLevel = $('[name="contest_level"]');
            let contestPlace = $('[name="contest_place"]');

            contestLevel.empty();
            contestPlace.empty();

            $.each(levels, function() {
                contestLevel.append($("<option />").val(this.id).text(this.title));
            });

            $.each(places, function() {
                contestPlace.append($("<option />").val(this.id).text(this.title));
            });

            $('[name="code"]').val(contestCode);

            $(".mw-newContest").hide();
            $(".mw-newParticip").show();

            setModalWindowTitle("Регистрация в испытании");
            openModalWindow();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function makeNewParticipRequest(formData) {
    $.ajax({
        url: '/new_particip',
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
    $("#newParticip_form").on("submit", function(event) {
        event.preventDefault();

        let contestLevel = $.trim($('[name="contest_level"]').val());
        let contestPlace = $.trim($('[name="contest_place"]').val());

        if (!isNaN(contestLevel) && !isNaN(contestPlace)) {
            let formData = new FormData(this);

            makeNewParticipRequest(formData);
        }
    });

    $("#getContest_form").on("submit", function(event) {
        event.preventDefault();

        let contestCode = $.trim($('[name="contest_code"]').val());

        if (contestCode && !isNaN(contestCode) && contestCode.length === 7) {
            let formData = new FormData(this);

            makeGetContestRequest(formData);
        }
    });
});
