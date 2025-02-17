/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

$(document).ready(function() {

    // initialize Charts
    ctxEnergy = document.getElementById('energyChart').getContext('2d');
    energyChart = new Chart(
        ctxEnergy,
        configEnergy
    );

});
