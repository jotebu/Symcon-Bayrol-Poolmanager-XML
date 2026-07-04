# Architecture

## Current runtime

`BayrolPoolmanagerXML/module.php` is the IP-Symcon module entrypoint. It currently contains the complete working runtime so existing installations do not break during the refactor.

## Target layout

The codebase is being split into small components under `BayrolPoolmanagerXML/lib/`:

```text
BayrolPoolmanagerXML/
  module.php                 IP-Symcon entrypoint and orchestration
  form.json                  IP-Symcon configuration UI
  module.json                IP-Symcon module metadata
  lib/
    XmlClient.php            HTTP/XML access to the PM5
    DiscoveryClassifier.php  XML object classification
    DiscoveryDatabase.php    first_seen/last_seen/scan_count database handling
    SnapshotComparator.php   A/B diff and drift filtering
    DiscoveryExporter.php    CSV/JSON/PHP/Markdown/HTML export helpers
    DiscoverySchedulerState.php cursor/progress/resume helper
```

## Refactor rule

The running module must stay stable. Therefore the refactor is done in two stages:

1. Add tested helper classes in `lib/`.
2. Move one responsibility at a time out of `module.php` and verify in IP-Symcon.

This avoids replacing a working monolithic module with an untested rewrite.

## Safety model

- Default mode is read-only.
- Write toggles are configuration-only until the write URL format is validated.
- Any future write action must require global write enablement plus per-value enablement.
- After every write, the value must be read back and verified.

## Versioning

The repository uses semantic-style versions:

- `MAJOR`: breaking module structure changes.
- `MINOR`: new user-visible features.
- `PATCH`: bug fixes and non-breaking refactors.

The authoritative visible version for IP-Symcon is `library.json`.
