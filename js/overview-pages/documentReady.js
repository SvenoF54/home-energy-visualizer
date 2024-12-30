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
        "autoWidth": false,
        "scrollX": false,
        "order": [
            [0, 'asc']
        ],
        "pageLength": 10,
        "columnDefs": [{
            "targets": 0,
            "orderDataType": "dom-text",
            "type": "string",
        }, ]
    });

    setTimeout(function() {
        // Datatable needs to be rezised for correct view problems
        table.columns.adjust();
    }, 200);
});