# HomeServer Software

Optional software modules for an existing Nextcloud 34 installation, independent
of the host, VPN and storage implementation. Project code is [MIT](LICENSE).
Infrastructure lives in [HomeServer](https://github.com/Fogbose/HomeServer).

## Modules

- **Maison (`homehub` 0.2.0)**: authenticated French application launcher with
  search and categories. Only enabled applications available to the current user
  appear. Links stay in the same browsing context and use installed app icons.
- **App catalogue**: twelve independent App Store selections, including Talk,
  Cookbook, Cospend and Passwords. No upstream application source is bundled.
- **Client kit**: offline links and a setup guide for Windows, Android, iOS and
  Linux. No installer, credentials or automatic account provisioning.

Maison is a local, unsigned extension, **not an App Store release**. Its current
compatibility declaration targets Nextcloud 34 / PHP 8.3+. The interface is French.
It does not add task scheduling, notifications, a mobile shell or authentication
sharing between separate native apps. Native Talk is recommended for calls.

## Install Maison on an existing test instance first

Back up database and the complete Nextcloud files/configuration together. Copy
the `homehub` directory into the writable Nextcloud `custom_apps` directory as
`custom_apps/homehub`, readable by the web user. In the Nextcloud container/root:

```sh
php occ app:enable homehub
```

Run as the web user (usually `www-data`), not root. For the infrastructure sibling:

```sh
docker compose cp ../HomeServer-Software/homehub app:/var/www/html/custom_apps/homehub
docker compose exec -T -u root app chown -R www-data:www-data /var/www/html/custom_apps/homehub
docker compose exec -T -u www-data app php occ app:enable homehub
```

Execute these from the infrastructure checkout with the source path adjusted to
the software checkout; do not overwrite an existing app without an upgrade plan.
Open `/index.php/apps/homehub/`. Making it the default landing page is optional:
record the old `defaultapp` value before setting it to `homehub` with `occ`.
Existing installations must use a targeted application upgrade, not a general
`occ upgrade` that may also update unrelated pending applications.

Rollback: restore the recorded default landing page, then `occ app:disable homehub`.
This app has no database migrations or stored user data. Do not delete Nextcloud
volumes. Nextcloud remains responsible for access control and sessions.

## Select upstream applications

Read [the catalogue](applications/catalogue.json). `observed_version` records the
2026-11-13 deployment baseline, not a lockfile or a latest-security guarantee.
Use Nextcloud's App Store administration to install compatible signed versions
individually. Each app retains its own license and update lifecycle. No bulk
installer or forced compatibility bypass is provided.

Talk needs a separate TURN/HPB infrastructure for reliable calling; installing
`spreed` alone does not provision those services. See the sibling's
[Talk architecture](https://github.com/Fogbose/HomeServer/blob/main/docs/talk.md).
Memories media binaries likewise belong to the infrastructure layer.

## Clients and development

Read [the four-platform guide](docs/clients.md). Generate a private link kit:

```sh
python3 tools/build_client_kit.py --server https://cloud.example.org --output client-kit
python3 -m unittest discover -s tests -v
python3 tests/check_links.py
php tests/controller.php
```

The kit refuses to replace existing output and stores no password. Keep generated
personal links private. PHP tests exercise permission filtering and route choices
using doubles; they do not replace integration testing against Nextcloud.
CI also lints PHP and JavaScript. See [validation](docs/validation.md),
[contribution rules](CONTRIBUTING.md) and [security](SECURITY.md).
