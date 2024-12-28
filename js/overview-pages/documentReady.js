$(document).ready(function() {

    // initialize Chart
    ctx = document.getElementById('energyChart').getContext('2d');
    const myChart = new Chart(
        ctx,
        config
    );
});
