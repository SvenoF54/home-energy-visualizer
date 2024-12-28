$(document).ready(function() {
    $('.selectorMonth1').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#month1').val($(this).attr('href').slice(1));
        $('#year1').val($('#selectorYear1').val());

        $('#date-range-form').submit();
    });

    $('.selectorMonth2').on('click', function(event) {
        event.preventDefault();

        // set hidden values
        $('#month2').val($(this).attr('href').slice(1));
        $('#year2').val($('#selectorYear2').val());

        $('#date-range-form').submit();
    });

});
