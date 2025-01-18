let energyChart, autarkyChart;

$(document).ready(function() {

    // initialize Charts
    ctxEnergy = document.getElementById('energyChart').getContext('2d');
    energyChart = new Chart(
        ctxEnergy,
        configEnergy
    );
    $('#energy-chart-container').data('chart', energyChart);

    ctxAutarky = document.getElementById('autarkyChart').getContext('2d');
    autarkyChart = new Chart(
        ctxAutarky,
        configAutarky
    );
    $('#autarky-chart-container').data('chart', autarkyChart);


    $('.chart-toggle-line').on('click', function() {
        var customFormFieldName = $(this).data('custom-form-field-fame');
        var customFormFieldName2 = $(this).data('custom-form-field-fame2');
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

    function hideOrShowChartLine(chart, dataset1, hide1, dataset2 = undefined, hide2 = undefined) {
        // Hide or show chart line
        dataset1.hidden = hide1;
        if (dataset2 !== undefined) dataset2.hidden = hide2;
        chart.update();

        // Set hidden form field to recover chart settings on next page call
        $('#' + dataset1.customFormFieldName).val(!hide1);
        if (dataset2 !== undefined) $('#' + dataset2.customFormFieldName).val(!hide2);
    }

    function strikethroughLegendText($buttonElement, hide) {
        // Set strikethrough outside foreach
        if (hide) {
            $buttonElement.find('.legend-label').addClass('strikethrough');
        } else {
            $buttonElement.find('.legend-label').removeClass('strikethrough');
        }
    }

    $(".switch-chart").on("click", function() {
        const chartType = $(this).data("chart");
        switch (chartType) {
            case "EnergyChart":
                $('#autarky-chart-container').hide();
                $('#energy-table-container').hide();
                $('#energy-chart-container').show();
                //$('#legendBtnEnergyChart').hide();
                break;
            case "EnergyTable":
                $('#autarky-chart-container').hide();
                $('#energy-chart-container').hide();
                $('#energy-table-container').show();
                break;
            case "AutarkyChart":
                $('#energy-table-container').hide();
                $('#energy-chart-container').hide();
                $('#autarky-chart-container').show();
                break;
        }
        $('#chartOrTableView').val(chartType);
    });

    //-------------------------------------------------------

    // add datatable (Sort + Filter table)
    var energyDataTable = $('#energyTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "orderMulti": false,
        "autoWidth": false,
        "scrollX": false,
        "orderCellsTop": false,
        "pageLength": config.energy1.tablePageLength,
        "columnDefs": [{
            "targets": '_all',
            "orderDataType": "dom-text",
            "type": "num",
            "render": function(data, type, row, meta) {
                return $(row).find('td').eq(meta.col).data('sort') || data;
            }
        }],
        language: {
            lengthMenu: "Zeige _MENU_ Einträge pro Seite",
            zeroRecords: "Keine Einträge gefunden",
            info: "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
            infoEmpty: "Keine Einträge verfügbar",
            infoFiltered: "(gefiltert von _MAX_ gesamten Einträgen)",
            search: "Suchen:",
        }
    });

    setTimeout(function() {
        energyDataTable.columns.adjust();
    }, 200);

    function toggleColumnVisibility(className, show) {
        energyDataTable.columns().every(function() {
            var column = this;
            if ($(column.header()).hasClass(className)) {
                column.visible(show);
            }
        });
    }

    $('#toggleProductionColumns').on('change', function() {
        toggleColumnVisibility('production-pm1', !this.checked);
        toggleColumnVisibility('production-pm2', !this.checked);
        toggleColumnVisibility('production-pm3', !this.checked);
        toggleColumnVisibility('production-pmtotal', this.checked);
        if ($('#tableEnergyShowProductionTotal').length) {
            $('#tableEnergyShowProductionTotal').val(this.checked ? "true" : "false");
        }
    });

    toggleColumnVisibility('production-pm1', !$('#toggleProductionColumns').prop('checked'));
    toggleColumnVisibility('production-pm2', !$('#toggleProductionColumns').prop('checked'));
    toggleColumnVisibility('production-pm3', !$('#toggleProductionColumns').prop('checked'));
    toggleColumnVisibility('production-pmtotal', $('#toggleProductionColumns').prop('checked'));

});
