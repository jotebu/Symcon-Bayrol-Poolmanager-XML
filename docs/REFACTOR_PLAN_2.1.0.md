# Refactor Plan 2.1.0

The refactor is intentionally staged. The current `module.php` is working against a real PM5 and must not be replaced by a large untested rewrite.

## PR 1 - Core Safety Net

Goal: prepare the repository for safe refactoring without changing runtime behaviour.

Scope:

- PHP syntax check helper.
- GitHub Actions syntax workflow.
- Central component include file.
- Architecture and test documentation.

Runtime risk: very low.

## PR 2 - XML Client Extraction

Goal: move HTTP/XML access into `BPMXML_XmlClient`.

Scope:

- Replace `FetchRawXml()` with `BPMXML_XmlClient::fetchRaw()`.
- Replace `ParseXmlString()` with `BPMXML_XmlClient::parseXml()`.
- Replace `ReadItem()` internals with `BPMXML_XmlClient::itemAttributes()`.

Regression tests:

- Instance becomes active.
- pH value reads correctly.
- Existing measured values still update.
- Invalid host still produces status 201/202.

## PR 3 - Discovery Component Extraction

Goal: move pure discovery logic out of `module.php`.

Scope:

- `ClassifyDiscoveryEntry()` -> `BPMXML_DiscoveryClassifier`.
- `MergeDiscoveryDB()` -> `BPMXML_DiscoveryDatabase`.
- `BuildDiscoveryReport()` -> `BPMXML_DiscoveryDatabase::report()`.
- CSV/JSON/PHP export helpers -> `BPMXML_DiscoveryExporter`.
- Snapshot compare -> `BPMXML_SnapshotComparator`.

Regression tests:

- XML Explorer scan 34.4000-4100.
- Snapshot A/B comparison.
- Discovery one-shot scan.
- Discovery DB update.

## PR 4 - Scheduler Extraction

Goal: move cursor and progress math into `BPMXML_DiscoverySchedulerState`.

Regression tests:

- Scheduler starts.
- Cursor advances.
- Stop preserves cursor.
- Start resumes.
- Completion stops timer.

## PR 5 - Learning Assistant Runtime

Goal: expose Learning Assistant functions in Symcon.

Scope:

- Session JSON variable.
- Instruction variable.
- Candidate result variable.
- Actions for light, filter pump, heater, solar, eco and Flockmatic.

## PR 6 - Firmware and Variable Generator

Goal: generate profiles and variable proposals from Discovery DB.

Scope:

- Firmware profile snapshot.
- Firmware profile compare.
- Variable proposal JSON.
- PHP ITEMS proposal.

## PR 7 - Write Framework Integration

Goal: wire write infrastructure while keeping HTTP writes disabled.

Scope:

- Global write enable check.
- Per-ident allow list.
- Write plan variable/log.
- Runtime exception before any HTTP write is sent.

## Release Rule

`main` must remain usable. Every PR needs a small Symcon test before merge.
