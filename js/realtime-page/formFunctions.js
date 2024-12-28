$(document).ready(function() {
    $('#to-date').datetimepicker({
        format: 'Y-m-d H:i', // Show without seconds
        formatTime: 'H:i', // Show without seconds
        onChangeDateTime: (currentDateTime, $input) => $('#hours').val(0)
    });
    $('#from-date').datetimepicker({
        format: 'Y-m-d H:i', // Show without seconds
        formatTime: 'H:i', // Show without seconds
        onChangeDateTime: (currentDateTime, $input) => $('#hours').val(0)
    });

    // When the user makes a selection, populate the date fields with the last x hours
    $('#hours').change(function() {
        const selectedRange = $(this).val();
        setDateRange(selectedRange);
    });

    // Check form before send
    $('#date-range-form').submit(function(e) {
        const fromDate = $('#from-date').val();
        const toDate = $('#to-date').val();

        if (fromDate && toDate) {
            // Verify if the "From" date is after the "To" date
            if (new Date(fromDate) > new Date(toDate)) {
                alert("Das 'Von'-Datum darf nicht später als das 'Bis'-Datum sein.");
                e.preventDefault(); // Verhindert das Absenden
            }
        } else {
            alert("Bitte beide Daten auswählen.");
            e.preventDefault();
        }
    });
});
