jQuery(document).ready(function ($) {

    var form = $('#export-filters'),
        review = $('.eutcsv_leave_review'),
        users = $('#users-filters'),
        notusers = $('#export-filters input:not(.user-export)');

    review.hide();
    users.hide();

    $('input[value="Download Export File"]').on("click", function () {
        $(review).delay(2000).slideDown(500);
    });

    $('form#export-filters input[type=radio]').change(function () {
        if ($('input.user-export').is(':checked')) {
            users.slideDown();
        }
        if ($(notusers).is(':checked')) {
            users.slideUp();
        }
    });

});