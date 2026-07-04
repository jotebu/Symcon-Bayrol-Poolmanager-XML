# Roadmap

## Current focus: 2.1.0 Refactor and Discovery Architecture

The project is moving from a monolithic IP-Symcon module toward a maintainable component-based architecture while preserving the working PM5 XML reader.

## Completed

- [x] Basic PM5 XML reader
- [x] Known PM5 value polling
- [x] Measurement variables
- [x] Setpoint and limit variables
- [x] Alarm variables
- [x] XML Explorer
- [x] Explorer Snapshot A/B comparison
- [x] Discovery Engine foundation
- [x] Discovery database foundation
- [x] Discovery scheduler foundation
- [x] Component structure under `BayrolPoolmanagerXML/lib/`
- [x] Version metadata (`library.json`, `module.json`, `VERSION`)
- [x] Changelog
- [x] Architecture documentation
- [x] Test plan for 2.1.0
- [x] Refactor plan
- [x] PHP syntax check tool
- [x] GitHub Actions syntax workflow

## In progress

- [ ] PR 2: XML Client extraction
  - [ ] Include `ModuleIntegration.php` from `module.php`
  - [ ] Move raw HTTP fetch into `BPMXML_XmlClient`
  - [ ] Move XML parsing into `BPMXML_XmlClient`
  - [ ] Move item attribute extraction into `BPMXML_XmlClient`
  - [ ] Keep runtime behaviour unchanged

## Planned for 2.1.x

- [ ] PR 3: Discovery component extraction
  - [ ] Use `BPMXML_DiscoveryClassifier`
  - [ ] Use `BPMXML_DiscoveryDatabase`
  - [ ] Use `BPMXML_DiscoveryExporter`
  - [ ] Use `BPMXML_SnapshotComparator`

- [ ] PR 4: Scheduler extraction
  - [ ] Use `BPMXML_DiscoverySchedulerState`
  - [ ] Improve scheduler progress and resume logic

- [ ] PR 5: Learning Assistant runtime
  - [ ] Learning session variable
  - [ ] Instruction variable
  - [ ] Candidate result variable
  - [ ] Guided workflows for light, filter pump, heater, solar, eco and Flockmatic

- [ ] PR 6: Firmware profiles and variable generator
  - [ ] Firmware profile snapshot
  - [ ] Firmware profile comparison
  - [ ] Variable proposal JSON
  - [ ] PHP ITEMS proposal

- [ ] PR 7: Write framework integration
  - [ ] Global write enablement
  - [ ] Per-ident write allow list
  - [ ] Write plan generation
  - [ ] Write log
  - [ ] Keep actual HTTP writes disabled until validated

## Later milestones

### 2.2.0

- [ ] Learning Assistant GUI finalized
- [ ] Discovery history UI
- [ ] Variable proposal UI
- [ ] Improved classification rules based on real PM5 findings

### 2.3.0

- [ ] Safe write support after PM5 write URLs are validated
- [ ] Readback verification
- [ ] Write rollback strategy where possible
- [ ] Full write audit log

### 2.4.0

- [ ] Firmware profiling
- [ ] Firmware-to-firmware XML comparison
- [ ] Shared PM5 XML reference database

### 3.0.0

- [ ] Full PM5 Discovery Assistant
- [ ] User-guided automatic device mapping
- [ ] Complete generated Symcon variable structure
- [ ] Stable public release candidate

## Future integration ideas

- [ ] MQTT bridge
- [ ] Home Assistant integration notes
- [ ] Node-RED integration notes
- [ ] ioBroker integration notes
- [ ] REST API bridge
- [ ] Optional write support documentation

## Release rule

`main` must remain usable. Every PR needs a small, explicit test plan and should change only one responsibility at a time.
