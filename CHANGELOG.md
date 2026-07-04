# Changelog

## 2.1.0 - Discovery Architecture

### Added
- Structured repository versioning with `VERSION`, `CHANGELOG.md` and release documentation.
- Discovery Engine runtime in the IP-Symcon module.
- Discovery scheduler with cursor/resume state.
- Discovery database JSON stored in the Symcon object tree.
- Snapshot A/B comparison for Explorer and Discovery scans.
- Initial classification model for PM5 XML objects.
- CSV/JSON/PHP definition export foundations.
- Modular source layout under `BayrolPoolmanagerXML/lib/` for the next refactor stage.

### Notes
- The installed module remains read-only by default.
- Write actions are still intentionally blocked until the PM5 write URL format has been validated on real hardware.
- The current runtime still contains legacy monolithic code in `module.php`; the new `lib/` files define the target structure for the next safe refactor without breaking the working instance.

## 0.1.0 - Initial XML reader

### Added
- Basic XML polling for known PM5 values.
- Known measurement, setpoint and alarm variables.
- Basic IP/timeout/interval configuration.
