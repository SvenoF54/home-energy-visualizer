$(document).ready(function() {

    // initialize Charts
    ctxEnergy = document.getElementById('energyChart').getContext('2d');
    energyChart = new Chart(
        ctxEnergy,
        configEnergy
    );

});
