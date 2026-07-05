# Release v2.1.0 - Discovery Architecture

## Summary

Version 2.1.0 is the first structured Discovery Architecture release of the Bayrol PoolManager XML module for IP-Symcon.

It keeps the module read-only and focuses on stable PM5 XML reading, XML Explorer, Discovery Engine, Scheduler/Resume and a maintainable component architecture.

## Tested on real PM5 hardware

The following checks were performed against a real BAYROL PoolManager 5:

- Instance can be created/opened.
- Instance becomes active.
- Known values are read again after the XmlClient refactor.
- XML Explorer works with type `34`, IDs `4000-4100`.
- Discovery works with type `34`, IDs `4000-4100`.
- Discovery Scheduler works with type `34`, IDs `4078-4097`, small batch size and resume cursor.

## Key features

- Known value polling for PM5 measurements, setpoints, limits and alarms.
- XML Explorer with manual type/ID ranges.
- Snapshot A/B comparison with analog drift filtering.
- Discovery Engine for systematic PM5 XML scans.
- Discovery database JSON in the Symcon object tree.
- Initial classification of discovered XML objects.
- Discovery Scheduler with cursor/resume state and progress.
- CSV/JSON/PHP definition output foundations.
- Read-only write framework preparation; no HTTP writes are sent.

## Architecture changes

The codebase now has a component layout under `BayrolPoolmanagerXML/lib/`:

- `ModuleIntegration.php`
- `XmlClient.php`
- `DiscoveryClassifier.php`
- `DiscoveryDatabase.php`
- `DiscoveryExporter.php`
- `DiscoveryHistory.php`
- `DiscoverySchedulerState.php`
- `FirmwareProfile.php`
- `LearningAssistant.php`
- `SnapshotComparator.php`
- `VariableGenerator.php`
- `WriteManager.php`

`module.php` remains the IP-Symcon entrypoint.

## Safety notes

- The module is read-only by default.
- Write toggles are only preparation.
- Write requests are intentionally blocked until the PM5 write URL format has been validated.

## Recommended upgrade test

1. Update module repository in IP-Symcon.
2. Open instance and press Apply.
3. Verify instance status is active.
4. Verify pH/Chlor/Redox/Temperature values update.
5. Run XML Explorer type `34`, IDs `4000-4100`.
6. Run Discovery type `34`, IDs `4000-4100`.
7. Optionally run Scheduler with type `34`, IDs `4078-4097`, Max per run `5`.

## Next milestone

Version 2.2.0 will focus on the Learning Assistant runtime and guided discovery workflows.
