import {
    closeModalWindow,
    openModalWindow,
    setModalWindowTitle
} from "../../modal_window.js";


function makeRequest(formData) {
    $.ajax({
        url: '/new_expert',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let email = response.email;
            let level = response.level;
            let name = response.name;

            event = document.createEvent("HTMLEvents");
            event.initEvent("dataavailable", true, true);
            event.eventName = "dataavailable";

            let count = $(".experts_table tbody tr").length;

            $(".experts_table tbody").append(`
                <tr>
                    <td>${count + 1}</td>
                    <td>${name}</td>
                    <td>${email}</td>
                    <td>${level}</td>
                    <td>X</td>
                </tr>
            `);

            closeModalWindow(event);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}


$(function () {
    $("#newExpert_form").on("submit", function(event) {
        event.preventDefault();

        let expertName = $.trim($('[name="expert_name"]').val());
        let expertEmail = $.trim($('[name="expert_email"]').val());
        let expertLevel = $.trim($('[name="expert_level"]').val());

        if (!isNaN(expertLevel) && expertName && expertEmail) {
            let formData = new FormData(this);

            makeRequest(formData);
        }
    });

    $(".new-expert_link").click(function (event) {
        $(".mw-newPlace").hide();
        $(".mw-newExpert").show();
        $(".mw-newLevel").hide();
        setModalWindowTitle("Новый эксперт");
        openModalWindow(event);
    });

    $(document).on('click', closeModalWindow);
});
