function setRatingsList(contest_id, level_id, place_id, considered) {
    $.ajax({
        url: '/get_results',
        type: 'GET',
        data: {
            level_id: level_id,
            place_id: place_id,
            contest_id: contest_id
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            let results = response["results"][0];

            if (results.length) {
                $(".not-found_message").css("display", "none");
                $(".rating_table tbody").empty();
                $(".rating_table").css("display", "table");

                $.each(results, function (index, contestMember) {
                    let school = contestMember["school_name"] ? contestMember["school_name"] : contestMember["short_title"];
                    let grade = contestMember["grades_sum_final_score"] ? contestMember["grades_sum_final_score"] : 0;

                    let text = `<tr>
                        <td>${index + 1}</td>
                        <td>${contestMember["last_name"]} ${contestMember["first_name"]} ${contestMember["middle_name"]}</td>
                        <td>${school}</td>
                        <td>${contestMember["title"]}</td>
                        <td>${grade}</td>
                    </tr>`;

                    $(".rating_table tbody").append(text);
                });
            } else {
                $(".rating_table").css("display", "none");
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
        setRatingsList(contestId, $('#level').val(), $('#place').val());
    });

    $('#place').change(function() {
        setRatingsList(contestId, $('#level').val(), $('#place').val());
    });
});
