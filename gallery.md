# Projektname
Energie-Visualisierung für zu Hause, Beschreibung siehe hier [readme.md](./README.md).

# Echtzeitansicht

![Echtzeit-Ansicht](./images/gallery/realtime-overview.png "Echtzeit-Ansicht")

Zeigt die in Echtzeit (z.B. alle 2 Sekunden) erfassten Energiewerte. Ermöglicht die Analyse der Werte durch 2 einstellbare Watt-Linie.


# Stundenübersicht

![Stundenübersicht](./images/gallery/hours-overview.png "Stundenübersicht")

Zeigt den Verbrauch in einer Stundenübersicht. Aus Performancegründen werden aufsummierte Werte aus den Echtzeitdaten verwendet.
Es können 2 Zeitabschnitte dargestellt werden.

# Tagesübersicht

![Tagesübersicht](./images/gallery/days-overview.png "Tagesübersicht")

Zeigt den Verbrauch in einer Tagesübersicht. Aus Performancegründen werden aufsummierte Werte aus den Echtzeitdaten verwendet.
Es können Tage von 2 unterschiedlichen Monaten dargestellt werden.

# Monatsübersicht

![Monatsübersicht](./images/gallery/months-overview.png "Monatsübersicht")

Zeigt wie die Daten wie in den anderen Ansichten auch allerdings in einer Monatsübersicht
Es können die Monate von 2 unterschiedlichen Jahren dargestellt werden.


# Eigene Energiewerte

![Eigene Energiewerte](./images/gallery/own-energy-values.png "Eigene Energiewerte")

Hier können eigene Energiewerte für einen Tag oder einen Monat eingegeben werden.

# Eigene Strompreisdaten

![Eigene Preiswerte](./images/gallery/own-price-values.png "Eigene Preiswerte")

Hier können eigene Strompreisdaten für einen Zeitraum von minimal einen Tag über mehrere Monate eingegeben werden. Es sind Einkaufs- und Einspeise (Verkaufspreise) möglich. 

# Status der fehlenden Energiewerte

![Status der Energiewerte](./images/gallery/status-energy-values.png "Status der Energiewerte")

Es wird ein Status der fehlenden Energiewerte angezeigt. Bei manueller Eingabe können diese nachgepflegt werden. Bei Echtzeitdaten kann es helfen diese nachzuberechnen, falls der Cronjob für die Berechnung ausgefallen ist. Fehlen Echtzeitdaten, kann leider nichts gemacht werden.
