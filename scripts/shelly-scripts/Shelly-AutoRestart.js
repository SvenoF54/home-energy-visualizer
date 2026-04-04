let targetScriptIds = [1, 3]; // Ändere die ID entsprechend des Shelly-Scripte für den Logger. Diese kann der Browser-URL entnommen werden (.../script/2).
let checkIntervalMinutes = 5; // 15 Minuten
let checkInterval = checkIntervalMinutes * 60 * 1000;

function checkAndRestartScript(id) {
    print("Überprüfe Status des Skripts mit ID:", id);

    Shelly.call("Script.GetStatus", { id: id }, function(result, error_code, error_message) {
        if (error_code !== 0) {
            print("Fehler beim Abrufen des Status für ID", id + ":", error_message);
            return;
        }

        if (!result.running) {
            print("Skript mit ID", id, "läuft nicht, starte es neu...");
            Shelly.call("Script.Start", { id: id }, function(res, err_code, err_msg) {
                if (err_code === 0) {
                    print("Skript mit ID", id, "erfolgreich neu gestartet.");
                } else {
                    print("Fehler beim Starten des Skripts mit ID", id + ":", err_msg);
                }
            });
        } else {
            print("Skript mit ID", id, "läuft bereits.");
        }
    });
}

Timer.set(checkInterval, true, function() {
    for (let i = 0; i < targetScriptIds.length; i++) {
        checkAndRestartScript(targetScriptIds[i]);
    }
});
