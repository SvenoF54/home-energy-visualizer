let energyChart, autarkyChart;

$(document).ready(function() {

    // initialize Charts
    ctxEnergy = document.getElementById('energyChart').getContext('2d');
    energyChart = new Chart(
        ctxEnergy,
        configEnergy
    );
    $('#energy-chart-container').data('chart', energyChart); // Add chart to container for later access

    ctxAutarky = document.getElementById('autarkyChart').getContext('2d');
    autarkyChart = new Chart(
        ctxAutarky,
        configAutarky
    );
    $('#autarky-chart-container').data('chart', autarkyChart); // Add chart to container for later access

    // Toggles the chart lines and strikethrough the legend
    // If 2 custom-form-fields are given, toggles between the 2 fields
    $('.chart-toggle-line').on('click', function() {
        var customFormFieldName = $(this).data('custom-form-field-field');
        var customFormFieldName2 = $(this).data('custom-form-field-field2');
        var chartId = $(this).data('chart-id');
        var chart = $('#' + chartId).data('chart');

        var dataset1;
        var dataset2;
        chart.data.datasets.forEach(function(dataset) {
            if (dataset.customFormFieldName === customFormFieldName) {
                dataset1 = dataset;
            } else if (dataset.customFormFieldName === customFormFieldName2) {
                dataset2 = dataset;
            }
        });

        if ((dataset1 !== undefined) && (dataset2 !== undefined)) {
            if (!dataset1.hidden && dataset2.hidden) {
                // Show both
                hideOrShowChartLine(chart, dataset1, false, dataset2, false);
                strikethroughLegendText($(this), false);
            } else if (!dataset1.hidden && !dataset2.hidden) {
                // Hide both                
                hideOrShowChartLine(chart, dataset1, true, dataset2, true);
                strikethroughLegendText($(this), true);
            } else if (dataset1.hidden && !dataset2.hidden) {
                // Show 2
                hideOrShowChartLine(chart, dataset1, true, dataset2, false);
                strikethroughLegendText($(this), false);
            } else {
                // Show 1
                hideOrShowChartLine(chart, dataset1, false, dataset2, true);
                strikethroughLegendText($(this), false);
            }
        } else if (dataset1 !== undefined) {
            hideOrShowChartLine(chart, dataset1, !dataset1.hidden);
            strikethroughLegendText($(this), dataset1.hidden);
        }

    });

    // Helper: Hides / shows the chart line and sets new status to the hidden form field
    function hideOrShowChartLine(chart, dataset1, hide1, dataset2 = undefined, hide2 = undefined) {
        // Hide or show chart line
        dataset1.hidden = hide1;
        if (dataset2 !== undefined) dataset2.hidden = hide2;
        chart.update();

        // Set hidden form field to recover chart settings on next page call
        $('#' + dataset1.customFormFieldName).val(!hide1);
        if (dataset2 !== undefined) $('#' + dataset2.customFormFieldName).val(!hide2);
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

    // Switches the energy- or autarky chart or the energy table on/off, also the buttons
    // Sets the state in the hidden form field
    $(".switch-chart").on("click", function() {
        const chartType = $(this).data("chart");
        switch (chartType) {
            case "EnergyChart":
                $('#autarky-chart-container').hide();
                $('#energy-table-container').hide();
                $('#energy-chart-container').show();

                $('#legendBtnEnergyChart').hide();
                $('#legendBtnEnergyTable').show();
                $('#legendBtnAutarkyChart').show();

                $('#autarky-chart-legend-buttons1').hide();
                $('#autarky-chart-legend-buttons2').hide();
                $('#energy-table-caption').hide();
                $('#energy-chart-legend-buttons1').show();
                $('#energy-chart-legend-buttons2').show();
                break;
            case "EnergyTable":
                $('#autarky-chart-container').hide();
                $('#energy-chart-container').hide();
                $('#energy-table-container').show();

                $('#legendBtnEnergyChart').show();
                $('#legendBtnEnergyTable').hide();
                $('#legendBtnAutarkyChart').show();

                $('#energy-chart-legend-buttons1').hide();
                $('#energy-chart-legend-buttons2').hide();
                $('#autarky-chart-legend-buttons1').hide();
                $('#autarky-chart-legend-buttons2').hide();
                $('#energy-table-caption').show();
                break;
            case "AutarkyChart":
                $('#energy-table-container').hide();
                $('#energy-chart-container').hide();
                $('#autarky-chart-container').show();

                $('#legendBtnEnergyChart').show();
                $('#legendBtnEnergyTable').show();
                $('#legendBtnAutarkyChart').hide();

                $('#energy-chart-legend-buttons1').hide();
                $('#energy-chart-legend-buttons2').hide();
                $('#energy-table-caption').hide();
                $('#autarky-chart-legend-buttons1').show();
                $('#autarky-chart-legend-buttons2').show();
                break;
        }
        $('#chartOrTableView').val(chartType);
    });

});
