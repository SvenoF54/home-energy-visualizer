let targetScriptId = 1; // Ändere die ID entsprechend des Shelly-Scripte für den Logger. Diese kann der Browser-URL entnommen werden (.../script/2).
let checkInterval = 3600 * 1000; // 1 Stunde in Millisekunden

Timer.set(checkInterval, true, function() {
    print("Überprüfe Status des Skripts mit ID:", targetScriptId);

    Shelly.call("Script.GetStatus", { id: targetScriptId }, function(result, error_code, error_message) {
        if (error_code !== 0) {
            print("Fehler beim Abrufen des Status:", error_message);
            return;
        }

        if (!result.running) {
            print("Skript läuft nicht, starte es neu...");
            Shelly.call("Script.Start", { id: targetScriptId }, function(res, err_code, err_msg) {
                if (err_code === 0) {
                    print("Skript erfolgreich neu gestartet.");
                } else {
                    print("Fehler beim Starten des Skripts:", err_msg);
                }
            });
        } else {
            print("Skript läuft bereits.");
        }
    });
});