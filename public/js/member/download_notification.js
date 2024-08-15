function makeRequest(formData) {
    $.ajax({
        url: 'notification',
        type: 'POST',
        data: formData,
        xhrFields: {
            responseType: 'blob'
        },
        success: function(blob) {
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'document.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    $("#downloadNotification").on("submit", function(event) {
        event.preventDefault();

        let regNumber = $.trim($('[name="reg_number"]').val());

        if (!isNaN(regNumber) && regNumber.length === 9) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });
});
