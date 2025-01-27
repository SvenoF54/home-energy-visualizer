function calculateAverage(dataArray) {
    const sum = dataArray.reduce((acc, value) => acc + value, 0);
    return (sum / dataArray.length).toFixed(2);
}



// Formats the power values (e.g., 300W, 5.3kW)
function formatCurrent(val, suffix = "") {
    if (val == null || isNaN(val)) {
        return "0 W" + suffix;
    }

    val = Number(val);
    let formattedValue;

    if (Math.abs(val) >= 1000000) {
        // For mW
        formattedValue = (val / 1000000).toFixed(3).replace('.', ',');
        if ((val / 1000000) % 1 === 0) {
            formattedValue = (val / 1000000).toFixed(0).replace('.', ',');
        }
        return formattedValue + " mW" + suffix;
    } else if (Math.abs(val) >= 1000) {
        // For kW
        formattedValue = (val / 1000).toFixed(1).replace('.', ',');
        if ((val / 1000) % 1 === 0) {
            formattedValue = (val / 1000).toFixed(0).replace('.', ',');
        }
        return formattedValue + " kW" + suffix;
    } else {
        // For W
        formattedValue = val.toFixed(2).replace('.', ',');
        if ((val) % 1 === 0) {
            formattedValue = val.toFixed(0).replace('.', ',');
        }
        return formattedValue + " W" + suffix;
    }
}

function formatPrice(priceInCent) {
    return priceInCent == 0 ? "-" : (priceInCent / 100).toFixed(2) + "€";
}

function formatDateTime(dateTime) {
    let day = String(dateTime.getDate()).padStart(2, '0');
    let month = String(dateTime.getMonth() + 1).padStart(2, '0');
    let year = dateTime.getFullYear();
    let hours = String(dateTime.getHours()).padStart(2, '0');
    let minutes = String(dateTime.getMinutes()).padStart(2, '0');
    let seconds = String(dateTime.getSeconds()).padStart(2, '0');

    return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
}

function formatDate(date) {
    let day = String(date.getDate()).padStart(2, '0');
    let month = String(date.getMonth() + 1).padStart(2, '0');
    let year = date.getFullYear();

    return `${day}.${month}.${year}`;
}

function parseDate(dateString) {
    if (!dateString) {
        return new Date();
    }

    let [datePart, timePart] = dateString.split(' ');

    // Use today date if not valid
    let [day, month, year] = datePart ? datePart.split('.') : [null, null, null];
    day = parseInt(day) || new Date().getDate();
    month = parseInt(month) || new Date().getMonth() + 1; // Monate: 0-basiert
    year = parseInt(year) || new Date().getFullYear();

    // Time (default 00:00:00)
    let [hours, minutes, seconds] = timePart ? timePart.split(':') : [null, null, null];
    hours = parseInt(hours) || 0;
    minutes = parseInt(minutes) || 0;
    seconds = parseInt(seconds) || 0;

    return new Date(year, month - 1, day, hours, minutes, seconds);
}

function parseDbDate(dateString) {
    // Parse date format 'YYYY-MM-DD HH:MM:SS'
    // Remoce seconds, because JS-Date don't need them
    let dateParts = dateString.split(' '); // Splitte das Datum und die Uhrzeit
    let date = dateParts[0]; // '2024-10-01'
    let time = dateParts[1]; // '00:00:00'

    let dateArray = date.split('-');
    let year = parseInt(dateArray[0]);
    let month = parseInt(dateArray[1]) - 1; // JavaScript zählt Monate von 0 bis 11
    let day = parseInt(dateArray[2]);

    let timeArray = time.split(':');
    let hours = parseInt(timeArray[0]);
    let minutes = parseInt(timeArray[1]);
    let seconds = parseInt(timeArray[2]);

    let parsedDate = new Date(year, month, day, hours, minutes, seconds);

    return parsedDate;
}

function formatNumber(val, digits = 0) {
    if (isNaN(val)) {
        return "0";
    }

    // Format the number with fixed decimals
    let formattedValue = parseFloat(val)
        .toFixed(digits) // round to given decimal digits
        .replace('.', ',');

    formattedValue = formattedValue.replace(new RegExp(`\\B(?=(\\d{${digits}})+(?!\\d))`, 'g'), '.');

    if (digits > 0) {
        formattedValue = formattedValue.replace(/,?0+$/, '');
    }

    return formattedValue;
}
