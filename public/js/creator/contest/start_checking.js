function makeStartCheckingRequest(formData) {
    $.ajax({
        url: '/start_checking',
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


$(function () {
    let contestId = $.trim($('[name="contest_id"]').val());

    let formData = new FormData();

    formData.append("contest_id", contestId);

    $(".start-checking").click(function () {
        makeStartCheckingRequest(formData);
    });
});