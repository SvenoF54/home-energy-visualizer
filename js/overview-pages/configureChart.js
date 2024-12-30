const scales = {
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
        stacked: true,
        position: 'left',
        title: {
            display: true,
            //text: 'Watt'
        },
        ticks: {
            callback: function(value, index, values) {
                return value + ' W'; // add Watt after numbers
            }
        }
    }
}


const options = {
    layout: {
        padding: {
            left: 20,
            right: 20,
            top: 20,
            bottom: 20
        }
    },
    onClick: (event, elements, chart) => {
        if (elements.length > 0) {
            const element = elements[0];
            const datasetIndex = element.datasetIndex;
            const dataIndex = element.index;

            const dataset = chart.data.datasets[datasetIndex];
            const customDataSourceNo = dataset.customDataSourceNo;
            const label = chart.data.labels[dataIndex]; // X-Wert
            const value = dataset.data[dataIndex]; // value from the bar

            var url = "";
            switch (true) {
                case window.location.pathname.includes("yearsOverview"):
                    const selectedYear = label;
                    url = `${URL_PREFIX}monthsOverview.php?year1=${selectedYear}&year2=${selectedYear-1}`;
                    break;
                case window.location.pathname.includes("monthsOverview"):
                    const selectedMonthYear = label[customDataSourceNo - 1];
                    const [month, year] = selectedMonthYear.split('.');
                    const monthBefore = new Date(year, month - 1, 1, 0, 0, 0);

                    url = `${URL_PREFIX}daysOverview.php?month1=${month}&year1=${year}&month2=${monthBefore.getMonth()}&year2=${monthBefore.getYear()}`;
                    break;
                case window.location.pathname.includes("daysOverview"):
                    const selectedDay = label[customDataSourceNo - 1];
                    const dayBefore = parseDate(selectedDay);
                    dayBefore.setDate(dayBefore.getDate() - 1);

                    url = `${URL_PREFIX}hoursOverview.php?day1=${selectedDay}&day2=${formatDate(dayBefore)}`;
                    break;
                case window.location.pathname.includes("hoursOverview"):
                    const selectedDateTime = timestampsTooltip[dataIndex][customDataSourceNo - 1];
                    let fromDateTime = parseDate(selectedDateTime);
                    let toDateTime = parseDate(selectedDateTime);

                    toDateTime.setHours(toDateTime.getHours() + 1);
                    fromDateTime.setHours(fromDateTime.getHours());
                    url = `${URL_PREFIX}realtimeOverview.php?from-date=${formatDateTime(fromDateTime)}&to-date=${formatDateTime(toDateTime)}&hours=0`;
                    break;
            }

            window.location.href = url;
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
                    const index = tooltipItems[0].dataIndex;
                    const dataset = tooltipItems[0].dataset;
                    const customDataSourceNo = tooltipItems[0].dataset.customDataSourceNo;

                    // original Label
                    let label = dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    label += timestampsTooltip[index][customDataSourceNo - 1];

                    return label;
                },
                label: function(tooltipItem) {
                    const energy = tooltipItem.parsed.y;
                    const customDataSourceNo = tooltipItem.dataset.customDataSourceNo;
                    const priceFieldName = tooltipItem.dataset.priceFieldName;
                    const dataArray = customDataSourceNo === 1 ? data1 : data2;
                    const dataPoint = dataArray[tooltipItem.dataIndex];

                    let label = formatCurrent(energy, "h");
                    if (priceFieldName && dataPoint[priceFieldName] !== undefined) {
                        label += ' (' + formatPrice(dataPoint[priceFieldName]) + ')';
                    } else {
                        label += ' (-)';
                    }

                    return label;
                }
            },
        },
        annotation: {
            // Shows the 2 lines
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
                    borderColor: 'maroon',
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

// Plugins, here the button for the table view
const plugins = [{
    id: 'customButtonPlugin',
    afterDraw(chart, args, options) {
        const { ctx, chartArea } = chart;
        const buttonX = chartArea.right - 100;
        const buttonY = chartArea.top - 30;

        // Draw button
        ctx.fillStyle = 'blue';
        ctx.fillRect(buttonX, buttonY, 100, 25);

        // Draw Text
        ctx.fillStyle = 'white';
        ctx.font = '12px Arial';
        ctx.fillText('Tabellenansicht', buttonX + 10, buttonY + 12);

        // Add Event-Listener
        if (!chart.customButtonListener) {
            chart.customButtonListener = true;
            chart.canvas.addEventListener('click', function(event) {
                const canvasPosition = Chart.helpers.getRelativePosition(event, chart);
                const x = canvasPosition.x;
                const y = canvasPosition.y;

                // If click is for button
                if (x > buttonX && x < buttonX + 100 && y > buttonY && y < buttonY + 30) {
                    $('#chart-container').css('display', 'none');
                    $('#table-container').css('display', 'block');
                }
            });
        }
    },
}];

// configure diagram
const config = {
    type: 'bar',
    data: {
        labels: timestampsXAxis,
        datasets: [{
                label: '(1) Stromeinkauf',
                data: data1.map(item => item.emOZ),
                backgroundColor: 'rgb(190,110,110)',
                borderColor: 'rgb(190,110,110)',
                borderWidth: 1,
                stack: 'Stack EM1',
                maxBarThickness: 30,
                customDataSourceNo: 1,
                priceFieldName: 'emOZPrice'
            },
            {
                label: '(1) Netzeinspeisung',
                data: data1.map(item => item.emUZ),
                borderColor: 'rgb(127, 127, 181)',
                backgroundColor: 'rgb(127, 127, 181)',
                stack: 'Stack EM1',
                maxBarThickness: 30,
                customDataSourceNo: 1,
                priceFieldName: 'emUZPrice'
            },
            {
                label: '(2) Stromeinkauf',
                data: data2.map(item => item.emOZ),
                backgroundColor: 'rgb(150,90,110)',
                borderColor: 'rgb(150,90,110)',
                borderWidth: 1,
                stack: 'Stack EM2',

                customDataSourceNo: 2,
                priceFieldName: 'emOZPrice',
                hidden: (data2.length == 0)
            },
            {
                label: '(2) Netzeinspeisung',
                data: data2.map(item => item.emUZ),
                borderColor: 'rgb(97, 97, 161)',
                backgroundColor: 'rgb(97, 97, 161)',
                stack: 'Stack EM2',
                maxBarThickness: 30,
                customDataSourceNo: 2,
                priceFieldName: 'emUZPrice',
                hidden: (data2.length == 0)
            },
            {
                label: '(1) Energie Ersparnis',
                data: data1.map(item => item.pmSvg),
                borderColor: 'rgb(127, 181, 181)',
                backgroundColor: 'rgb(127, 181, 181)',
                stack: 'Stack PV1',
                maxBarThickness: 30,
                priceFieldName: 'pmSvgPrice',
                customDataSourceNo: 1
            },
            {
                label: '(2) Energie Ersparnis',
                data: data2.map(item => item.pmSvg),
                borderColor: 'rgb(97, 141, 141)',
                backgroundColor: 'rgb(97, 141, 141)',
                stack: 'Stack PV2',
                maxBarThickness: 30,
                customDataSourceNo: 2,
                priceFieldName: 'pmSvgPrice',
                hidden: (data2.length == 0)
            },
        ]
    },
    options,
    plugins
};