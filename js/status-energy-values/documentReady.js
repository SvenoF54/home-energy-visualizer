/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {
    $('.recalculate-btn').click(function() {
        var gapMonthYear = $(this).closest('tr').data('gap-month-year');

        var modal = new bootstrap.Modal($('#recalculateModal'));
        modal.show();

        $('#status-spinner').show();
        $('#status-msg1').text('Bitte warten, während die Daten neu berechnet werden.');
        $('#status-msg2').text('');
        $('#close-modal-btn').hide();

        $.ajax({
            url: `${URL_PREFIX}api/unify-data.php`,
            type: 'POST',
            data: {
                apikey: API_KEY,
                monthYear: gapMonthYear,
            },
            success: function(data) {
                $('#status-spinner').hide();
                $('#status-msg1').text('Daten erfolgreich neu berechnet.');
                $('#status-msg2').text('Antwort: ' + data);
                $('#close-modal-btn').show(); // Zeige den Schließen-Button an
                $('#close-modal-btn').click(function() {
                    location.reload(); // Seite neu laden
                });

            },
            error: function(xhr, status, error) {
                $('#status-spinner').hide();
                $('#status-msg').text('Es ist ein Fehler aufgetreten: ' + error);
                $('#close-modal-btn').show(); // Zeige den Schließen-Button an
            }
        });
    });
});