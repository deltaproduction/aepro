function setAppealsList(contest_id, level_id, place_id, considered) {
    $.ajax({
        url: '/get_appeals',
        type: 'GET',
        data: {
            level_id: level_id,
            place_id: place_id,
            contest_id: contest_id,
            considered: considered
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let appeals = response["appeals"][0];

            if (appeals.length) {
                $(".not-found_message").css("display", "none");
                $(".appeals_table tbody").empty();
                $(".appeals_table").css("display", "table");

                $.each(appeals, function (index, appeal) {
                    let text = `<tr>
                        <td>${index + 1}</td>

                        <td>
                            <a href="${contest_id}/appeal/${appeal["id"]}">
                                ${appeal["last_name"]} ${appeal["first_name"]} ${appeal["middle_name"]}
                            </a>
                        </td>
                        <td>${appeal["place"]}</td>
                        <td>${appeal["level"]}</td>
                        <td>X</td>
                    </tr>`;
                    $(".appeals_table tbody").append(text);
                });
            } else {
                $(".appeals_table").css("display", "none");
                $(".not-found_message").css("display", "block");
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$(function () {
    let contestId = $('[name="contest_id"]').val();

    $('#level').change(function() {
        setAppealsList(contestId, $('#level').val(), $('#place').val(), $('#considered').val());
    });

    $('#place').change(function() {
        setAppealsList(contestId, $('#level').val(), $('#place').val(), $('#considered').val());
    });

    $('#considered').change(function() {
        setAppealsList(contestId, $('#level').val(), $('#place').val(), $('#considered').val());
    });
});
