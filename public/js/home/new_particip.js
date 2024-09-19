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

function setCitiesList(region) {
    $.ajax({
        url: '/get_cities',
        type: 'GET',
        data: {
            region: region
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let cities = response.cities[0];
            let citiesMenu = $('[name="city"]');
            citiesMenu.empty();

            $.each(cities, function() {
                citiesMenu.append($("<option />").val(this["city_id"]).text(this["city"]));
            });

        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function setSchoolsList(city_id) {
    $.ajax({
        url: '/get_schools',
        type: 'GET',
        data: {
            city_id: city_id
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let schools = response.schools[0];
            let schoolsMenu = $('[name="school"]');
            schoolsMenu.empty();

            $.each(schools, function() {
                schoolsMenu.append($("<option />").val(this["s_id"]).text(this["short_title"]));
            });

        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    $('.js-select2').select2({
        placeholder: "Выберите из списка",
        maximumSelectionLength: 2,
        language: "ru"
    });

    $('[name="region"]').change(function() {
        setCitiesList($(this).val());
        $('[name="school"]').empty();
    });

    $('[name="city"]').change(function() {
        setSchoolsList($(this).val());
    });

    $('[name="school_absend"]').change(function() {
        if ($(this)[0].checked) {
            $(".form_field.region").hide();
            $(".form_field.place").hide();
            $(".form_field.school").hide();
            $(".form_field.school_manual").css("display", "flex");
        } else {
            $(".form_field.region").show();
            $(".form_field.place").show();
            $(".form_field.school").show();
            $(".form_field.school_manual").hide();
        }
    });

    $("#newParticip_form").on("submit", function(event) {
        event.preventDefault();

        let contestLevel = $.trim($('[name="contest_level"]').val());
        let contestPlace = $.trim($('[name="contest_place"]').val());
        let code = $.trim($('[name="code"]').val());

        if (!isNaN(contestLevel) && !isNaN(contestPlace)) {
            let formData = new FormData();

            formData.append("code", code);
            formData.append("contest_level", contestLevel);
            formData.append("contest_place", contestPlace);

            if ($('[name="school_absend"]')[0].checked) {
                let schoolName = $.trim($('[name="school_name"]').val());

                formData.append("school_name", schoolName);
            } else {
                let schoolId = $.trim($('[name="school"]').val());

                formData.append("school_id", schoolId);
            }

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
