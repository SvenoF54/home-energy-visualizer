/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

const scalesRealtime = {
    x: {
        type: 'time',
        time: {
            unit: timeLabelUnit, // Zeitachse auf Sekunden-Einheit setzen
            tooltipFormat: 'dd.MM.yyyy HH:mm:ss',
            displayFormats: {
                second: 'dd.MM HH:mm:ss',
                minute: 'HH:mm',
                hour: 'HH:mm',
                day: 'dd.MMM',
                month: 'MM',
                year: 'yyyy'
            }
        },
        title: {
            display: true,
            text: 'Zeitpunkt'
        }
    },
    y: {
        beginAtZero: true,
        position: 'left',
        title: {
            display: true,
            //text: 'Watt'
        },
        ticks: {
            callback: function(value, index, values) {
                return formatCurrent(Number(value));
            }
        }
    }
}


const optionsRealtime = {
    layout: {},
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            mode: 'x',
            callbacks: {
                label: function(tooltipItem) {
                    // Ursprüngliches Label
                    let label = tooltipItem.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += formatCurrent(tooltipItem.raw);

                    const avg = calculateAverage(tooltipItem.dataset.data);
                    label += ` (∅: ${formatCurrent(avg)})`;
                    return label;
                }
            }
        },
        annotation: {
            annotations: {
                line1: {
                    type: 'line',
                    yMin: config.line1,
                    yMax: config.line1,
                    borderColor: line1Color,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    label: {
                        content: config.line1 + ' W',
                        display: false
                    }
                },
                line2: {
                    type: 'line',
                    yMin: config.line2,
                    yMax: config.line2,
                    borderColor: line2Color,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    label: {
                        content: config.line2 + ' W',
                        display: false
                    }
                },
                lineYZero: {
                    type: 'line',
                    yMin: 0,
                    yMax: 0,
                    borderColor: lineZeroColor,
                    borderWidth: 1,
                }
            }
        },

        legend: {
            display: false
        },
    },
    scales: scalesRealtime
};

// Configure diagram
const configRealtime = {
    type: 'line',
    data: {
        labels: timestamps, // timestamp on X-Axis
        datasets: [{
                label: 'EM',
                data: emPowerRows,
                borderColor: emColor,
                fill: false,
                customFormFieldName: 'realtime_chartShowEM',
                hidden: !config.realtime.chartShowEM
            },
            {
                label: '-PTotal',
                data: pmTotalPowerRows,
                borderColor: pmTotalColor,
                fill: false,
                customFormFieldName: 'realtime_chartShowPMTotal',
                hidden: !config.realtime.chartShowPMTotal,
            },
            {
                label: '-P1',
                data: pm1PowerRows,
                borderColor: pm1Color,
                fill: false,
                customFormFieldName: 'realtime_chartShowPM1',
                hidden: !config.realtime.chartShowPM1,
            },
            {
                label: '-P2',
                data: pm2PowerRows,
                borderColor: pm2Color,
                fill: false,
                customFormFieldName: 'realtime_chartShowPM2',
                hidden: !config.realtime.chartShowPM2,
            },
            {
                label: '-P3',
                data: pm3PowerRows,
                borderColor: pm3Color,
                fill: false,
                customFormFieldName: 'realtime_chartShowPM3',
                hidden: !config.realtime.chartShowPM3,
            },
        ]
    },
    options: optionsRealtime
};
