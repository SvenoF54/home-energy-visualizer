const scales = {
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
                return value + ' W'; // Watt hinter den Zahlen
            }
        }
    }
}


const options = {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
        padding: {
            left: 20,
            right: 20,
            top: 20,
            bottom: 20
        }
    },
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
                    yMin: line1_selected,
                    yMax: line1_selected,
                    borderColor: 'rgb(90, 90, 50)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    label: {
                        content: line1_selected + ' W',
                        display: false
                    }
                },
                line2: {
                    type: 'line',
                    yMin: line2_selected,
                    yMax: line2_selected,
                    borderColor: 'rgb(90, 90, 180)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    label: {
                        content: line2_selected + ' W',
                        display: false
                    }
                },
                lineYZero: {
                    type: 'line',
                    yMin: 0,
                    yMax: 0,
                    borderColor: 'marroon',
                    borderWidth: 1,
                }
            }
        },

        legend: {
            display: true
        },
    },
    scales
};

// Configure diagram
const config = {
    type: 'line',
    data: {
        labels: timestamps, // timestamp on X-Axis
        datasets: [{
                label: 'EM',
                data: emPowerRows,
                borderColor: 'rgb(190,110,110)',
                fill: false,
            },
            {
                label: '-P1',
                data: pm1PowerRows,
                borderColor: 'rgba(144, 238, 144, 1)',
                fill: false,
                hidden: true
            },
            {
                label: '-P2',
                data: pm2PowerRows,
                borderColor: 'rgba(152, 251, 152, 1)',
                fill: false,
                hidden: true
            },
            {
                label: '-P3',
                data: pm3PowerRows,
                borderColor: 'rgba(119, 221, 119, 1)',
                fill: false,
                hidden: true
            },
            {
                label: '-PTotal',
                data: pmTotalPowerRows,
                borderColor: 'rgb(127, 181, 181)',
                fill: false
            },
        ]
    },
    options
};
