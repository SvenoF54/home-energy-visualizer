/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {
    // add datatable (Sort + Filter table)
    var energyDataTable = $('#energyTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "orderMulti": false,
        "autoWidth": false, // if true, the js for hidding production tables didn't work
        "scrollX": false,
        "orderCellsTop": false,
        "pageLength": config.energy1.tablePageLength || 10,
        "responsive": false,
        "columnDefs": [{
            "targets": '_all',
            "render": function(data, type, row, meta) {
                if (type === 'sort') {
                    // Hole den Sortierwert aus dem Attribut data-sort
                    return $(row).find('td').eq(meta.col).data('sort') || data;
                }
                return data;
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

    // Helper for toggle colum visibility (triggered by production checkbox or responsive js)
    function toggleEnergyTableColumnVisibility(className, show) {
        energyDataTable.columns().every(function() {
            var column = this;
            if ($(column.header()).hasClass(className)) {
                column.visible(show);
            }
        });
    }

    // checkbox to trigger production column visibility
    $('#toggleProductionColumns').on('change', function() {
        toggleEnergyTableColumnVisibility('production-pmtotal', this.checked);
        toggleEnergyTableColumnVisibility('production-pm3', !this.checked);
        toggleEnergyTableColumnVisibility('production-pm2', !this.checked);
        toggleEnergyTableColumnVisibility('production-pm1', !this.checked);
        if ($('#tableEnergyShowProductionTotal').length) {
            $('#tableEnergyShowProductionTotal').val(this.checked ? "true" : "false");
        }
    });

    // initial production column visibility
    toggleEnergyTableColumnVisibility('production-pm3', !$('#toggleProductionColumns').prop('checked'));
    toggleEnergyTableColumnVisibility('production-pm2', !$('#toggleProductionColumns').prop('checked'));
    toggleEnergyTableColumnVisibility('production-pm1', !$('#toggleProductionColumns').prop('checked'));
    toggleEnergyTableColumnVisibility('production-pmtotal', $('#toggleProductionColumns').prop('checked'));

    // responsive column visibility
    function adjustEnergyTableColumns() {
        var windowWidth = $(window).width();
        if (windowWidth <= 1200) {
            toggleEnergyTableColumnVisibility('production-pm3', false);
            toggleEnergyTableColumnVisibility('production-pm2', false);
            toggleEnergyTableColumnVisibility('production-pm1', false);
            $('#toggleProductionColumns').prop('checked', true);
            $('#toggleProductionColumns').hide();
            $('label[for="toggleProductionColumns"]').hide();
        }
        if (windowWidth >= 1200) {
            // initial production column visibility
            toggleEnergyTableColumnVisibility('production-pm3', !$('#toggleProductionColumns').prop('checked'));
            toggleEnergyTableColumnVisibility('production-pm2', !$('#toggleProductionColumns').prop('checked'));
            toggleEnergyTableColumnVisibility('production-pm1', !$('#toggleProductionColumns').prop('checked'));
            toggleEnergyTableColumnVisibility('production-pmtotal', $('#toggleProductionColumns').prop('checked'));
            $('#toggleProductionColumns').show();
            $('label[for="toggleProductionColumns"]').show();
        }
        if (windowWidth <= 990) {
            toggleEnergyTableColumnVisibility('production-pmtotal', false);
        }
        if (windowWidth >= 990 && !$('#toggleProductionColumns').prop('checked')) {
            toggleEnergyTableColumnVisibility('production-pmtotal', true);
        }
    }

    // initial column visibility
    //adjustEnergyTableColumns();

    // guard window resize
    $(window).resize(function() {
        //adjustEnergyTableColumns();
    });

});
