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
            display: false,
        },
    },
    scales: scalesAutarky
};

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
    options: optionsAutarky
};
