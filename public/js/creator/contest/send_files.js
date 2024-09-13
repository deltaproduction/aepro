function makeSendFilesRequest(formData) {
    $.ajax({
        url: '/send_files',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $(".after-sending_information").addClass("show");

            $("#papers_loaded").html(response.allContestMembersCount);
            $("#scans_loaded").html(response.allScansCount);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    let contestId = $.trim($('[name="contest_id"]').val());

    $(".send-files").click(function () {
        let formData = new FormData();

        formData.append("contest_id", contestId);

        let auditoriumsFiles = $('[name="auditoriums_files"]')[0].files;

        for (var i = 0; i < auditoriumsFiles.length; i++) {
            formData.append('files[]', auditoriumsFiles[i]);
        }

        console.log(formData);
        $(".after-sending_information").removeClass("show");

        makeSendFilesRequest(formData);
    });
});
