# Symcon-Bayrol-Poolmanager-XML

IP-Symcon Modul fuer den BAYROL PoolManager 5 / Analyt XML-Zugriff.

## Version

Aktueller Entwicklungsstand: **2.1.0**

Die sichtbare IP-Symcon-Version steht in `library.json`. Zusaetzlich gibt es eine `VERSION`-Datei und `CHANGELOG.md`.

## Status

Das Modul liest regulaer die bekannten PM5-Messwerte, Sollwerte, Alarmgrenzen und Alarmzustaende aus. Zusaetzlich enthaelt es eine Discovery Engine, mit der bislang unbekannte XML-Adressen des PM5 systematisch gesucht und klassifiziert werden koennen.

## Read-only by default

Das Modul arbeitet standardmaessig rein lesend. Schreibfreigaben sind im Modul vorbereitet, aber die eigentlichen Schreibbefehle sind absichtlich noch gesperrt, bis die PM5-Write-URLs sicher am realen Geraet validiert wurden.

## Hauptfunktionen

- Zyklisches Auslesen bekannter XML-Werte
- Kategorien fuer Messwerte, Sollwerte, Alarmgrenzen, Alarme, XML Explorer, Discovery Engine und Service
- Eigene Variablenprofile fuer pH, mg/l, mV, Grad Celsius, Liter, Volt, Prozent, Minuten, Mikroampere, Milliampere und Leitwert
- Alarmprofil OK / Alarm
- Kommunikationsstatus und letzte Aktualisierung
- XML Explorer fuer manuelle Typ/ID-Scans
- Snapshot A/B Vergleich mit Filterung analoger Messwert-Drift
- Discovery Engine fuer systematische Scans ueber mehrere XML-Typen und ID-Bereiche
- Discovery-Datenbank im JSON-Format
- Klassifizierung gefundener XML-Objekte
- Discovery Scheduler mit Resume-Cursor und Fortschritt
- CSV-/JSON-Ausgabe im Objektbaum
- PHP-Definitionsvorschlag fuer neue Modulobjekte

## Repository-Struktur

```text
library.json
VERSION
CHANGELOG.md
docs/
  ARCHITECTURE.md
  TESTPLAN_2.1.0.md
BayrolPoolmanagerXML/
  module.json
  form.json
  module.php
  lib/
    XmlClient.php
    DiscoveryClassifier.php
    DiscoveryDatabase.php
    SnapshotComparator.php
    DiscoveryExporter.php
    DiscoverySchedulerState.php
```

`module.php` bleibt aktuell der stabile IP-Symcon-Einstiegspunkt. Die Dateien unter `lib/` bilden die Zielstruktur fuer die schrittweise Auslagerung der einzelnen Verantwortlichkeiten.

## Discovery Engine 2.1

Die Discovery Engine kann mehrere XML-Typen und ID-Bereiche systematisch scannen. Jeder Lauf speichert:

- XML-Adresse, z. B. `34.4001`
- Gueltigkeit der XML-Antwort
- Label
- Einheit
- Wert
- Attribute
- Antwortzeit
- Fehlertext
- automatische Klasse
- Konfidenzwert

Die Discovery-Datenbank speichert fuer jedes gefundene XML-Objekt:

- `first_seen`
- `last_seen`
- `scan_count`
- `class`
- `confidence`
- `attributes`

## Automatische Klassifizierung

Die aktuelle Klassifizierung erkennt u. a.:

- `measurement`
- `setpoint`
- `limit`
- `alarm`
- `status`
- `operating_mode`
- `output_status`
- `calibration`
- `numeric_status_or_config`
- `unknown`

## Empfohlener Testablauf

1. Modul in IP-Symcon aktualisieren.
2. Instanz oeffnen und uebernehmen.
3. Pruefen, ob Messwerte weiterhin aktualisiert werden.
4. XML Explorer mit kleinem Bereich testen, z. B. Typ 34, ID 4000 bis 4100.
5. Discovery Engine klein starten, z. B. Typ 1 bis 10, ID 0 bis 100, Limit 200.
6. Danach schrittweise erweitern.

## Sicherheitshinweis

Discovery-Scans erzeugen viele HTTP-Anfragen an den PoolManager. Deshalb sollten grosse Scans nur mit Scheduler, Limit und Pause ausgefuehrt werden. Die voreingestellten Sicherheitslimits sollen verhindern, dass der PM5 zu stark belastet wird.
