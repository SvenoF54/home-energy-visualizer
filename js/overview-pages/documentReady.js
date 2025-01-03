$(document).ready(function() {

    // initialize Chart
    ctx = document.getElementById('energyChart').getContext('2d');
    const myChart = new Chart(
        ctx,
        config
    );

    $('#switchToBarView').on('click', function(e) {
        console.log("btn");
        $('#chart-container').css('display', 'block');
        $('#table-container').css('display', 'none');
    });

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
        // Datatable needs to be rezised for correct view problems
        $('#energyTable').DataTable().columns.adjust();
    }, 200);
});