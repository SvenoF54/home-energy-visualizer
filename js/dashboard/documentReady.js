/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {

    // Prepare Cirlce for now
    var autarkyColorRGB = autarkyColor.replace(/rgba?\(([^,]+),([^,]+),([^,]+),?.*\)/, 'rgb($1,$2,$3)'); // Extracts RGB-Part from RGBA
    var circleNow = prepareCircle('#circle-now', autarkyColorRGB);

    // Prepare Cirlce for today
    var autarkyColorRGB2 = autarkyColor2.replace(/rgba?\(([^,]+),([^,]+),([^,]+),?.*\)/, 'rgb($1,$2,$3)'); // Extracts RGB-Part from RGBA
    var circleToday = prepareCircle('#circle-today', autarkyColorRGB2);

    // Prepare Cirlce for yesterday
    var autarkyColorRGB2 = autarkyColor2.replace(/rgba?\(([^,]+),([^,]+),([^,]+),?.*\)/, 'rgb($1,$2,$3)'); // Extracts RGB-Part from RGBA
    var circleYesterday = prepareCircle('#circle-yesterday', autarkyColorRGB2);
    circleYesterday.animate(staticData.yesterday.autInPct / 100);

    function fetchDashboardData() {
        $.ajax({
            url: URL_PREFIX + 'api/get-dashboard-data.php',
            method: 'GET',
            dataType: 'json', // Falls die Antwort JSON ist
            success: function(response) {
                // Fill fields dynamicly
                $.each(response, function(category, values) {
                    $.each(values, function(key, value) {
                        let fieldId = `${category}-${key}`; // HTML id now-emOZ
                        let field = $("#" + fieldId);

                        if (field.length) {
                            let formated = formatJsValue(category, key, value);
                            field.text(formated);
                        }
                    });
                });

                // set progress circle + bar
                circleNow.animate(response.now.autInPct / 100);
                circleToday.animate(response.today.autInPct / 100);

                // Bar 1 (Hausdaten)
                $("#em-now-bar").css("width", (response.now.emPercent) + "%");
                $("#em-now-bar").css("background", response.now.em > 0 ? emOverZeroColor : feedInColor);
                $('#now-em-caption').html(response.now.em > 0 ? "Einkauf" : "Einspeisung");

                // Bar 2 (Produktionsdaten)
                $("#pm-now-bar").css("width", (response.now.productionPercent) + "%");
                $('#zeroFeedInActive').toggle(response.now.isZeroFeedInActive); // Set zero feed in active msg

                // Bar 3 (Zendure Batteriedaten)
                $("#pm-pack-bar").css("width", (response.zendure.electricLevelPercent) + "%");
                $("#pm-pack-bar").css("background-color", "var(--" + getAkkuColor(response.zendure.electricLevelPercent) + ")");
                $('#zendure-chargeActive').toggle(response.zendure.isZendureChargeActive);
                $('#zendure-dischargeActive').toggle(response.zendure.isZendureDischargeActive);

            },

            error: function() {
                $('#dashboard-data').html('Fehler beim Laden der Daten');
            }
        });
    }

    // Initial call + intervall
    fetchDashboardData();
    setInterval(fetchDashboardData, 2000);

    // Format helper
    function formatJsValue(category, key, value) {
        if (key.toLowerCase().includes("price")) {
            return formatPrice(value);
        } else if (category.toLowerCase().includes("now")) {
            return formatCurrent(value);
        } else if (key.toLowerCase().includes("percent")) {
            return formatNumber(value, 0) + "%";
        }

        return formatCurrent(value, "h");
    }

    // Prepare circle
    function prepareCircle(id, color) {
        return new ProgressBar.Circle(id, {
            color: color,
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
    }

    function getAkkuColor(stateInPercent) {
        if (stateInPercent < 15) return "akku-red-color";
        if (stateInPercent >= 15 && stateInPercent < 25) return "akku-orange-color";
        if (stateInPercent >= 25 && stateInPercent < 85) return "akku-green-color";
        if (stateInPercent >= 85 && stateInPercent < 100) return "akku-greenfull-color";
    }
});
