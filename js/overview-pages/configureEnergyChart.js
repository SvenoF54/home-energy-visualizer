const emColor = getComputedStyle(document.documentElement).getPropertyValue('--em-color').trim();
const emOverZeroColor = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color').trim();
const consumptionCompleteColor = getComputedStyle(document.documentElement).getPropertyValue('--consumption-complete-color').trim();
const emOverZeroColor2 = getComputedStyle(document.documentElement).getPropertyValue('--em-over-zero-color2').trim();

const savingsColor = getComputedStyle(document.documentElement).getPropertyValue('--savings-color').trim();
const savingsColor2 = getComputedStyle(document.documentElement).getPropertyValue('--savings-color2').trim();

const feedInColor = getComputedStyle(document.documentElement).getPropertyValue('--feed-in-color').trim();
const feedInColor2 = getComputedStyle(document.documentElement).getPropertyValue('--feed-in-color2').trim();

const pm1Color = getComputedStyle(document.documentElement).getPropertyValue('--pm1-color').trim();
const pm2Color = getComputedStyle(document.documentElement).getPropertyValue('--pm2-color').trim();
const pm3Color = getComputedStyle(document.documentElement).getPropertyValue('--pm3-color').trim();
const pmTotalColor = getComputedStyle(document.documentElement).getPropertyValue('--pm-total-color').trim();

const line1Color = getComputedStyle(document.documentElement).getPropertyValue('--line1-color').trim();
const line2Color = getComputedStyle(document.documentElement).getPropertyValue('--line2-color').trim();
const lineZeroColor = getComputedStyle(document.documentElement).getPropertyValue('--line-zero-color').trim();


const scalesEnergy = {
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
                return formatCurrent(Number(value)) + 'h';
            }
        }
    }
}


const optionsEnergy = {
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
                    const priceFieldName = tooltipItem.dataset.customPriceFieldName;
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
            display: true,
            onClick: function(event, legendItem) {
                const clickedDatasetIndex = legendItem.datasetIndex;
                const clickedLabel = legendItem.text;
                const dataset = energyChart.data.datasets[clickedDatasetIndex];

                // Update chart
                dataset.hidden = !dataset.hidden;
                energyChart.update();

                // Set hidden form field to recover chart settings on next page call
                const customFormFieldName = dataset.customFormFieldName;
                $('#' + customFormFieldName).val(!dataset.hidden);
            },
        },
    },
    scales: scalesEnergy
};

// Plugins, here the button for the table view
const pluginsEnergy = [{
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

        // Button 2: autarky-view
        const button2X = chartArea.right - 110;
        const button2Y = chartArea.top - 30;

        ctx.fillStyle = 'blue';
        ctx.fillRect(button2X, button2Y, 100, 25);

        ctx.fillStyle = 'white';
        ctx.font = '12px Arial';
        ctx.textBaseline = 'middle';
        ctx.fillText('Autarkieansicht', button2X + 10, button2Y + 12);

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
                    $('#chartOrTableView').val('EnergyTable');
                }

                // Button 2: autarky-view
                if (x > button2X && x < button2X + 100 && y > button2Y && y < button2Y + 25) {
                    $('#energy-chart-container').hide();
                    $('#energy-table-container').hide();
                    $('#autarky-chart-container').show();
                    $('#chartOrTableView').val('AutarkyChart');
                }
            });
        }
    }
}];

const emOverZeroData1 = {
    label: '(1) Stromeinkauf',
    data: data1.map(item => item.emOZ),
    backgroundColor: emOverZeroColor,
    borderColor: emOverZeroColor,
    borderWidth: 1,
    stack: 'Stack EM1',
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowEnergyOverZero',
    customPriceFieldName: 'emOZPrice',
    hidden: !config.energy1.chartShowEnergyOverZero
};
const feedInData1 = {
    label: '(1) Netzeinspeisung',
    data: data1.map(item => item.emUZ),
    borderColor: feedInColor,
    backgroundColor: feedInColor,
    stack: 'Stack EM1',
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowFeedIn',
    customPriceFieldName: 'emUZPrice',
    hidden: !config.energy1.chartShowFeedIn
};
const savingsData1 = {
    label: '(1) Energie Ersparnis',
    data: data1.map(item => item.pmSvg),
    borderColor: savingsColor,
    backgroundColor: savingsColor,
    stack: 'Stack PV1',
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowSavings',
    customPriceFieldName: 'pmSvgPrice',
    hidden: !config.energy1.chartShowSavings
};
const energyOverZeroPlusSavings1 = {
    label: '(1) Ersparnis addiert',
    data: data1.map(item => item.pmSvg),
    fill: false,
    backgroundColor: consumptionCompleteColor,
    tension: 0.1,
    borderWidth: 0,
    maxBarThickness: 30,
    customDataSourceNo: 1,
    customFormFieldName: 'energy1_chartShowEnergyOverZeroPlusSavings',
    stack: 'Stack EM1',
    hidden: !config.energy1.chartShowEnergyOverZeroPlusSavings,
};
const emOverZeroData2 = {
    label: '(2) Stromeinkauf',
    data: data2.map(item => item.emOZ),
    backgroundColor: emOverZeroColor2,
    borderColor: emOverZeroColor2,
    borderWidth: 1,
    stack: 'Stack EM2',
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowEnergyOverZero',
    customPriceFieldName: 'emOZPrice',
    hidden: !config.energy2.chartShowEnergyOverZero,
}
const feedInData2 = {
    label: '(2) Netzeinspeisung',
    data: data2.map(item => item.emUZ),
    borderColor: feedInColor2,
    backgroundColor: feedInColor2,
    stack: 'Stack EM2',
    maxBarThickness: 30,
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowFeedIn',
    customPriceFieldName: 'emUZPrice',
    hidden: !config.energy2.chartShowFeedIn,
}
const savingsData2 = {
    label: '(2) Energie Ersparnis',
    data: data2.map(item => item.pmSvg),
    borderColor: savingsColor2,
    backgroundColor: savingsColor2,
    stack: 'Stack PV2',
    maxBarThickness: 30,
    customDataSourceNo: 2,
    customFormFieldName: 'energy2_chartShowSavings',
    customPriceFieldName: 'pmSvgPrice',
    hidden: !config.energy2.chartShowSavings,
}

const energyDataset = [];
energyDataset.push(emOverZeroData1);
energyDataset.push(energyOverZeroPlusSavings1);
energyDataset.push(feedInData1);
if (data2.length > 0) energyDataset.push(emOverZeroData2);
if (data2.length > 0) energyDataset.push(feedInData2);
energyDataset.push(savingsData1);
if (data2.length > 0) energyDataset.push(savingsData2);

// configure diagram
const configEnergy = {
    type: 'bar',
    data: {
        labels: timestampsXAxis,
        datasets: energyDataset
    },
    options: optionsEnergy,
    plugins: pluginsEnergy
};
