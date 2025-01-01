# Projektname
Energie-Visualisierung für zu Hause, Beschreibung siehe hier [readme.md](./README.md).

## Installationsschritte

Für die Benutzung des Programms werden gewisse technische Kenntnisse, wie das erstellen einer Datenbank, sowie das kopieren der Programmdateien auf den Webserver vorrausgesetzt. 

**Die Installation erfolgt manuell, es gibt keinen automatischen Installationsmechanismus.**

### Systemvorraussetzungen

- Webserversystem mit LAMP-Stack
- PHP 8.3
- Mysql / Maria Datenbank
- Webserver 

### Projektdateien herunterladen

- Diese Projekdateien in einem Verzeichnis auf dem Rechner lokal herunterladen

### Datenbank erstellen

- Datenbank beim Provider erstellen und Zugangsdaten für später notieren. 
- Schema der Datenbank erstellen, mit einem Datenbanktool.
- Das DB-Skript ist hier zu finden: [scripts\sql-scripts\create-table.sql](scripts\sql-scripts\create-table.sql)

#### HINWEISE
- Bei der Erfassung von Echtzeitdaten enstehen z.B. im 2 Sekundentakt ca. 1 Million Datensätze pro Monat
- Bitte regelmäßg Datensicherungen durchführen, auf jeden Fall BEVOR Updates installiert werden

### Config-Datei vorbereiten

- Eine eigene Config-Datei im Ordner config erstellen mit dem Namen: local-config.php
- Als Vorlage die Daten local-config.php.sample benutzen
- Die geforderten Daten eintragen
- Der hier eingetragene API-Key muss auch bei den Shelly-Skripten verwendet werden

### PHP-Skripte auf dem Server kopieren

- Die heruntergeladenen Dateien inkl. der eigenen Config-Datei per SFTP o.ä. auf den Webserver übertragen

### Navigation anpassen (optional)

Falls man keine Echtzeitdaten verwendet, kann man die Navigation so anpassen um die Ansichten hierfür zu entfernen.

**Navigation**
- Datei: views\partials\navigation.phtml
- Für die Variable $pages die Seite entfernen: Startseite, Stunden, sowie die Tagesansicht

**Startseite**
- Datei: index.php
- Hier z.B. die monthsOverview.php eintragen


## Shelly-Skripte

Für die Erfassung von Echtzeidaten müssen auf jedem Energiemessgerät die Shelly-Skripte installiert werden

### Skript PRO Energiemessgerät

Generell sind alle Skripte für die Erfassung der Energiewerte identisch und unterscheiden sich nur durch die Konfiguration im oberen Abschnitt. Die Skripte erkennen automatisch ob sie auf einem Shelly-EM oder Shelly PM laufen.

#### Verbrauchsmessung

Für die Verbrauchsmessung ist ein Skript vorgesehen, welches den aufsummierten Verbrauch aller 3 Phasen als Summe speichert. 

Im oberen Abschnitt sind folgende Parameter anzupassen:
- loggingUrl
- API-Key (wie in der local-config.php angegeben)

Das Skript ist hier zu finden [scripts\shelly-scripts\Shelly-Pro3EM-SendToLogger.js](scripts\shelly-scripts\Shelly-Pro3EM-SendToLogger.js)

#### Erzeugungsmessung

Es ist möglich die Stromerzeugung für jede Phase einzeln zu messen. Hierfür ist für jeden Energiemesser ein Skript anzulegen.

Im oberen Abschnitt sind folgende Parameter anzupassen:
- loggingUrl
- API-Key (wie in der local-config.php angegeben)
- devicePhase

Das Skript ist hier zu finden [scripts\shelly-scripts\Shelly-Plus1PM-SendToLogger.js](scripts\shelly-scripts\Shelly-Plus1PM-SendToLogger.js)

#### Skript zum aufsummieren der Daten

Um die Echtzeitdaten für den schnelleren Zugriff aufzusummieren wird ein eigenes Skript benötigt. Dies kann man entweder vom Shelly oder über einen eigenen Cronjob starten. 
Bei mir läuft es alle 60 Sekunden auf einem Shelly, z.B. dem EM.

Das Skript ist hier zu finden [scripts\shelly-scripts\Shelly-Pro3EM-Trigger_UnifyData.js](scripts\shelly-scripts\Shelly-Pro3EM-Trigger_UnifyData.js)

#### Autorestart (optional)

Bei mir kam es vor, das die Shelly-Skripte ab und zu stehen geblieben sind. Für diesen Fall kann auf jedem Energiemessgerät zusätzlich ein Autorestart-Skript angelegt werden.

Im oberen Abschnitt sind folgende Parameter anzupassen:
- targetScriptId

Das Skript ist hier zu finden [scripts\shelly-scripts\Shelly-AutoRestart.js](scripts\shelly-scripts\Shelly-AutoRestart.js)

## Passwortschutz (optional)

Um einen Passwortschutz einzurichten, ist es wichtig, das `api`-Verzeichnis **nicht** zu schützen, da die API-Aufrufe sonst nicht funktionieren. Für den API-Schutz ist der API-Key vorgesehen. 

Im `api`-Ordner liegt bereits eine `.htaccess`-Datei, die den Schutz aufhebt mit der Anweisung:

```apache
Require all granted
```

### Einrichtung des Passwortschutzes

Um einen Schutz einzurichten, muss im Hauptverzeichnis des Projekts eine Standard .htaccess- und .htpasswd-Datei abgelegt werden.

Vorgehen:

1. Verwenden Sie einen Online-Generator für .htpasswd, indem Sie nach 'htpasswd Generator' suchen.
2. Hinweis: Der vollständige Pfad zur .htpasswd-Datei muss in der .htaccess-Datei hinterlegt werden. Andernfalls kann ein Internal Server Error auftreten.

Beispiel .htaccess-Datei:

```AuthType Basic
AuthName "Bitte Anmelden"
AuthUserFile /www/htdocs/[pfad]/[zum]/[projekt]/.htpasswd
Require valid-user
```
