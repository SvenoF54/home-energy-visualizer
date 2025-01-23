$(document).ready(function() {
    // add datatable (Sort + Filter table)
    var energyDataTable = $('#energyTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "orderMulti": false,
        //"autoWidth": true, // needed for responsive
        "scrollX": false,
        "orderCellsTop": true,
        "pageLength": config.energy1.tablePageLength || 10,
        "responsive": true,
        "columnDefs": [{
            "targets": '_all',
            "orderDataType": "dom-text",
            "type": "num",
            "render": function(data, type, row, meta) {
                var sortData = $(row).find('td').eq(meta.col).data('sort');
                return sortData !== undefined ? sortData : data;
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
