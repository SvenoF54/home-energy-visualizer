# Projektname
Energie-Visualisierung für zu Hause

![Echtzeit-Ansicht](./images/gallery/realtime-overview.png "Echtzeit-Ansicht")

Zur Bildergallerie [gallery.md](gallery.md)

## Beschreibung

Mit der Energie-Visualisierung ist es möglich Echtzeitdaten oder manuell eingebenene Energiewerte (Verbrauchs- sowie selbst erzeugte Energie) visuell darzustellen. Hierfür gibt es mehrere Ansichten: Echtzeitdaten, Stunden-, Tages-, Monats- und Jahresübersichten. Die Preisberechnung erfolgt mit den für den jeweiligen Zeitabschnitt eingegebenen Preisdaten. So ist es möglich historische Energiedaten mit zu dieser Zeit gültigen Preis abzulesen.

### Zielsetzung

Das Projekt richtet sich an technisch erfahrene Benutzer, die ihren Echtzeit-Energieverbrauch und / oder ihre Echtzeit-Stromerzeugung via PV-Strom / Balkonkraftwerk oder andere Energieerzeuger im Detail untersuchen möchten. Es ist für eine PC-Nutzung optimiert, eine Nutzung auf einem Smartphone ist auch möglich, aber nicht optimal.

So ist es z.B. möglich Stromspitzen in Echtzeit zu erkennen und mit Hilfe von 2 Berechnungslinien zu erkennen, wieviel Strom ober, unterhalb oder zwischen den Energiewerten verbraucht wird. 

**Beispiel 1:** Wieviel Strom verbrauche ich über 800 Watt, um zu erkennen ob ein Balkonkraftwerk für meine Zwecke ausreichend ist, oder eine größer Anlage besser wäre.

**Beispiel 2:** Wieviel Strom wird unterhalb 200 Watt verwendet. Die kann bei einer konstanten Stromeinspeisung z.B. eines Akkus in der Nacht helfen die optimale Einstellung zu finden.

**Beispiel 3:** Wieviel Strom wird oberhalb 800 Watt und unterhalb 1500 Watt verwendet. Hiermit kann ich rausfinden, inwieweit eine Anhebung meiner Erzeugungsgrenze Sinn macht. 

### Installation

Die Installationshinweise sind hier zu finden [install.md](install.md).

### Energiedaten

Als Energiedaten werden folgende bezeichnet:

#### gemessene bzw. manuell eingebenene Daten
- Verbrauchsdaten: Hiermit sind die selbst verbrauchten Daten gemeint. Diese können am eigenen Stromzähler oder aber über einen Energiesensor, wie z.B. die Shelly 3 EM-Sensoren mit Skriptunterstützung in Echtzeit erfasst werden. 
- Erzeugungsdaten: Dies beschreibt selbst erzeugte Energie, wie z.B. aus PV-Anlagen / Balkonkraftwerken.

#### Datenspeicherung

In der Datenbank ist pro Zeitabschnitt 1 Wert für Verbrauchsdaten vorgesehen, also 1 Wert für alle 3 Phasen. Für die Erzeugungsdaten ist je Phase 1 Wert vorgesehen, d.h. es können alle 3 Phasen getrennt gemessen und erfasst werden.

#### berechnete Daten
- Die Berechnung erfolgt in der Genauigkeit, wie die Daten zur Verfügung stehen. D.h. bei Echtzeitdaten im 2 Sekundentakt, werden jeweils die 2 Sekunden des Verbrauchs mit den gleichen 2 Sekunden der Erzeugung verglichen. Der Preis wird ebenfalls mit dem zu dieser Zeit gültigen Preis berechnet.
- Energieersparnis: Ist die gesparte Energie, also der im positiven Bereich liegende Anteil der Verbrauchsdaten abzüglich der erzeugten Daten.
- Netzeinspeisung: Ist die überschüssige erzeugte Energie, welche nicht selbst verbraucht wird.

### Echtzeitdaten

