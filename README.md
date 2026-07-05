# Symcon-Bayrol-Poolmanager-XML

IP-Symcon Modul fuer den BAYROL PoolManager 5 / Analyt XML-Zugriff.

## Version

Aktueller stabiler Stand: **2.1.0**

Die sichtbare IP-Symcon-Version steht in `library.json`. Zusaetzlich gibt es eine `VERSION`-Datei und `CHANGELOG.md`.

## Status

Das Modul liest regulaer die bekannten PM5-Messwerte, Sollwerte, Alarmgrenzen und Alarmzustaende aus. Zusaetzlich enthaelt es eine Discovery Engine, mit der bislang unbekannte XML-Adressen des PM5 systematisch gesucht und klassifiziert werden koennen.

Version 2.1.0 wurde gegen einen realen BAYROL PoolManager 5 getestet:

- Instanz aktiv
- bekannte Werte werden gelesen
- XML Explorer funktioniert
- Discovery Scan funktioniert
- Discovery Scheduler mit Resume funktioniert

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
- Strukturierte Komponenten unter `BayrolPoolmanagerXML/lib/`

## Repository-Struktur

```text
library.json
VERSION
CHANGELOG.md
CONTRIBUTING.md
ROADMAP.md
docs/
  ARCHITECTURE.md
  REFACTOR_PLAN_2.1.0.md
  TESTPLAN.md
  TESTPLAN_2.1.0.md
tools/
  check_php_syntax.php
BayrolPoolmanagerXML/
  module.json
  form.json
  module.php
  lib/
    ModuleIntegration.php
    XmlClient.php
    DiscoveryClassifier.php
    DiscoveryDatabase.php
    DiscoveryExporter.php
    DiscoveryHistory.php
    DiscoverySchedulerState.php
    FirmwareProfile.php
    LearningAssistant.php
    SnapshotComparator.php
    VariableGenerator.php
    WriteManager.php
```

`module.php` bleibt der IP-Symcon-Einstiegspunkt. Die fachliche Logik wird schrittweise in Komponenten unter `lib/` ausgelagert.

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
5. Discovery Engine mit bekannt gueltigem Bereich testen, z. B. Typ 34, ID 4000 bis 4100.
6. Discovery Scheduler mit kleinem Bereich testen, z. B. Typ 34, ID 4078 bis 4097, Limit 5.
7. Danach schrittweise erweitern.

Weitere Details stehen in `docs/TESTPLAN.md`.

## Sicherheitshinweis

Discovery-Scans erzeugen viele HTTP-Anfragen an den PoolManager. Deshalb sollten grosse Scans nur mit Scheduler, Limit und Pause ausgefuehrt werden. Die voreingestellten Sicherheitslimits sollen verhindern, dass der PM5 zu stark belastet wird.

## Entwicklung

Bitte `CONTRIBUTING.md`, `ROADMAP.md` und `docs/REFACTOR_PLAN_2.1.0.md` beachten.

Vor Commits lokal ausfuehren:

```bash
php tools/check_php_syntax.php
```
