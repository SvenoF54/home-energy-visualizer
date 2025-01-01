const emColor = getComputedStyle(document.documentElement).getPropertyValue('--em-color').trim();
const pm1Color = getComputedStyle(document.documentElement).getPropertyValue('--pm1-color').trim();
const pm2Color = getComputedStyle(document.documentElement).getPropertyValue('--pm2-color').trim();
const pm3Color = getComputedStyle(document.documentElement).getPropertyValue('--pm3-color').trim();
const pmTotalColor = getComputedStyle(document.documentElement).getPropertyValue('--pm-total-color').trim();

const line1Color = getComputedStyle(document.documentElement).getPropertyValue('--line1-color').trim();
const line2Color = getComputedStyle(document.documentElement).getPropertyValue('--line2-color').trim();
const lineZeroColor = getComputedStyle(document.documentElement).getPropertyValue('--line-zero-color').trim();



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
                    borderColor: line1Color,
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
                    borderColor: line2Color,
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
                    borderColor: lineZeroColor,
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
                borderColor: emColor,
                fill: false,
            },
            {
                label: '-P1',
                data: pm1PowerRows,
                borderColor: pm1Color,
                fill: false,
                hidden: true
            },
            {
                label: '-P2',
                data: pm2PowerRows,
                borderColor: pm2Color,
                fill: false,
                hidden: true
            },
            {
                label: '-P3',
                data: pm3PowerRows,
                borderColor: pm3Color,
                fill: false,
                hidden: true
            },
            {
                label: '-PTotal',
                data: pmTotalPowerRows,
                borderColor: pmTotalColor,
                fill: false
            },
        ]
    },
    options
};