Die Echtzeitdaten und Stundenübersicht ist nur in Verbindung der Echtzeitdaten sinnvoll nutzbar. Gleiches gilt auch für die Tagesansicht, weil man wohl eher nur Monatsdaten manuell eingibt.
Für die Echtzeitdaten gibt es eine API, welche diese entgegennimmt und in die Datenbank speichert. In diesem Projekt liegen Skripte für die Shelly Energiemesssensoren bei. Bitte ungebdingt die Sicherheitshinweise unten beachten, falls Sensoren benutzt werden, welche in die Elektroinstallation eingebaut werden.

Bei mir laufen die Shelly-Skripte im 2 Sekundentakt stabil, bei 1 Sekunden kommt es zu Fehlern und Datenverlusten. Theoretisch sollte auch ein höherer Takt (z.B. 5 Sekunden) möglich sein, ich habe es aber noch nicht ausprobiert.

Generell kommt es aber immer wieder zu kleinen Datenlücken, weil z.B. die WLAN-Verbindung zu den Shellys nicht stabil ist. Aus diesem Grund wird die Anzahl fehlenden Werte teilweise mit angezeigt.

### Aufsummierte Daten und Übersichtsseiten

Die Übersichtsseiten (Stunden, Tag, Monat und Jahr) arbeiten intern mit aufsummierten Echtzeitdaten, um eine schnelle Darstellung zu ermöglichen. Diese werden über einen Cronjob aus den Echtzeitdaten berechnet. Hier fließen dann auch die manuell eingegebenen Daten ein.

### Preisdaten

Es können manuell Tagespreise für verschiedene Zeitabschnitte eingegeben werden. Diese werden dann bei der Darstellung und Preisberechnung berücksichtigt.
Die Architektur erlaubt es theoretisch Preisdaten im Viertelstundentakt zu verwenden, was eine Anbindung an einen dynamischen Stromtarif ermöglicht. Aktuell gibt es keinen Konnektor hierfür, falls jemand diesen entwickeln möchte, kann er sich gerne bei mir melden.

### Manuel eingegebene Daten

Es ist möglich Energiewerte für einen Tag oder einen Monat manuell einzugeben. Diese Daten werden dann auf den Übersichtsseite angezeigt. Die manuelle Eingabe von Tagesdaten ist gedacht um bei inbetriebnahme des Programms innerhalb eines Monats diesen noch manuell nachzupflegen.
Preisdaten werden ebenfalls manuell für mehrere Zeiträume eingegeben.

### Status fehlender Werte

Hier wird ein entsprechender Status angezeigt, um einen Überglick über die Datenabdeckung zu bekommen und ggfls. manuell Daten nachzupflegen. Falls es bei der Berechung von den Echtzeitdaten zu einem Fehler bei der Ausführung des Cronjobs gekommen ist, hat man hier die Möglichkeit den Monat manuell nachberechnen zu lassen. Wurde allerdings keine Echtzeitdaten erfasst, bleibt die Lücke bestehen.

## Lizenz

Dieses Projekt ist unter der [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html) lizenziert. Sie dürfen es verwenden, modifizieren und weitergeben, solange alle Kopien und abgeleiteten Werke ebenfalls unter dieser Lizenz stehen.
Die Lizenz für die verwendeten Bibliotheken ist hier zu finden [third-party-licenses.txt](third-party-licenses.txt).

## Verwendete Fremd-Bibliotheken

Das Projekt nutzt folgende Open-Source-Bibliotheken:

