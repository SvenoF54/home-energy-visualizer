$(document).ready(function() {
    $('.selectorYear1').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#year1').val($(this).attr('href').slice(1));
        $('#date-range-form').submit();
    });

    $('.selectorYear2').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#year2').val($(this).attr('href').slice(1));
        $('#date-range-form').submit();
    });

});
