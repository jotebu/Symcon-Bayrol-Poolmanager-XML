# Test Plan

This document is the central manual test plan for the Bayrol PoolManager XML IP-Symcon module.

## 1. Environment checks

- IP-Symcon can reach the PM5 web interface.
- Browser test returns XML:

```text
http://<pm5-ip>/cgi-bin/webgui.fcgi?xmlitem=34.4001
```

Expected structure:

```xml
<pm5><item type="34" id="4001" label="pH" unit="pH" value="7.15"/></pm5>
```

## 2. Repository checks

Run locally before pushing:

```bash
php tools/check_php_syntax.php
```

Expected result: all PHP files return `OK`.

## 3. Basic module loading

1. Update module repository in IP-Symcon.
2. Open the module list.
3. Verify version and module name.
4. Create or open a `Bayrol PoolManager XML` instance.
5. Enter PM5 IP address.
6. Press Apply.

Expected result:

- Instance status is active.
- No profile type mismatch warnings.
- No fatal errors.

## 4. Known value polling

Verify the following values are created and updated:

- pH
- Chlor / Brom
- Redox
- Temperature T1
- Temperature T2
- Temperature T3
- Battery
- O2 dosed amount
- Setpoints if enabled
- Alarms if enabled

Expected result:

- Values update according to the configured interval.
- `Online` is true.
- `LastUpdate` changes.
- `LastError` is empty or contains only expected unavailable optional XML addresses.

## 5. XML Explorer

Recommended small test:

- Type: `34`
- Start ID: `4000`
- End ID: `4100`
- Max per run: `150`

Expected result:

- `ExplorerSummary` changes.
- `ExplorerCSV` is populated.
- `ExplorerJSON` is populated.
- `ExplorerLastRun` changes.

## 6. Explorer Snapshot A/B

1. Configure a small relevant range.
2. Store Snapshot A.
3. Change one PM5 function, for example light on/off.
4. Store Snapshot B.
5. Compare snapshots.

Expected result:

- Analog drift is listed as ignored.
- Relevant digital/status changes are listed separately when present.

## 7. Discovery one-shot scan

Recommended first test:

- Type Start: `1`
- Type End: `10`
- ID Start: `0`
- ID End: `100`
- Max per run: `200`

Expected result:

- `DiscoverySummary` changes.
- `DiscoveryJSON` is populated.
- `DiscoveryDB` is updated.
- `DiscoveryReport` lists class counts.

## 8. Discovery scheduler

1. Use a small range first.
2. Start Discovery Scheduler.
3. Verify `Scheduler active` becomes true.
4. Verify cursor type/id advances.
5. Verify progress increases.
6. Stop scheduler.
7. Start scheduler again.

Expected result:

- Scheduler resumes from cursor.
- Timer stops when scan completes.
- Normal value polling still works.

## 9. Regression tests after refactor PRs

For every refactor PR:

- Instance can be opened.
- Apply does not throw.
- pH value reads.
- Normal update works.
- XML Explorer still works.
- No write operation occurs.

## 10. Write safety tests

Until write support is explicitly implemented:

- Any write attempt must throw an exception.
- No HTTP write request may be sent.
- Discovery and Learning workflows must remain read-only.

## 11. Known optional environment issue

If an unrelated Discovery Configurator instance reports:

```text
PDO SQLite ist nicht verfuegbar
```

this is an environment limitation of that separate SQLite-based component and not required for the XML reader runtime.
