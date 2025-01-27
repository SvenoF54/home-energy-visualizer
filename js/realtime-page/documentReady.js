$(document).ready(function() {

    // Configure chart
    ctxRealtime = document.getElementById('realtimeChart').getContext('2d');
    realtimeChart = new Chart(
        ctxRealtime,
        configRealtime
    );
    $('#realtime-chart-container').data('chart', realtimeChart); // Add chart to container for later access

    // Toggles the chart lines and strikethrough the legend
    // If 2 custom-form-fields are given, toggles between the 2 fields
    $('.chart-toggle-line').on('click', function() {
        var customFormFieldName = $(this).data('custom-form-field-field');
        var chartId = $(this).data('chart-id');
        var chart = $('#' + chartId).data('chart');

        var dataset1;
        chart.data.datasets.forEach(function(dataset) {
            if (dataset.customFormFieldName === customFormFieldName) {
                dataset1 = dataset;
            }
        });

        if (dataset1 !== undefined) {
            hideOrShowChartLine(chart, dataset1, !dataset1.hidden);
            strikethroughLegendText($(this), dataset1.hidden);
        }

    });

    // Helper: Hides / shows the chart line and sets new status to the hidden form field
    function hideOrShowChartLine(chart, dataset1, hide1) {
        // Hide or show chart line
        dataset1.hidden = hide1;
        chart.update();

        // Set hidden form field to recover chart settings on next page call
        $('#' + dataset1.customFormFieldName).val(!hide1);
    }

    // Helper: Strikethrough the legend from the chart
    function strikethroughLegendText($buttonElement, hide) {
        // Set strikethrough outside foreach
        if (hide) {
            $buttonElement.find('.legend-label').addClass('strikethrough');
        } else {
            $buttonElement.find('.legend-label').removeClass('strikethrough');
        }
    }

    // --- Auto-Reload function ---

    // Set data range, needed for auto reload
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

    // Auto-Reload Checkbox
    $('#reloadCheckbox').on('change', checkAndReload);
    checkAndReload();

    // Function to check if the checkbox is enabled and control the reloading process
    var realtimeIntervalId;

    function checkAndReload() {
        var checkbox = document.getElementById('reloadCheckbox');

        if (checkbox.checked) {
            realtimeIntervalId = setInterval(function() {
                setDateRange($('#pastperiod').val());
                $('#date-range-form').submit();
            }, config.refreshIntervalInSec * 1000);
        } else {
            clearInterval(realtimeIntervalId);
        }
    }

});
