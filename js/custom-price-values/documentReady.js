/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {

    // Edit-Button
    $('.edit-btn').on('click', function(e) {
        e.preventDefault();

        const row = $(this).closest('tr');

        $('#priceRowId').val(row.data('price-row-id'));
        $('#timestampFrom').val(row.data('timestampFrom'));
        $('#timestampTo').val(row.data('timestampTo'));
        $('#outCentPricePerKwh').val(formatNumber(row.data('out-cent-price-per-watt-hour') * 1000, 5).replace(",", "."));
        $('#inCentPricePerKwh').val(formatNumber(row.data('in-cent-price-per-watt-hour') * 1000, 5).replace(",", "."));

        $('#timestampFrom').focus();
    });


    // Delete-Button
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const priceRowId = row.data('price-row-id');
        const timestampFrom = row.data('timestamp-from-html');
        const timestampTo = row.data('timestamp-to-html');

        if (confirm(`Möchten Sie den Eintrag für den Zeitraum ${timestampFrom} von ${timestampTo} wirklich löschen?`)) {
            $('#performDelete').val(true);
            $('#priceRowId').val(priceRowId);
            $('#editform').submit();
        }
    });

    // Sort + Filter table
    $('#customEnergyValues').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "order": [
            [0, 'asc']
        ],
        "pageLength": 25,
        "columnDefs": [{
            "targets": 0,
            "orderDataType": "dom-text", // Sort for "data-sort"-Attribute
            "type": "string" // Sort as Strings 
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
});
