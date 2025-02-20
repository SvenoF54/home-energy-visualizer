/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {

    var circleNow = new ProgressBar.Circle('#circle-now', {
        color: '#007bff',
        strokeWidth: 10,
        trailColor: '#e0e0e0',
        duration: 500,
        easing: 'easeInOut',
        text: {
            value: '0%',
            className: 'progress-text'
        },
        step: function(state, bar) {
            bar.setText(Math.round(bar.value() * 100) + '%');
        }
    });

    var circleToday = new ProgressBar.Circle('#circle-today', {
        color: '#28a745',
        strokeWidth: 10,
        trailColor: '#e0e0e0',
        duration: 500,
        easing: 'easeInOut',
        text: {
            value: '0%',
            className: 'progress-text'
        },
        step: function(state, bar) {
            bar.setText(Math.round(bar.value() * 100) + '%');
        }
    });



    function fetchDashboardData() {
        $.ajax({
            url: URL_PREFIX + 'api/dashboard-reader.php',
            method: 'GET',
            dataType: 'json', // Falls die Antwort JSON ist
            success: function(response) {
                // Fill fields dynamicly
                $.each(response, function(category, values) {
                    $.each(values, function(key, value) {
                        let fieldId = `${category}-${key}`; // HTML id now-emOZ
                        let field = $("#" + fieldId);

                        if (field.length) {
                            let formated = formatJsValue(key, value);
                            field.text(formated);
                        }
                    });
                });

                // set progress circle + bar
                circleNow.animate(response.now.autInPct / 100);
                circleToday.animate(response.today.autInPct / 100);

                $("#em-now-bar").css("width", (response.now.emPercent) + "%");
                $("#pm-now-bar").css("width", (response.now.pmPercent) + "%");

            },


            error: function() {
                $('#dashboard-data').html('Fehler beim Laden der Daten');
            }
        });
    }

    // Initialer Aufruf
    fetchDashboardData();

    // Wiederhole den Abruf alle 1 Sekunden
    setInterval(fetchDashboardData, 2000);

    function formatJsValue(key, value) {
        if (key.toLowerCase().includes("price")) {
            return formatPrice(value);
        }

        return formatCurrent(value);
    }
});
