# WP Cloud Station — Agent Guide

> **⚠️ Not recommended for production.** This is an experimental dashboard for demonstrating WP Cloud API integration.

WP Cloud Station is a WordPress plugin + block-based theme that allows management of WP Cloud sites from any WordPress installation. It provides a `wpcloud_site` custom post type, Gutenberg blocks, REST API endpoints, and a WP-CLI interface for interacting with the WP Cloud API.

---

## Repository Structure

```
wpcloud-station/
├── plugin/               # WordPress plugin (PHP + JS)
├── theme/                # Block-based FSE theme (wpcloud-station)
├── theme-pico/           # Minimal alternate block theme
├── wp-env-mu-plugins/    # MU-plugins loaded only in wp-env (not distributed)
├── bin/                  # Build, release, and packaging scripts (Node/Bash)
├── .wp-env.json          # wp-env local dev config
├── docker-compose.yml    # Docker local dev config
├── package.json          # Root npm (theme CSS build + release)
├── composer.json         # Root Composer (dev: PHPCS/WPCS)
└── .github/workflows/    # CI (run-tests.yml) + CD (release-build.yaml)
```

### Plugin (`plugin/`)

```
plugin/
├── wpcloud-station.php   # Plugin header, constants, requires init.php
├── init.php              # Bootstrap: registers hooks, loads all submodules
├── admin/                # WP Admin settings page + assets
├── assets/js/            # Frontend JS (src/ -> build/)
├── blocks/               # Gutenberg blocks (src/ -> build/)
│   └── src/
│       ├── components/   # Shared React components
│       ├── controls/     # Block controls
│       ├── hooks/        # Custom React hooks
│       └── {block-name}/ # One directory per block
├── cli/                  # WP-CLI command classes (loaded only when WP_CLI=true)
├── controllers/          # REST API controllers (domains, sites, webhooks, metrics)
├── custom-post-types/    # wpcloud_site CPT registration
├── hosting/              # Optional MU-plugins for Station sites hosted on WP Cloud
├── includes/             # Core PHP classes (API client, models, etc.)
├── patterns/             # Block patterns (JSON)
├── tests/                # PHPUnit tests + JS tests
│   └── js/               # Jest tests for blocks and components
├── views/                # PHP templates
├── package.json          # Plugin npm (blocks/frontend/admin JS build)
├── webpack.config.js     # Extends @wordpress/scripts; adds @wpcloud alias
├── jest.config.js        # Jest config
└── phpunit.xml.dist      # PHPUnit config
```

### Key Constants (defined in `plugin/wpcloud-station.php`)

| Constant | Value |
|---|---|
| `WPCLOUD_CAN_MANAGE_SITES` | `'wpcloud_manage_sites'` (capability) |
| `WPCLOUD_CATEGORY_PRIVATE` | `'wpcloud_private'` |
| `WPCLOUD_CATEGORY_CORE` | `'wpcloud_core_pages'` |
| `WP_STATION_CLIENT_ID` | `'61'` |
| `DEFAULT_PHP_VERSION` | `'8.2'` |

---

## Local Development

### Option 1: wp-env (recommended)

Requires: [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) and Docker.

```bash
wp-env start
```

- WordPress with PHP 8.1
- Plugin mapped to `wp-content/plugins/wpcloud-station-plugin`
- Both themes mapped to `wp-content/themes/`
- `wp-env-mu-plugins/` mapped to `wp-content/mu-plugins/`
- Debug flags enabled: `WP_DEBUG`, `WP_DEBUG_LOG`, `SCRIPT_DEBUG`

After starting, add your WP Cloud API key via the settings page: `/wp-admin/admin.php?page=wpcloud_admin_settings`.

### Option 2: Docker

```bash
cp docker-compose.yml-example docker-compose.yml
# Edit docker-compose.yml and set WP_CLOUD_API_KEY
docker compose up
```

- WordPress on port **8282**
- phpMyAdmin on port **8383**

### Plugin Configuration

Navigate to `/wp-admin/admin.php?page=wpcloud_admin_settings` and set:
- **Client Name** – your WP Cloud client identifier
- **API Key** – your WP Cloud API key
- **Default Domain** – default primary domain for new sites

Your server IP must be whitelisted by your WP Cloud representative.

---

## Building

### Install Dependencies

```bash
# Root (theme CSS toolchain)
npm install

# Plugin (blocks, frontend, admin JS)
cd plugin && npm install

# PHP dev tools (PHPCS)
composer install
```

Node **>=20.10.0** and npm **>=10.2.3** are required.

### Plugin JavaScript

Run from `plugin/`:

```bash
# Build everything (blocks + frontend + admin JS)
npm run build

# Development mode (build + watch all)
npm run start

# Build individual targets
npm run build:blocks    # Gutenberg blocks -> blocks/build/
npm run build:frontend  # Frontend JS -> assets/js/build/
npm run build:admin     # Admin JS -> admin/assets/js/build/

# Watch individual targets
npm run start:blocks
npm run start:frontend
npm run start:admin
```

The webpack config uses `@wpcloud` as an alias for `plugin/blocks/src/`.

### Theme CSS

Run from the project root:

```bash
# Build CSS for all themes
npm run build:theme-css

# Build individual theme
npm run build:theme-css:theme
npm run build:theme-css:theme-pico

# Watch for changes
npm run watch:theme-css
npm run watch:theme-css:theme
npm run watch:theme-css:theme-pico
```

Block CSS source lives in `{theme}/assets/blocks/src/*.css`. The build script processes each file with PostCSS (`postcss-nested` + `postcss-expand-selectors`) and writes the result directly into `theme.json` under `styles.blocks[blockName].css`. **Do not edit `theme.json` CSS fields by hand** — edit the source CSS files and rebuild.

