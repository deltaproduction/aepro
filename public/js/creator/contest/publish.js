function makePublishResultsRequest(formData) {
    $.ajax({
        url: '/publish_results',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
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

    $(".publish-results").click(function () {
        let formData = new FormData();

        formData.append("contest_id", contestId);

        makePublishResultsRequest(formData);
    });
});

