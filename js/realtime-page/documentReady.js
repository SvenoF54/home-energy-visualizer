$(document).ready(function() {

    // Configure chart
    const myEnergyChart = new Chart(
        $('#energyChart')[0].getContext('2d'),
        configEnergy
    );

    $('#reloadCheckbox').on('change', checkAndReload);
    checkAndReload();

    function setDateRange(hours) {
        const now = new Date();
        var timestamp = now.getTime() - now.getTimezoneOffset() * 60000;
        const toDate = new Date(timestamp).toISOString().slice(0, 16);
        $('#to-date').val(toDate);

        now.setHours(now.getHours() - hours); // setze 'Von' auf den Zeitraum zurück
        var timestamp = now.getTime() - now.getTimezoneOffset() * 60000;
        const fromDate = new Date(timestamp).toISOString().slice(0, 16);
        $('#from-date').val(fromDate);
    }

    // Function to check if the checkbox is enabled and control the reloading process
    var intervalId;

    function checkAndReload() {
        var checkbox = document.getElementById('reloadCheckbox');

        if (checkbox.checked) {
            intervalId = setInterval(function() {
                setDateRange($('#hours').val());
                $('#date-range-form').submit();
            }, refreshIntervalInSec * 1000);
        } else {
            clearInterval(intervalId);
        }
    }

});