# Testplan 2.1.0

## Preconditions

- PoolManager web UI is reachable from the IP-Symcon host.
- A browser request to `/cgi-bin/webgui.fcgi?xmlitem=34.4001` returns a PM5 XML item for pH.

## Basic module test

1. Update module repository in IP-Symcon.
2. Open the PM5 instance and press Apply.
3. Verify instance status is active.
4. Verify values are created and updated: pH, Chlor/Brom, Redox, T1/T2/T3, Battery, O2 dosed amount.

## XML Explorer test

1. Set Explorer Type = `34`.
2. Set Start ID = `4000`.
3. Set End ID = `4100`.
4. Run XML Explorer.
5. Verify scan summary, CSV and JSON are populated.

## Snapshot comparison test

1. Configure a narrow scan range around suspected output/status IDs.
2. Store Snapshot A.
3. Change one function on the PM5, for example light on/off.
4. Store Snapshot B.
5. Compare A/B.
6. Verify analog drift is listed separately from relevant status changes.

## Discovery one-shot test

1. Set Type Start = `1`.
2. Set Type End = `10`.
3. Set ID Start = `0`.
4. Set ID End = `100`.
5. Set Max Per Run = `200`.
6. Run Discovery.
7. Verify summary, JSON, database and report are populated.

## Discovery scheduler test

1. Use small ranges first.
2. Start Discovery Scheduler.
3. Verify Scheduler active = true.
4. Verify cursor type/id advances.
5. Verify progress increases.
6. Stop Discovery Scheduler.
7. Start Scheduler again.
8. Verify it resumes from cursor.

## Regression checks

- Normal cyclic value polling must continue after Discovery runs.
- Discovery must not trigger any write operation.
- Write attempts must throw an exception until write support is explicitly implemented.
