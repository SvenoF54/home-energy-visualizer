$(document).ready(function() {
    $('#line1').change(function() {
        $('#date-range-form').submit();
    });

    $('#line2').change(function() {
        $('#date-range-form').submit();
    });

    $('#day1').datetimepicker({
        format: 'd.m.Y',
        timepicker: false, // no time, only date
        onChangeDateTime: (currentDateTime, $input) => $('#date-range-form').submit()
    });

    $('#subtract-day1').on('click', function(e) {
        e.preventDefault();
        updateDate($('#day1'), -1);
    });

    $('#add-day1').on('click', function(e) {
        e.preventDefault();
        updateDate($('#day1'), 1);
    });


    $('#day2').datetimepicker({
        format: 'd.m.Y',
        timepicker: false, // no time, only date
        onChangeDateTime: (currentDateTime, $input) => $('#date-range-form').submit()
    });

    $('#subtract-day2').on('click', function(e) {
        e.preventDefault();
        updateDate($('#day2'), -1);
    });

    $('#add-day2').on('click', function(e) {
        e.preventDefault();
        updateDate($('#day2'), 1);
    });

    function updateDate(dayField, days) {
        let currentDate = parseDate(dayField.val());

        currentDate.setDate(currentDate.getDate() + days);
        const newDate = formatDate(currentDate);
        dayField.val(newDate);

        $('#date-range-form').submit();
    }

});
