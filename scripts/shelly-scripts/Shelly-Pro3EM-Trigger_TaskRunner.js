// Konfiguration
let taskRunnerUrl = "http://meine.domain/mein-verzeichnis/api/taskrunner.php?apikey=987654321"; // URL zum PHP-Skript
let logZendureDataUrl = "http://meine.domain/mein-verzeichnis/api/log-zendure-data.php?apikey=987654321"; // URL zum PHP-Skript
let zendureUrls = [ // Hier koennen bis zu 3 Zendure-System inklusive der zugehoerigen Phase eingetragen werden.
    { phase: 1, url: "http://lokale-zendure-ip-phase1/properties/report" },
    { phase: 3, url: "http://lokale-zendure-ip-phase3/properties/report" }
];

let AppName = "Taskrunner";
let intervalInSeconds = 10;
let printLogMsg = true;
let pulledZendureData = {};
let finishedCount = 0;

function timerCallback() {
    try {
        log("----------------------------------------------");
        log("Starte Taskrunner Tasks...");

        sendTriggerSignalToServerApi();
        if (zendureUrls.length !== 0) pullLocaleZendureData();
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
                response = null;
            }
        );
    } catch (e) {
        info("Fehler beim Senden der Triggerdaten an die Server-API: " + e.message);
    }
}

function pushZendureDataToLogger() {
    log("Sende Zendure-Daten von " + zendureUrls.length + " Systemen an DB-Logger.");
    let finalPayload = {
        timestamp: Math.floor(Shelly.getComponentStatus("sys").unixtime),
        zendureData: pulledZendureData
    };

    info(JSON.stringify(finalPayload));
    Shelly.call("HTTP.POST", {
        url: logZendureDataUrl,
        body: JSON.stringify(finalPayload),
        timeout: 2,
        headers: { "Content-Type": "application/json" }
    }, function(serverResponse) {
        if (serverResponse && serverResponse.code === 200) {
            info("Pushe Zendure-Daten an Logger: " + serverResponse.body.slice(0, 100));
        } else {
            var errorMsg = "keine Antwort";
            if (serverResponse && serverResponse.body) {
                errorMsg += ", Http-Code: " + serverResponse.code
                errorMsg += serverResponse.body;
            }
            info("Fehler beim Weiterleiten an Server: " + errorMsg);
        }
        serverResponse = null;
    });
    finalPayload = null;

}

function pullLocaleZendureData() {
    log("Lese Zendure-Daten von " + zendureUrls.length + " Systemen ein.");
    for (let i = 0; i < zendureUrls.length; i++) {
        let item = zendureUrls[i];

        Shelly.call("HTTP.GET", { url: item.url, timeout: 2 }, function(res, err_code, err_msg, userdata) {
            let zKey = "zendure" + userdata.pNum;

            if (err_code === 0 && res && res.body) {
                log("Zenduredaten von Phase " + userdata.pNum + " erfolgreich gelesen.");
                let body = res.body;
                let keys = ["solarInputPower", "electricLevel", "socSet", "minSoc", "packInputPower", "outputPackPower", "packState", "hyperTmp"];
                let props = {};

                for (let j = 0; j < keys.length; j++) {
                    props[keys[j]] = getValue(body, keys[j]);
                }
                props["packTypes"] = getPackDataValAsString(body, "packType");

                pulledZendureData[zKey] = props;
            } else {
                print("Fehler bei Phase " + userdata.pNum + ": " + err_msg);
                pulledZendureData[zKey] = null;
            }

            finishedCount++;
            if (finishedCount === zendureUrls.length) {
                pushZendureDataToLogger();
                finishedCount = 0;
            }
        }, { pNum: item.phase });
    }
    item = null;
    pulledZendureData = null;
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

function getValue(b, key) {
    let keyStr = '"' + key + '":';
    let pos = b.indexOf(keyStr);
    if (pos < 0) return 0;
    pos += keyStr.length; // Nach ":"

    // Leerzeichen überspringen
    while (b.charAt(pos) === ' ') pos++;

    let end = b.indexOf(",", pos);
    let numStr = end > 0 ? b.substring(pos, end) : b.substring(pos);
    return +numStr;
}

function getPackDataValAsString(body, key) {
    let result = [];
    let pos = 0;

    let searchKey = '"' + key + '":';
    while ((pos = body.indexOf(searchKey, pos)) >= 0) {
        result.push(getValue(body, key));
        pos += key.length; // zum nächsten springen
    }

    return result.join(",") || "0";
}


Timer.set(
    intervalInSeconds * 1000, // Intervall in Millisekunden (300000 ms = 5 Minuten)
    true, // Timer wiederholen
    timerCallback
);