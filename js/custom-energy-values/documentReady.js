$(document).ready(function() {
    $("#editform").on("submit", function(event) {
        const producedPowerInput = $("#producedPower").val();
        const phase1Checked = $("#phase1").is(":checked");
        const phase2Checked = $("#phase2").is(":checked");
        const phase3Checked = $("#phase3").is(":checked");

        if (producedPowerInput > 0 && !(phase1Checked || phase2Checked || phase3Checked)) {
            event.preventDefault();
            alert("Bitte wählen Sie mindestens eine Phase aus, wenn eine Stromproduktion eingegeben wurde.");
        }
    });

    // Radio Button Event Listener
    $('input[name="timestampType"]').on('change', function() {
        if ($(this).val() === 'month') {
            $('#timestamp').attr('type', 'month');
        } else {
            $('#timestamp').attr('type', 'date');
        }
    });

    // Edit-Button
    $('.edit-btn').on('click', function(e) {
        e.preventDefault();

        const row = $(this).closest('tr');

        $timestampType = row.data('timestamp-type');
        if ($timestampType === 'month') {
            $('#timestamp').attr('type', 'month');
            $('#monthType').prop('checked', true);
        } else {
            $('#timestamp').attr('type', 'date');
            $('#dayType').prop('checked', true);
        }

        $('#timestamp').val(row.data('timestamp'));
        $('#consumption').val(formatNumber(row.data('em-total-power') / 1000, 5).replace(",", "."));
        $('#feedIn').val(formatNumber(row.data('em-under-zero') / -1000, 5).replace(",", "."));
        let genSum = parseFloat(row.data('pm1-total-power')) + parseFloat(row.data('pm2-total-power')) + parseFloat(row.data('pm3-total-power'));
        $('#producedPower').val(formatNumber(genSum / 1000, 5).replace(",", "."));
        $('#outCentPricePerKwh').val(formatNumber(row.data('out-cent-price-per-watt-hour') * 1000, 5).replace(",", "."));
        $('#inCentPricePerKwh').val(formatNumber(row.data('in-cent-price-per-watt-hour') * 1000, 5).replace(",", "."));

        $('#phase1').prop('checked', row.data('pm1-total-power') > 0);
        $('#phase2').prop('checked', row.data('pm2-total-power') > 0);
        $('#phase3').prop('checked', row.data('pm3-total-power') > 0);

        $('#timestamp').focus();
    });


    // Delete-Button
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const timestampRaw = row.data('timestamp-raw');
        const timestampType = row.data('timestamp-type');
        let timestampDate = parseDbDate(timestampRaw);
        const timestampAsString = timestampType == "month" ? (timestampDate.getMonth() + 1) + "." + timestampDate.getFullYear() : timestampDate.getDate() + "." + timestampDate.getMonth() + "." + timestampDate.getFullYear();
        if (confirm(`Möchten Sie den Eintrag vom ${timestampAsString} wirklich löschen?`)) {
            $('#performDelete').val(true);
            $('#timestampDelete').val(timestampRaw);
            $('#editform').submit();
        }
    });

    // Sort + Filter table
    let dataTable = new DataTable('#energyTable');
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