let modalOpened = false;

export function setModalWindowTitle(title) {
    $(".modal-window__header_title").html(title);
}

export function openModalWindow(event) {
    $(".modal-window_back").css("display", "flex");

    $(".modal-window_wrapper").animate({
        marginBottom: "50px"
    }, 100);

    $(".modal-window_back").animate({
        opacity: "1",
    }, 100);

    $(".layout-header__info_menu_wrapper").hide();
    modalOpened = true;
}

export function closeModalWindow(event) {
    if (!$(event.target).closest('.modal-window_wrapper, .new_contest_item, .new-place, .new-level, .new-auditorium, .new-task, .new-expert_link, .select2-results__options, .select2-search').length && modalOpened) {
        $(".modal-window_wrapper").animate({
            marginBottom: "20px"
        }, 100);

        $(".modal-window_back").animate({
            opacity: "0",
        }, 100);

        setTimeout(function () {
            $('.modal-window_back').hide();
            modalOpened = false;
        }, 100);
    }
}
