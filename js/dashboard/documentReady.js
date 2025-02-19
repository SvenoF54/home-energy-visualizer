/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {

    function fetchDashboardData() {
        console.log("fetch");
        $.ajax({
            url: URL_PREFIX + 'api/dashboard-reader.php',
            method: 'GET',
            dataType: 'json', // Falls die Antwort JSON ist
            success: function(response) {
                //console.log(JSON.stringify(response));
                $('#em_total').html(response.em_total);
                $('#em_over_zero').html(response.em_over_zero);
                $('#em_under_zero').html(response.em_under_zero);
                $('#pm_total').html(response.pm_total);
                $('#pm1_total').html(response.pm1_total);
                $('#pm2_total').html(response.pm2_total);
                $('#pm3_total').html(response.pm3_total);


                $('#dashboard-data').html(response); // Passe die Ausgabe an
            },
            error: function() {
                $('#dashboard-data').html('Fehler beim Laden der Daten');
            }
        });
    }

    // Initialer Aufruf
    fetchDashboardData();

    // Wiederhole den Abruf alle 1 Sekunden
    setInterval(fetchDashboardData, 1000);
});
