$(function () {
    $(".layout-header__info_down-icon").click(function(event) {
        $(".layout-header__info_menu_wrapper").toggle();
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.layout-header__info_down-icon, .layout-header__info_menu_wrapper').length) {
            $('.layout-header__info_menu_wrapper').hide();
        }
    });
});
