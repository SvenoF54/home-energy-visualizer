/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

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
            right: 15
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            // Extends the tooltip with more information
            mode: 'x',
            callbacks: {
                title: function(tooltipItems) {
                    return "Autarkiewerte";
                },
                label: function(tooltipItem) {
                    const customDataSourceNo = tooltipItem.dataset.customDataSourceNo;

                    const date = timestampsTooltip[tooltipItem.dataIndex][customDataSourceNo - 1];
                    const percent = tooltipItem.parsed.y;
                    let label = formatNumber(percent, 2).padStart(6, "_");
                    label += "% | " + tooltipItem.dataset.label + " " + date;

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
                },
                lineMinusY50Pct: {
                    type: 'line',
                    yMin: -50,
                    yMax: -50,
                    borderColor: lineZeroColor,
                    borderWidth: 0.5,
                    borderDash: [10, 10],
                }
            }
        },
        legend: {
            display: false
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
    customFormFieldName: 'energy1_chartShowAutarkyRate',
    hidden: !config.energy1.chartShowAutarkyRate
};

const selfConsumptionData1 = {
    label: '(1) Eigenverbrauchsquote',
    data: autarky1.map(item => item.slfConInPct),
    borderWidth: 3,
    borderColor: selfConsumptionColor,
    backgroundColor: selfConsumptionColor,
    fill: true,
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowSelfConsumptionRate',
    hidden: !config.energy1.chartShowSelfConsumptionRate
};


const autarky2Data = {
    label: '(2) Anteil selbst produzierter Strom',
    data: autarky2.map(item => item.autInPct),
    borderWidth: 3,
    borderColor: autarkyColor2,
    backgroundColor: autarkyColor2,
    fill: true,
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowAutarkyRate',
    hidden: !config.energy2.chartShowAutarkyRate
};

const selfConsumptionData2 = {
    label: '(2) Eigenverbrauchsquote',
    data: autarky2.map(item => item.slfConInPct),
    borderWidth: 3,
    borderColor: selfConsumptionColor2,
    backgroundColor: selfConsumptionColor2,
    fill: true,
    maxBarThickness: 30,
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowSelfConsumptionRate',
    hidden: !config.energy2.chartShowSelfConsumptionRate
};


const autarkyDataset = [];
autarkyDataset.push(autarkyData1);
autarkyDataset.push(selfConsumptionData1);
if (data2.length > 0) {
    autarkyDataset.push(autarky2Data);
    autarkyDataset.push(selfConsumptionData2);
}

// configure diagram
const configAutarky = {
    type: 'line',
    data: {
        labels: timestampsXAxis,
        datasets: autarkyDataset
    },
    options: optionsAutarky
};
