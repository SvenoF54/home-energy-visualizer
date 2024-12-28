# Projektname
Energie-Visualisierung für zu Hause

## Beschreibung

Dieses Projekt nutzt Shelly-Geräte und benutzerdefinierte Skripte, um Energiedaten zu erfassen und an eine API zu senden. Die Daten werden in einer Datenbank gespeichert und in Form von Diagrammen und Tabellen visualisiert. Die Verwendung von Shelly ist optional – alternativ können Energiedaten manuell eingegeben werden.

## Lizenz

Dieses Projekt ist unter der [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html) lizenziert. Sie dürfen es verwenden, modifizieren und weitergeben, solange alle Kopien und abgeleiteten Werke ebenfalls unter dieser Lizenz stehen.

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

Dieses Projekt verwendet Shelly-Geräte, insbesondere die Modelle **Pro3EM**, **Plus1PM** und **Mini1PMG3**, in Kombination mit benutzerdefinierten Skripten, die speziell entwickelt wurden, um **Energiedaten auszulesen und an eine API zu übermitteln**. Diese Skripte ermöglichen es, die erfassten Daten in einer Datenbank zu speichern und in Form von **Diagrammen** und **aufaddierten Tabellen** darzustellen.

### Wichtiger Hinweis zur Installation

Gemäß den gesetzlichen Bestimmungen des jeweiligen Landes dürfen die genannten **Shelly-Geräte** (Pro3EM, Plus1PM, Mini1PMG3) **nur von einem qualifizierten Elektriker** installiert werden. Diese Geräte sind für die Messung und Steuerung von Energieverbrauch und -produktion konzipiert, und die Installation erfordert entsprechendes Fachwissen, um sicherzustellen, dass die Geräte korrekt und sicher betrieben werden.

Bitte beachten Sie, dass die Nutzung dieses Systems und der damit verbundenen Skripte voraussetzt, dass die Installation der Shelly-Geräte fachgerecht durchgeführt wurde. Stellen Sie sicher, dass alle elektrischen Arbeiten von einem **zertifizierten Elektriker** durchgeführt werden, um sowohl die Sicherheit als auch die Einhaltung der gesetzlichen Vorschriften zu gewährleisten. Diese Bestimmungen gelten insbesondere in Deutschland, aber auch in anderen Ländern, in denen die Geräte genutzt werden.

### Optionale Nutzung von Shelly

Die Nutzung von Shelly-Skripten ist in dieser App **optional**. Alternativ können Energiewerte auch **manuell eingegeben** werden, ohne Shelly-Geräte zu verwenden. Das manuelle Eingabesystem ermöglicht eine einfache und direkte Erfassung von Energiedaten, wenn keine Shelly-Geräte zur Verfügung stehen oder wenn eine andere Lösung bevorzugt wird.

### Haftungsausschluss

Dieses Projekt stellt die Software zur Verfügung, die mit den Shelly-Geräten arbeitet, übernimmt jedoch **keine Haftung** für die Installation, Nutzung oder eventuelle Schäden, die durch unsachgemäße Installation oder Nutzung der Geräte entstehen könnten. Die Verantwortung für die ordnungsgemäße Installation der Geräte liegt beim Benutzer.

**Wichtiger Hinweis**: Die Geräte und Skripte dürfen nur in Übereinstimmung mit den geltenden gesetzlichen Bestimmungen und Sicherheitsvorgaben genutzt werden.
