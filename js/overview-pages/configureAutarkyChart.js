const autarkyColor = getComputedStyle(document.documentElement).getPropertyValue('--autarky-color').trim();
const autarkyColor2 = getComputedStyle(document.documentElement).getPropertyValue('--autarky-color2').trim();
const emOverZeroPctColor = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color').trim();
const emOverZeroPctColor2 = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color2').trim();

const scalesAutarky = {
    x: {
        type: 'category',
        stacked: true,
        title: {
            display: true,
            text: 'Zeitpunkt'
        }
    },
    y: {
        beginAtZero: true,
        position: 'left',
        min: 0,
        max: 100,
        title: {
            display: true,
            text: ''
        },
        ticks: {
            callback: function(value, index, values) {
                return value + ' %'; // add % after numbers
            }
        }
    }
}

const optionsAutarky = {
    layout: {
        padding: {
            left: 20,
            right: 20,
            top: 20,
            bottom: 20
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            // Extends the tooltip with more information
            mode: 'x',
            callbacks: {
                label: function(tooltipItem) {
                    const percent = tooltipItem.parsed.y;
                    let label = formatNumber(percent, 2) + "%";

                    return label;
                }
            },
        },
        annotation: {
            annotations: {
                lineY50Pct: {
                    type: 'line',
                    yMin: 50,
                    yMax: 50,
                    borderColor: lineZeroColor,
                    borderWidth: 0.5,
                    borderDash: [10, 10],
                }
            }
        },
        legend: {
            display: true,
            onClick: function(event, legendItem) {
                const clickedDatasetIndex = legendItem.datasetIndex;
                const clickedLabel = legendItem.text;
                const dataset = autarkyChart.data.datasets[clickedDatasetIndex];

                // Update chart
                dataset.hidden = !dataset.hidden;
                autarkyChart.update();

                // Set hidden form field to recover chart settings on next page call
                const customFormFieldName = dataset.customFormFieldName;
                $('#' + customFormFieldName).val(!dataset.hidden);
            },
        },
    },
    scales: scalesAutarky
};

// Plugins, here the button for the table view
const pluginsAutarky = [{
    id: 'customButtonPlugin',
    afterDraw(chart) {
        const { ctx, chartArea } = chart;

        if (!chartArea) return;

        // Button 1: table-view
        const button1X = chartArea.right - 220;
        const button1Y = chartArea.top - 30;

        ctx.fillStyle = 'blue';
        ctx.fillRect(button1X, button1Y, 100, 25);

        ctx.fillStyle = 'white';
        ctx.font = '12px Arial';
        ctx.textBaseline = 'middle';
        ctx.fillText('Tabellenansicht', button1X + 10, button1Y + 12);

        // Button 2: energy-chart-view
        const button2X = chartArea.right - 110;
        const button2Y = chartArea.top - 30;

        ctx.fillStyle = 'blue';
        ctx.fillRect(button2X, button2Y, 100, 25);

        ctx.fillStyle = 'white';
        ctx.font = '12px Arial';
        ctx.textBaseline = 'middle';
        ctx.fillText('Energieansicht', button2X + 10, button2Y + 12);

        // Add event listeners if not already added
        if (!chart.customButtonListener) {
            chart.customButtonListener = true;

            chart.canvas.addEventListener('click', function(event) {
                const rect = chart.canvas.getBoundingClientRect();
                const x = event.clientX - rect.left;
                const y = event.clientY - rect.top;

                // Button 1: table-view
                if (x > button1X && x < button1X + 100 && y > button1Y && y < button1Y + 25) {
                    $('#energy-chart-container').hide();
                    $('#autarky-chart-container').hide();
                    $('#energy-table-container').show();
                    $('#chartOrTableOnFirstPageView').val('EnergyTable');
                }

                // Button 2: energy-chart-view
                if (x > button2X && x < button2X + 100 && y > button2Y && y < button2Y + 25) {
                    $('#autarky-chart-container').hide();
                    $('#energy-table-container').hide();
                    $('#energy-chart-container').show();
                    $('#chartOrTableOnFirstPageView').val('EnergyChart');
                }
            });
        }
    }
}];

const autarkyData1 = {
    label: '(1) Anteil selbst produzierter Strom',
    data: autarky1.map(item => item.autInPct),
    borderWidth: 3,
    borderColor: autarkyColor,
    backgroundColor: autarkyColor,
    fill: true,
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowAutarky',
    hidden: !config.energy1.chartShowAutarky
};

const autarky2Data = {
    label: '(2) Anteil selbst produzierter Strom',
    data: autarky2.map(item => item.autInPct),
    borderWidth: 3,
    borderColor: autarkyColor2,
    backgroundColor: autarkyColor2,
    fill: true,
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowAutarky',
    hidden: !config.energy2.chartShowAutarky
};
const autarkyDataset = [];
autarkyDataset.push(autarkyData1);
if (data2.length > 0) autarkyDataset.push(autarky2Data);

// configure diagram
const configAutarky = {
    type: 'line',
    data: {
        labels: timestampsXAxis,
        datasets: autarkyDataset
    },
    options: optionsAutarky,
    plugins: pluginsAutarky
};
