let energyChart, autarkyChart;

$(document).ready(function() {

    // initialize Charts
    ctxEnergy = document.getElementById('energyChart').getContext('2d');
    energyChart = new Chart(
        ctxEnergy,
        configEnergy
    );

    ctxAutarky = document.getElementById('autarkyChart').getContext('2d');
    autarkyChart = new Chart(
        ctxAutarky,
        configAutarky
    );

    $('#switchToEnergyBarView').on('click', function(e) {
        $('#autarky-chart-container').hide();
        $('#table-container').hide();
        $('#energy-chart-container').show();
    });

    $('#switchToAutarkyBarView').on('click', function(e) {
        $('#energy-chart-container').hide();
        $('#table-container').hide();
        $('#autarky-chart-container').show();
    });

    //-------------------------------------------------------

    // Sort + Filter table    
    $('#energyTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "orderMulti": false,
        "autoWidth": false,
        "scrollX": false,
        "orderCellsTop": false,
        "pageLength": 10,
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
        const dataTable = $('#energyTable').DataTable();
        dataTable.columns.adjust();
    }, 200);
});