// Konfiguration
let taskRunnerUrl = "http://meine.domain/mein-verzeichnis/api/taskrunner.php?apikey=987654321"; // URL zum PHP-Skript
let logZendureDataUrl = "http://meine.domain/mein-verzeichnis/api/log-zendure-data.php?apikey=987654321"; // URL zum PHP-Skript
let zendureUrl1 = "http://lokale-ip-zendure/properties/report";

let AppName = "Taskrunner";
let intervalInSeconds = 10;
let printLogMsg = true;


function timerCallback() {
    try {
        log("----------------------------------------------");
        log("Starte Taskrunner Tasks...");

        sendTriggerSignalToServerApi();
        forwardZendureData(zendureUrl1);
    } catch (e) {
        Info("Fehler beim TimerCallback: " + e.message);
    }
}

function sendTriggerSignalToServerApi(data) {
    try {
        log("Bereite Triggerdaten vor.");
        let time = getTimestamp(true);

        // Erstelle zu sendendes JSON
        let dataToSend = {
            timestamp: time,
        };

        // Sende berechnete Daten an DB-Logger
        log("Sende Triggerdaten an die Server-API.");

        Shelly.call(
            "HTTP.POST", {
                url: taskRunnerUrl,
                body: JSON.stringify(dataToSend),
                timeout: 2, // Timeout in Sekunden
                headers: { "Content-Type": "application/json" }
            },
            function(response) {
                if (response && response.code === 200) {
                    info("Triggerdaten erfolgreich an den DB-Logger-Server gesendet: " + (response.body || "Kein Inhalt"));
                } else {
                    const errorMessage = response && response.body ?
                        response.body :
                        "Keine Antwort vom Server oder Fehler ohne Nachricht.";
                    info("Fehler beim Senden der Triggerdaten: " + errorMessage);
                }
            }
        );
    } catch (e) {
        info("Fehler beim Senden der Triggerdaten an die Server-API: " + e.message);
    }
}

function forwardZendureData(curZendureUrl) {
    log("Lese Zendure-Daten ein.");
    Shelly.call("HTTP.GET", {
        url: curZendureUrl,
        timeout: 2
    }, function(response) {
        if (response && response.code === 200 && response.body) {
            log("Zenduredaten erfolgreich gelesen, sende Zenduredaten an DB-Server");
            // 2. Rohdaten 1:1 als POST weiterleiten
            let dataToSend = {
                timestamp: getTimestamp(true),
                zendureData: JSON.parse(response.body)
            };

            Shelly.call("HTTP.POST", {
                url: logZendureDataUrl,
                body: JSON.stringify(dataToSend),
                timeout: 2,
                headers: { "Content-Type": "application/json" }
            }, function(serverResponse) {
                if (serverResponse && serverResponse.code === 200) {
                    info("Zendure-Daten 1:1 weitergeleitet: " + serverResponse.body.slice(0, 100));
                } else {
                    var errorMsg = "HTTP " + (serverResponse ? serverResponse.code : "keine Antwort");
                    if (serverResponse && serverResponse.body) {
                        errorMsg = serverResponse.body;
                    }
                    info("Fehler beim Weiterleiten an Server: " + errorMsg);
                }
            });
        } else {
            info("Fehler beim lesen der Zendure-Daten: " + (response ? response.body || "Keine Daten"));
        }
    });
}

function getTimestamp(onlyEvenSeconds) {
    let now = new Date();
    let miliseconds = now.getTime();
    let seconds = now.getSeconds();
    // Prüfe, ob die Sekunden ungerade sind
    if (onlyEvenSeconds && (seconds % 2 !== 0)) {
        // Eine Sekunde hinzufügen, um zur nächsten geraden Sekunde zu gelangen
        miliseconds += 1000;
        now = new Date(miliseconds);
    }

    let date = now.getFullYear() + '-' + ('0' + (now.getMonth() + 1)).slice(-2) + '-' + ('0' + now.getDate()).slice(-2);
    let time = ('0' + now.getHours()).slice(-2) + ":" + ('0' + now.getMinutes()).slice(-2) + ":" + ('0' + now.getSeconds()).slice(-2);
    let dateTime = date + ' ' + time;

    return dateTime; // Ausgabe: 2024-11-07 19:11:56	
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
