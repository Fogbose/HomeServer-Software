# Validation scope

The extracted source is Maison 0.2.0, without private deployment scripts, test
credentials or historical lab state. Python tests cover client link generation,
origin validation and overwrite refusal. PHP controller tests cover user-specific
availability, canonical routes, declared-route fallback and external URL rejection.
Language checks do not establish Nextcloud API compatibility by themselves.

The originating installation was reported functional on Nextcloud 34 by its
operator. That is distinct from a reproducible integration result for this export.
No full clean-install, mobile screenshot, call or notification test is claimed for
this repository's initial CI. Before a deployment, test Maison with the actual
Nextcloud release, a non-admin account, disabled apps, light/dark themes, mobile
navigation and shared data. Upstream apps and clients need their own acceptance.

Local pre-publication checks on 2026-11-13 passed: both Python tests, six local
Markdown links, PHP controller behavior and syntax checks in the existing
Nextcloud-derived PHP image, and JavaScript syntax checking. Gitleaks 8.30.1
found no leaks in the export. These are not real-device integration tests.