CSS uses `&` as the root block selector (expanded by WordPress to the block's CSS class). Nesting is supported sass-style. Each rule must have a single selector (the build tool handles expansion automatically).

---

## Testing

### PHP Tests

```bash
# Via npm (uses wp-env — recommended)
npm run test:php

# With filter
npm run test:php -- SomeTestClass

# With verbose output
npm run test:php -- -v

# Directly via PHPUnit (requires WordPress test suite installed)
cd plugin
vendor/bin/phpunit
```

PHP tests use PHPUnit. Bootstrap at `plugin/tests/bootstrap.php` loads the WordPress test library and mocks API credentials. Set `WPCLOUD_TESTING=true` is handled automatically.

To install the WordPress test suite manually (for CI or direct PHPUnit runs):
```bash
plugin/bin/install-wp-tests.sh <db-name> <db-user> <db-pass> <db-host> <wp-version>
```

To enable verbose PHP test logging, create the file `plugin/tests/verbose_logging_enabled`.

### JavaScript Tests

Run from `plugin/`:

```bash
npm run test
```

Uses Jest with `@testing-library/react`. Test files follow the pattern `tests/js/**/*.test.js`. Module aliases (`@wordpress/*`, `@wpcloud/*`) are mocked in `tests/js/mocks/`.

### Linting

```bash
# Lint only changed files (JS + CSS, relative to base branch)
npm run lint:changed

# Lint JS
cd plugin && npm run lint:js

# Lint CSS
cd plugin && npm run lint:css

# PHP CodeSniffer (WordPress coding standards)
cd plugin && vendor/bin/phpcs
```

---

## Architecture

### Data Model

The central entity is the **`wpcloud_site`** custom post type. Each managed WP Cloud site is stored as a CPT post. The `WPCloud_Site` PHP class (`plugin/includes/class-wpcloud-site.php`) is the primary model.

### API Layer

- `WPCloud_API_Client` (`plugin/includes/class-wpcloud-api-client.php`) — HTTP client for the WP Cloud API with 60-second cache TTL.
- `WPCloud_API_Request` / `interface-wpcloud-api-request.php` — Request abstraction (allows mocking in tests).
- Functional helpers in `plugin/includes/wpcloud-client.php` (prefixed `wpcloud_client_*`).

### REST API

Four controllers registered at `rest_api_init`:
- `WPCloud_Sites_Controller` — site management endpoints
- `WPCloud_Domains_Controller` — domain management endpoints
- `WPCloud_Webhook_Controller` — incoming webhook proxy
- `WPCloud_Metrics_Controller` — metrics endpoints

### Gutenberg Blocks

All blocks are in `plugin/blocks/src/{block-name}/`. Each block directory contains:
- `block.json` — block metadata
- `index.js` — editor JS (React)
- `index.php` (optional) — server-side render

Shared React components are in `plugin/blocks/src/components/`. Import them using the `@wpcloud` alias:
```js
import MyComponent from '@wpcloud/components/my-component';
```

### WP-CLI

CLI commands are in `plugin/cli/` and loaded only when `WP_CLI` is defined. Commands cover: sites, domains, SSH users, metrics, patterns, and station setup.

### Hosting MU-plugins

`plugin/hosting/` contains optional MU-plugins for when the Station dashboard itself runs on WP Cloud. These are **not required** for normal plugin operation.

---

## Code Conventions

### PHP

- **Prefix**: `wpcloud_` (functions), `WPCLOUD_` (constants), `WPCloud_` (classes)
- **Standard**: WordPress Coding Standards (enforced via PHPCS with `.phpcs.xml.dist`)
- **`declare(strict_types=1)`** at the top of all PHP files
- **Logging helpers**: `wpcloud_l()` (log), `wpcloud_le()` (error), `wpcloud_lo()` (object dump) — suppressed when `WP_DEBUG` is off or `WPCLOUD_TESTING` is true
- **PHP compatibility**: 8.1+ (CI target), 8.2 is default for new sites

### JavaScript

- **Toolchain**: `@wordpress/scripts` (webpack + Babel + ESLint + Prettier)
- **Framework**: React via `@wordpress/element`
- **Module alias**: `@wpcloud` → `plugin/blocks/src/`
- **Formatting**: `npm run format` via `wp-scripts format`

### Git

- Main branch: **`trunk`**
- PRs target `trunk`
- CI runs lint + PHP/JS tests on all PRs and pushes to `trunk`

---

## Release Process

Releases are automated via GitHub Actions (`release-build.yaml`). When a GitHub Release is created:

1. Theme CSS is built
2. Plugin JS is built
3. `plugin`, `theme`, and `theme-pico` are packaged as `.zip` files (respecting `.distignore`)
4. Zips are uploaded as release assets:
	- `wpcloud-station-plugin.zip`
	- `wpcloud-station-theme.zip`
	- `wpcloud-station-theme-pico.zip`

To manually package distributable zips:
```bash
npm run package          # packages all three
bin/package.sh plugin    # plugin only
bin/package.sh theme     # main theme only
bin/package.sh theme-pico
```

To bump versions and cut a release:
```bash
npm run version          # bump version numbers
npm run cut              # full automated release (bumps, PRs, waits for merge, releases)
```

---

## CI Overview

`.github/workflows/run-tests.yml` runs on all PRs and pushes to `trunk`:

| Job | What it does |
|---|---|
| `validate` | Validates `composer.json` |
| `lint-php` | PHPCS on changed PHP files |
| `lint-js` | `wp-scripts lint-js` + `lint-style` on changed files |
| `test-php` | Installs WP test suite, runs PHPUnit |
| `test-js` | Runs Jest if JS test files exist |

PHP tests run against MySQL 5.7 with WordPress latest. Node 20.11 is used for JS jobs.
