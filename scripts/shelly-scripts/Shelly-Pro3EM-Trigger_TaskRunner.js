// Konfiguration
let loggingUrl = "http://meine.domain/mein-verzeichnis/api/taskrunner.php?apikey=987654321"; // URL zum PHP-Skript

let AppName = "Taskrunner";
let intervalInSeconds = 60;
let printLogMsg = true;

function timerCallback() {
    try {
        log("----------------------------------------------");
        log("Sende Triger-Signal an den Server");

        sendTriggerSignalToServerApi();
    } catch (e) {
        Info("Fehler beim TimerCallback: " + e.message);
    }
}

function sendTriggerSignalToServerApi(data) {
    try {
        log("Bereite Daten zum Senden vor.");
        let time = getTimestamp(true);

        // Erstelle zu sendendes JSON
        let dataToSend = {
            timestamp: time,
        };

        // Sende berechnete Daten an DB-Logger
        log("Sende Daten an die Server-API.");

        Shelly.call(
            "HTTP.POST", {
                url: loggingUrl,
                body: JSON.stringify(dataToSend),
                timeout: 5, // Timeout in Sekunden
                headers: { "Content-Type": "application/json" }
            },
            function(response) {
                if (response && response.code === 200) {
                    info("Daten erfolgreich an den DB-Logger-Server gesendet: " + (response.body || "Kein Inhalt"));
                } else {
                    const errorMessage = response && response.body ?
                        response.body :
                        "Keine Antwort vom Server oder Fehler ohne Nachricht.";
                    info("Fehler beim Senden der Daten: " + errorMessage);
                }
            }
        );
    } catch (e) {
        info("Fehler beim Senden der Daten an die Server-API: " + e.message);
    }
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
