let editor = ace.edit('latex-editor');

document.addEventListener('DOMContentLoaded', function () {
    editor.setTheme('ace/theme/tomorrow');
    editor.session.setMode('ace/mode/latex');

    editor.setOptions({
    fontSize: '16px'
    });
});

function makeRequest(formData) {
    $.ajax({
        url: '/set_pattern',
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
    $(".submit-button.save-pattern").on("click", function(event) {
        let levelPattern = editor.getValue();
        let contestId = $.trim($('[name="contest_id"]').val());
        let levelId = $.trim($('[name="level_id"]').val());

        if (levelPattern) {
            let formData = new FormData();
            formData.append("level_pattern", levelPattern);
            formData.append("contest_id", contestId);
            formData.append("level_id", levelId);

            makeRequest(formData);
        }
    });
});