1. **Shelly** - [Shelly Cloud](https://shelly.cloud)
   - **Funktion**: Automatisierung und Steuerung von Geräten durch benutzerdefinierte Skripte.
   - **Verwendete Shelly-Geräte**: 
     - Shelly Pro3EM
     - Shelly Plus1PM
     - Shelly Mini1PMG3

2. **jQuery** - [jQuery](https://jquery.com/)
   - **Funktion**: Ermöglicht interaktive Webanwendungen mit minimalem Aufwand.
   - **Version**: 3.6.0

3. **Bootstrap 5** - [Bootstrap](https://getbootstrap.com/)
   - **Funktion**: CSS-Framework für die Gestaltung responsiver Webseiten.
   - **Version**: 5.3.3

4. **DataTables** - [DataTables](https://datatables.net/)
   - **Funktion**: Erweiterte Tabellenfunktionen wie Sortierung und Pagination.
   - **Version**: 1.12.1

5. **Chart.js** - [Chart.js](https://www.chartjs.org/)
   - **Funktion**: Bibliothek zur Erstellung interaktiver Diagramme.
   - **Version**: 4.4.6

6. **Moment.js** (via Chart.js Adapter) - [Moment.js](https://momentjs.com/)
   - **Funktion**: Datum- und Zeitmanipulation für Diagramme.

7. **Bootstrap Icons** - [Bootstrap Icons](https://icons.getbootstrap.com/)
   - **Funktion**: Sammlung von Icons für die Verwendung in Bootstrap-basierten Webseiten.

## Shelly-Geräte und Skripte

### Verwendung von Shelly-Geräten und -Skripten

Dieses Projekt kann mit Shelly-Geräten verwendet werden, welche Energiedaten messen und diese per Skript versenden können. Hierzu zählen z.B. die Shelly-EM, Shelly-PM sowie der Shelly Plug, in Kombination mit benutzerdefinierten Skripten, die speziell entwickelt wurden, um **Energiedaten auszulesen und an eine API zu übermitteln**. Diese Skripte ermöglichen es, die erfassten Daten in einer Datenbank zu speichern und in Form von **Diagrammen** und **aufaddierten Tabellen** darzustellen.

### Wichtiger Hinweis zur Installation

Gemäß den gesetzlichen Bestimmungen des jeweiligen Landes dürfen die **Shelly-Geräte** welche in in die Elektroinstallation installiert werden müssen **nur von einem qualifizierten Elektriker** installiert werden. Diese Geräte sind für die Messung und Steuerung von Energieverbrauch und -produktion konzipiert, und die Installation erfordert entsprechendes Fachwissen, um sicherzustellen, dass die Geräte korrekt und sicher betrieben werden.

Bitte beachten Sie, dass die Nutzung dieses Systems und der damit verbundenen Skripte voraussetzt, dass die Installation der Shelly-Geräte fachgerecht durchgeführt wurde. Stellen Sie sicher, dass alle elektrischen Arbeiten von einem **zertifizierten Elektriker** durchgeführt werden, um sowohl die Sicherheit als auch die Einhaltung der gesetzlichen Vorschriften zu gewährleisten. Diese Bestimmungen gelten insbesondere in Deutschland, aber auch in anderen Ländern, in denen die Geräte genutzt werden.

### Optionale Nutzung von Shelly

Die Nutzung von Shelly-Skripten ist in dieser App **optional**. Alternativ können Energiewerte auch **manuell eingegeben** werden, ohne Shelly-Geräte zu verwenden. Das manuelle Eingabesystem ermöglicht eine einfache und direkte Erfassung von Energiedaten, wenn keine Shelly-Geräte zur Verfügung stehen oder wenn eine andere Lösung bevorzugt wird.

### Haftungsausschluss

Dieses Projekt stellt die Software zur Verfügung, die mit den Shelly-Geräten arbeitet, übernimmt jedoch **keine Haftung** für die Installation, Nutzung oder eventuelle Schäden, die durch unsachgemäße Installation oder Nutzung der Geräte entstehen könnten. Die Verantwortung für die ordnungsgemäße Installation der Geräte liegt beim Benutzer. 

Ebenso wird keine Haftung für die erfassten Daten, deren Speicherung oder deren Schutz vor unbefugtem Zugriff übernommen. Es obliegt dem Benutzer, geeignete Sicherheitsmaßnahmen zu treffen, um die Integrität und Vertraulichkeit der Daten sicherzustellen.

Darüber hinaus wird keine Verantwortung für etwaige Datenverluste, Abwärtskompatibilitätsprobleme (z. B. nach einem Update) oder andere technische Probleme übernommen. Es wird ausdrücklich empfohlen, regelmäßig ein Backup der Datenbank zu erstellen, um den Verlust wichtiger Informationen zu vermeiden.

**Wichtiger Hinweis**: Die Geräte und Skripte dürfen nur in Übereinstimmung mit den geltenden gesetzlichen Bestimmungen und Sicherheitsvorgaben genutzt werden.
