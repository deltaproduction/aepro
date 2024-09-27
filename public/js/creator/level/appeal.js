function makeStartAppealsRequest(formData) {
    $.ajax({
        url: '/start_appeals',
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

function makeStopAppealsRequest(formData) {
    $.ajax({
        url: '/stop_appeals',
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
    let levelId = $.trim($('[name="level_id"]').val());

    let formData = new FormData();

    formData.append("level_id", levelId);

    $(".start-appeals").click(function () {
        makeStartAppealsRequest(formData);
    });

    $(".stop-appeals").click(function () {
        makeStopAppealsRequest(formData);
    });
});
