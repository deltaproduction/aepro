function makeStartApplyRequest(formData) {
    $.ajax({
        url: '/start_apply',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            window.location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function makeStopApplyRequest(formData) {
    $.ajax({
        url: '/stop_apply',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            window.location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function makeEndApplyRequest(formData) {
    $.ajax({
        url: '/end_apply',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            window.location.reload();
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function makeEndTourRequest(formData) {
    $.ajax({
        url: '/end_tour',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            window.location.reload();
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    let contestId = $.trim($('[name="contest_id"]').val());

    let formData = new FormData();

    formData.append("contest_id", contestId);

    $(".start-apply").click(function () {
        makeStartApplyRequest(formData);
    });

    $(".stop-apply").click(function () {
        makeStopApplyRequest(formData);
    });

    $(".end-apply").click(function () {
        makeEndApplyRequest(formData);
    });

    $(".end-tour").click(function () {
        makeEndTourRequest(formData);
    });
});

