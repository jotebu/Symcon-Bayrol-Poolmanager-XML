# Contributing

Thanks for helping improve the Bayrol PoolManager XML module for IP-Symcon.

## Development principles

This module is used against real PoolManager 5 installations. Changes must therefore be small, reviewable and testable.

Core rules:

- Keep `main` usable.
- Work on feature branches.
- Change only one responsibility per PR.
- Do not combine refactor and feature work in the same PR.
- Keep the module read-only unless a PR is explicitly about the write framework.
- Do not send HTTP write requests to the PM5 until the write URL format is validated on real hardware.

## Branch naming

Recommended branch names:

```text
feature/refactor-2.1.0
feature/discovery-classifier
feature/learning-assistant
fix/profile-type-mismatch
fix/xml-parser
```

## Commit style

Use short imperative commit messages:

```text
Extract XML access into XmlClient
Fix integer profile for Discovery progress
Add Discovery history component
```

## Required checks before commit

Run:

```bash
php tools/check_php_syntax.php
```

The command must pass before a commit is pushed.

## Required manual IP-Symcon checks

For runtime changes, test at least:

1. Module repository updates successfully in IP-Symcon.
2. Instance can be created/opened.
3. Instance status becomes active.
4. pH value is read.
5. Chlor/Redox/temperature values are read.
6. No unexpected write operation is triggered.

For Discovery-related changes, additionally test:

1. XML Explorer small scan, e.g. type `34`, IDs `4000-4100`.
2. Discovery small scan, e.g. types `1-10`, IDs `0-100`, limit `200`.
3. Snapshot A/B comparison.

## Pull request expectations

Each PR should contain:

- Summary.
- Scope.
- Risk level.
- Test steps performed.
- Explicit note if no IP-Symcon runtime test was performed.

## Coding style

- PHP code should stay compatible with IP-Symcon environments.
- Avoid unnecessary PHP features that reduce compatibility.
- Prefer small classes with one responsibility.
- Avoid large rewrites of `module.php`.
- Move one responsibility at a time into `BayrolPoolmanagerXML/lib/`.

## Safety model for write support

The module is read-only by default.

Future write support must require all of the following:

1. Global write enablement.
2. Per-variable write enablement.
3. Explicit write plan.
4. Readback verification.
5. Logging.
6. No implicit write during Discovery or Learning workflows.
