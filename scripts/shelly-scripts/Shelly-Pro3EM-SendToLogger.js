// Konfiguration
let loggingUrl = "http://www.fahni.org/test-visualizer/api/log-energy-data.php?apikey=987654321"; // URL zum PHP-Skript

let AppName = "EM3";
let devicePhase = ""; // Phase 1, 2 oder 3. Nur notwendig für Messung der Energieerzeugung, z.B. Shelly-PM Geräte
let intervalInSeconds = 2; // Gültige Werte sind: 2, 3, 4, 5, 6
let invertSign = false; // Je nachdem wie der Shelly verschaltet ist, muss das Vorzeichen umgedreht werden. Es müssen positive Werte geschickt werden!
let printLogMsg = false; // Für Debug-Zwecke default = false

// https://shelly-api-docs.shelly.cloud/gen2/Scripts/ShellyScriptLanguageFeatures
function timerCallback() {
    try {
        log("----------------------------------------------");
        log("Interval in Sekunden: " + intervalInSeconds);

        getShellyStatusData(devicePhase, function(actualData) {
            log("Verarbeite Shelly-Daten weiter.");

            sendDataToDBLogger(actualData);
        });
    } catch (e) {
        Info("Fehler beim TimerCallback: " + e.message);
    }
}

function sendDataToDBLogger(data) {
    if (data === undefined) {
        info("Keine Daten zum Senden bekommen");
        return;
    }
    log("Bereite Daten zum Senden vor.");
    let time = getTimestamp();

    // Erstelle zu sendendes JSON
    let dataToSend = {
        timestamp: time,
        device_type: data.device_type,
        interval_in_seconds: intervalInSeconds,
        a_act_power: data.a_act_power,
        b_act_power: data.b_act_power,
        c_act_power: data.c_act_power,
        total_act_power: data.total_act_power
    };

    // Sende berechnete Daten an DB-Logger
    log("Sende bisherige Daten an DB-Logger-Server.");
    log(JSON.stringify(dataToSend));

    Shelly.call(
        "HTTP.POST", {
            url: loggingUrl,
            body: JSON.stringify(dataToSend),
            timeout: 5, // Timeout in Sekunden
            headers: { "Content-Type": "application/json" }
        },
        function(response) {
            if (response && response.code === 200) {
                info("Daten an den DB-Logger-API gesendet: " + (response.body || "Kein Inhalt"));
            } else {
                const errorMessage = response && response.body ?
                    response.body :
                    "Keine Antwort vom Server oder Fehler ohne Nachricht.";
                info("Fehler beim Senden der Daten: " + errorMessage);
            }
        }
    );
}

function getShellyStatusData(devicePhase, callbackFkt) {
    Shelly.call(
        "Shelly.GetStatus",
        "",
        function(result, error_code, error_message) {
            if (error_code !== 0) {
                info("Fehler beim Abrufen der lokalen Shelly-Daten: ", error_message);
                return;
            }

            let resultData = null;
            if (result["em:0"] != undefined) {
                log("Daten erfolgreich gelesen für EM3, Total-Act-Power: " + result["em:0"].total_act_power);
                resultData = {
                    device_type: "EM",
                    a_act_power: prepareValue(result["em:0"].a_act_power, invertSign, false, false),
                    b_act_power: prepareValue(result["em:0"].b_act_power, invertSign, false, false),
                    c_act_power: prepareValue(result["em:0"].c_act_power, invertSign, false, false),
                    total_act_power: prepareValue(result["em:0"].total_act_power, invertSign, false, false)
                };
            } else if (result["switch:0"] != undefined) {
                log("Daten erfolgreich gelesen für PM, Swith-Power: " + result["switch:0"].apower);
                resultData = {
                    device_type: "PM" + devicePhase,
                    a_act_power: prepareValue(result["switch:0"].apower, invertSign, (devicePhase != "1"), true),
                    b_act_power: prepareValue(result["switch:0"].apower, invertSign, (devicePhase != "2"), true),
                    c_act_power: prepareValue(result["switch:0"].apower, invertSign, (devicePhase != "3"), true),
                    total_act_power: prepareValue(result["switch:0"].apower, invertSign, false, true)
                };
            } else {
                info("Fehler, konnte Daten weder als Shelly-EM noch als Shelly-PM-Daten interpretieren");
                return;
            }

            callbackFkt(resultData);
        }
    );
}

function prepareValue(val, invertValSign, setToZero, ignoreMismeasurement) {
    val = (setToZero ? 0 : val);
    if (ignoreMismeasurement) {
        val = ((val < 1.5 && val > -1.5) ? 0 : val); // Fehlmessungen kleiner 1.5 Watt
    }
    return (invertValSign ? val * -1 : val);
}

function getTimestamp() {
    let now = new Date();
    let milliseconds = now.getTime();
    let seconds = now.getSeconds();

    // Prüfe, ob die Sekunden innerhalb des gewünschten Intervalls liegen
    if (seconds % intervalInSeconds !== 0) {
        // Zeit bis zur nächsten gültigen Sekunde berechnen
        let adjustment = intervalInSeconds - (seconds % intervalInSeconds);
        milliseconds += adjustment * 1000; // Korrigiere die Zeit in Millisekunden
        now = new Date(milliseconds);
    }

    let date = now.getFullYear() + '-' + ('0' + (now.getMonth() + 1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2);
    let time = ('0' + now.getHours()).slice(-2) + ":" + ('0' + now.getMinutes()).slice(-2) + ":" + ('0' + now.getSeconds()).slice(-2);
    let dateTime = date + ' ' + time;

    return dateTime; // Ausgabe: 2024-12-07 19:11:56	
}

function log(msg) {
    if (!printLogMsg) return;
    print(AppName + ": " + msg);
}

function info(msg) {
    print(AppName + ": " + msg);
}

Timer.set(
    intervalInSeconds * 1000, // Intervall in Millisekunden (300000 ms = 5 Minuten)
    true, // Timer wiederholen
    timerCallback
);